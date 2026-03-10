<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Category;
use App\Models\EventLog;
use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function index()
    {
        $categories = Category::where('is_active', true)->get()->map(function ($category) {
            $hexColor = ltrim($category->color, '#');
            $r = hexdec(substr($hexColor, 0, 2));
            $g = hexdec(substr($hexColor, 2, 2));
            $b = hexdec(substr($hexColor, 4, 2));
            
            // Calculate brightness
            $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;
            
            // Determine contrast color
            $category->contrast_color = $brightness > 128 ? '#000000' : '#FFFFFF';
            
            return $category;
        });
        
        $todayStats = $this->getTodayStats();
        $weeklyData = $this->getWeeklyInquiryData();
        
        return view('admin.index', compact('categories', 'todayStats', 'weeklyData'));
    }

    /**
     * Get weekly inquiry data for chart
     */
    private function getWeeklyInquiryData()
    {
        $data = [];
        $labels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $label = $date->format('M j');
            
            $count = Inquiry::whereDate('created_at', $dateStr)->count();
            
            $data[] = $count;
            $labels[] = $label;
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Get today's statistics
     */
    private function getTodayStats()
    {
        $today = Inquiry::today();
        
        return [
            'total_inquiries' => (clone $today)->count(),
            'waiting' => (clone $today)->waiting()->count(),
            'serving' => (clone $today)->where('status', 'serving')->count(),
            'completed' => (clone $today)->completed()->count(),
            'skipped' => (clone $today)->where('status', 'skipped')->count(),
        ];
    }

    /**
     * Display all inquiries
     */
    public function inquiries(Request $request)
    {
        $query = Inquiry::with(['category', 'servedBy']);

        // Apply filters
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        } else {
            $query->today();
        }

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // For "All" filter, exclude completed inquiries
            $query->where('status', '!=', 'completed');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('guest_name', 'like', "%{$search}%")
                  ->orWhere('queue_number', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $inquiries = $query->oldest()->get(); // Get all results for grouping
        
        // Get categories first for section grouping
        $categories = Category::where('is_active', true)->get();
        
        // Map section acronyms to full names
        $sectionNames = [
            'ACS' => 'AGGREGATE AND CORRECTION',
            'OOSS' => 'ORIGINAL AND OTHER SURVEYS',
            'LES' => 'LAND EVALUATION',
            'SCS' => 'SURVEYS AND CONTROL',
        ];
        
        // Group inquiries by section (not category)
        $inquiriesBySection = $inquiries->groupBy(function($inquiry) use ($sectionNames) {
            $sectionAcronym = $inquiry->category ? $inquiry->category->section : 'Uncategorized';
            return $sectionNames[$sectionAcronym] ?? $sectionAcronym;
        });
        
        // Get section information for display
        $sections = $categories->groupBy('section')->map(function($sectionCategories, $sectionName) use ($sectionNames) {
            return [
                'name' => $sectionNames[$sectionName] ?? $sectionName,
                'acronym' => $sectionName,
                'categories' => $sectionCategories,
                'color' => $sectionCategories->first()->color ?? '#6c757d',
                'count' => $sectionCategories->count()
            ];
        })->sortBy('name');
        
        // Paginate manually for the current view
        $currentPage = $request->get('page', 1);
        $perPage = 20;
        $paginatedInquiries = new \Illuminate\Pagination\LengthAwarePaginator(
            $inquiries->forPage($currentPage, $perPage),
            $inquiries->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'pageName' => 'page']
        );
        
        // Update any 'urgent' priority records to 'priority' to comply with new enum values
        // We need to update all urgent records in the database, not just the paginated ones
        if ($request->filled('date')) {
            Inquiry::whereDate('date', $request->date)->where('priority', 'urgent')->update(['priority' => 'priority']);
        } else {
            Inquiry::today()->where('priority', 'urgent')->update(['priority' => 'priority']);
        }
        
        // Get next inquiry for each SECTION (not category) to show in the view
        $nextInquiries = [];
        $processedSections = [];
        
        // Enhanced debug: count waiting inquiries with details
        $todayStr = now()->toDateString();
        $totalWaiting = Inquiry::whereDate('date', $todayStr)->where('status', 'waiting')->count();
        \Log::info("=== TOTAL WAITING INQUIRIES TODAY: {$totalWaiting} ===");
        
        // Log first 5 waiting inquiries for detailed inspection
        $firstFewWaiting = Inquiry::whereDate('date', $todayStr)
            ->where('status', 'waiting')
            ->orderBy('created_at')
            ->limit(5)
            ->get();
        \Log::info('First few waiting inquiries:', $firstFewWaiting->map(fn($i) => [
            'id' => $i->id,
            'queue_number' => $i->queue_number,
            'guest_name' => $i->guest_name,
            'category_id' => $i->category_id,
            'priority' => $i->priority,
            'created_at' => $i->created_at->toDateTimeString(),
        ])->toArray());
        
        \Log::info('=== Building Next Inquiries Array ===');
        \Log::info('Total Categories: ' . $categories->count());
        \Log::info('Sections to Process: ' . $categories->groupBy('section')->keys()->join(', '));
        
        foreach ($categories as $category) {
            $section = $category->section;
            
            // Only process each section once since all categories in the same section share one queue
            if (!isset($processedSections[$section])) {
                \Log::info("=== Processing Section: {$section} ===");
                \Log::info("  Category ID: {$category->id}, Code: {$category->code}, Name: {$category->name}");
                
                // Direct query to check waiting inquiries in this section with full details
                $sectionWaiting = Inquiry::today()
                    ->join('categories', 'inquiries.category_id', '=', 'categories.id')
                    ->where('categories.section', $section)
                    ->where('inquiries.status', 'waiting')
                    ->select('inquiries.*')
                    ->orderBy('inquiries.created_at')
                    ->get();
                    
                \Log::info("  Waiting inquiries in {$section}: {$sectionWaiting->count()}");
                
                if ($sectionWaiting->isNotEmpty()) {
                    \Log::info("  Details:", $sectionWaiting->map(fn($i) => [
                        'id' => $i->id,
                        'queue_number' => $i->queue_number,
                        'guest_name' => $i->guest_name,
                        'priority' => $i->priority,
                        'created_at' => $i->created_at->toDateTimeString(),
                    ])->toArray());
                }
                
                $nextInquiry = $this->getNextInquiryByPriorityForAdmin($category->id);
                if ($nextInquiry) {
                    // Store by section, not category
                    $nextInquiries[$section] = $nextInquiry->id;
                    \Log::info("  ✅ NEXT Inquiry for {$section}: #{$nextInquiry->queue_number} (ID: {$nextInquiry->id}, Priority: {$nextInquiry->priority})");
                } else {
                    \Log::info("  ❌ No next inquiry for section: {$section}");
                }
                $processedSections[$section] = true;
            }
        }
        
        \Log::info('=== Final Next Inquiries Array ===');
        \Log::info(json_encode($nextInquiries));
        
        // Also pass raw counts to view for debugging
        $debugCounts = [
            'total_waiting' => $totalWaiting,
            'sections_with_next' => count($nextInquiries),
        ];

        return view('admin.inquiries', compact('inquiries', 'inquiriesBySection', 'sections', 'paginatedInquiries', 'categories', 'nextInquiries', 'debugCounts'));
    }

    /**
     * Update inquiry status (AJAX)
     */
    public function updateInquiryStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'inquiry_id' => 'required|exists:inquiries,id',
            'status' => 'required|in:waiting,serving,completed,skipped',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $inquiry = Inquiry::findOrFail($request->inquiry_id);
            $oldStatus = $inquiry->status;
            $newStatus = $request->status;

            // If trying to serve a waiting inquiry, check if it's the correct next one (SECTION-WIDE FIFO)
            if ($newStatus == 'serving' && $oldStatus == 'waiting') {
                // Get the next inquiry that should be served according to section-wide priority rules
                $nextInquiry = $this->getNextInquiryByPriorityForAdmin($inquiry->category_id);
                
                if ($nextInquiry && $nextInquiry->id != $inquiry->id) {
                    // Get section name for better error message
                    $sectionName = $inquiry->category ? $inquiry->category->section : 'Unknown Section';
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot serve this inquiry out of order. In ' . $sectionName . ' section, the next in queue is ' . $nextInquiry->queue_number . ' (' . ucfirst($nextInquiry->priority) . '). First-Come, First-Serve across all service types in this section.'
                    ], 422);
                }
            }

            // Update inquiry status
            $inquiry->status = $newStatus;

            // If starting to serve, record served_by and served_at
            if ($newStatus == 'serving' && $oldStatus != 'serving') {
                $inquiry->served_by = Auth::user()->username;
                $inquiry->served_at = now();
            }

            // If completed, record completed_at
            if ($newStatus == 'completed' && $oldStatus != 'completed') {
                $inquiry->completed_at = now();
            }

            $inquiry->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated from ' . ucfirst($oldStatus) . ' to ' . ucfirst($newStatus),
                'inquiry' => $inquiry->load('category')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show assessment form
     */
    public function createAssessment(Inquiry $inquiry)
    {
        if ($inquiry->assessment) {
            return redirect()->route('admin.assessments.show', $inquiry->assessment)
                ->with('info', 'Assessment already exists for this inquiry.');
        }

        $officers = \App\Models\OfficerOfDay::active()->get();
        $lotaOfficer = User::where('name', 'Mr. Stanley M. Lota')->first();

        return view('admin.assessment-create', compact('inquiry', 'officers', 'lotaOfficer'));
    }

    /**
     * Store assessment
     */
    public function storeAssessment(Request $request, Inquiry $inquiry)
    {
        $validator = Validator::make($request->all(), [
            'fees' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
            'officer_in_charge' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Handle officer incharge selection
        $officerInCharge = $request->officer_in_charge;
        $customOfficerName = null;
        if ($officerInCharge === 'lota') {
            $officerInCharge = 'lota'; // Keep as string identifier for Mr. Stanley M. Lota
        }

        $assessment = Assessment::create([
            'assessment_number' => $this->generateAssessmentNumber(),
            'inquiry_id' => $inquiry->id,
            'queue_number' => $inquiry->queue_number,
            'guest_name' => $inquiry->guest_name,
            'category_id' => $inquiry->category_id,
            'request_type' => $inquiry->purpose ?? 'General Inquiry',
            'fees' => $request->fees,
            'remarks' => $request->remarks,
            'processed_by' => Auth::id(),
            'officer_in_charge' => $officerInCharge,
            'custom_officer_name' => $customOfficerName,
            'assessment_date' => now()->toDateString(),
        ]);

        return redirect()->route('admin.assessments.show', $assessment)
            ->with('success', 'Assessment created successfully.');
    }

    /**
     * Show assessment
     */
    public function showAssessment(Assessment $assessment)
    {
        $assessment->load(['processedBy', 'officerInCharge', 'category']);
        return view('admin.assessment-show', compact('assessment'));
    }

    /**
     * Show the form for editing the specified assessment.
     */
    public function editAssessment(Assessment $assessment)
    {
        $assessment->load(['processedBy', 'officerInCharge', 'category']);
        $officers = \App\Models\OfficerOfDay::active()->get();
        $lotaOfficer = User::where('name', 'Mr. Stanley M. Lota')->first();
        return view('admin.assessment-edit', compact('assessment', 'officers', 'lotaOfficer'));
    }

    /**
     * Update the specified assessment in storage.
     */
    public function updateAssessment(Request $request, Assessment $assessment)
    {
        $validator = Validator::make($request->all(), [
            'guest_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'fees' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
            'officer_in_charge' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Handle officer incharge selection
        $officerInCharge = $request->officer_in_charge;
        $customOfficerName = null;
        if ($officerInCharge === 'other') {
            $customOfficerName = $request->new_officer_name;
            $officerInCharge = null; // Set to null since we're using custom name
        } elseif ($officerInCharge === 'lota') {
            $officerInCharge = 'lota'; // Keep as string identifier for Mr. Stanley M. Lota
        } elseif (is_numeric($officerInCharge)) {
            // Ensure it's stored as integer if it's numeric
            $officerInCharge = (int) $officerInCharge;
        }

        // Build names detail JSON if provided
        $namesDetail = [];
        if ($request->names) {
            foreach ($request->names as $index => $name) {
                if (!empty($name)) {
                    $namesDetail[] = [
                        'name' => $name,
                        'quantity' => $request->quantities[$index] ?? 1,
                        'amount' => $request->amounts[$index] ?? 0,
                    ];
                }
            }
        }

        // Debug: Log the values being updated
        \Log::info('Updating assessment ' . $assessment->id . ': officer_in_charge=' . $officerInCharge . ', custom_officer_name=' . $customOfficerName);
        
        $assessment->update([
            'guest_name' => $request->guest_name,
            'address' => $request->address,
            'fees' => $request->fees,
            'remarks' => $request->remarks,
            'officer_in_charge' => $officerInCharge,
            'custom_officer_name' => $customOfficerName,
            'names_detail' => json_encode($namesDetail),
        ]);

        // Debug: Log the values after update
        $assessment->refresh();
        \Log::info('After update - officer_in_charge=' . $assessment->officer_in_charge . ', custom_officer_name=' . $assessment->custom_officer_name);

        return redirect()->route('admin.assessments.show', $assessment)
            ->with('success', 'Assessment updated successfully.');
    }

    /**
     * Display all assessments
     */
    public function assessments(Request $request)
    {
        $query = Assessment::with(['inquiry', 'category', 'processedBy']);

        if ($request->filled('date_from')) {
            $query->whereDate('assessment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('assessment_date', '<=', $request->date_to);
        }

        if ($request->filled('category')) {
            $query->where('request_type', $request->category);
        }

        $assessments = $query->latest()->paginate(20);
        $categories = Category::where('is_active', true)->get();

        return view('admin.assessments', compact('assessments', 'categories'));
    }

    /**
     * User management
     */
    public function users()
    {
        $users = User::with('assignedCategory')->paginate(20);
        $categories = Category::where('is_active', true)->get();
        
        return view('admin.users', compact('users', 'categories'));
    }

    /**
     * Store new user
     */
    public function storeUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:front_desk,section_staff,admin',
            'assigned_category_id' => 'nullable|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'assigned_category_id' => $request->assigned_category_id,
            'is_active' => true,
        ]);

        return back()->with('success', 'User created successfully.');
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:front_desk,section_staff,admin',
            'assigned_category_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'assigned_category_id' => $request->assigned_category_id,
            'is_active' => $request->is_active ?? false,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => bcrypt($request->password)]);
        }

        return back()->with('success', 'User updated successfully.');
    }

    /**
     * Category management
     */
    public function categories()
    {
        $categories = Category::withCount(['inquiries', 'assignedUsers'])->get();
        return view('admin.categories', compact('categories'));
    }

    /**
     * Store new category
     */
    public function storeCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'section_name' => 'required|string|max:100',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:categories',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
            'lobby' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
                
        // Create category with section_name and keep section in sync for backward compatibility
        Category::create([
            'section' => $request->section_name,
            'section_name' => $request->section_name,
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'lobby' => $request->lobby,
            'is_active' => true,
        ]);

        return back()->with('success', 'Category created successfully.');
    }

    /**
     * Update category
     */
    public function updateCategory(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'section_name' => 'required|string|max:100',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
            'lobby' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        // Update section_name and keep section in sync for backward compatibility
        $category->update([
            'section' => $request->section_name,
            'section_name' => $request->section_name,
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'lobby' => $request->lobby,
            'is_active' => $request->is_active ?? false,
        ]);

        return back()->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroyCategory(Category $category)
    {
        try {
            // Check if category has related inquiries or assessments
            if ($category->inquiries()->count() > 0) {
                return back()->with('error', 'Cannot delete category. It has associated inquiries.');
            }
            
            if ($category->assessments()->count() > 0) {
                return back()->with('error', 'Cannot delete category. It has associated assessments.');
            }
            
            // Handle assigned users - reset their assigned category to null
            $assignedUsers = $category->assignedUsers;
            foreach ($assignedUsers as $user) {
                $user->assigned_category_id = null;
                $user->save();
            }
            
            // Log the deletion event
            EventLog::create([
                'user_id' => Auth::user()->id,
                'action' => 'deleted',
                'category_name' => $category->name,
                'description' => 'Category deleted by ' . Auth::user()->name,
                'old_values' => json_encode($category->toArray()),
                'new_values' => null,
            ]);
            
            // Delete the category
            $category->delete();
            
            return redirect()->route('admin.categories')->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.categories')->with('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }

    /**
     * Store direct assessment (from modal)
     */
    public function storeDirectAssessment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'responsibility_center' => 'required|string|max:50',
            'assessment_date' => 'required|date',
            'guest_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description_type' => 'required|string|max:100',
            'names' => 'nullable|array',
            'names.*' => 'nullable|string|max:255',
            'quantities' => 'nullable|array',
            'quantities.*' => 'nullable|integer|min:1',
            'amounts' => 'nullable|array',
            'amounts.*' => 'nullable|numeric|min:0',
            'fees' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
            'officer_in_charge' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Generate unique assessment number on the server side to prevent duplicates
        $assessmentNumber = $this->generateAssessmentNumber();

        // Handle officer incharge selection
        $officerInCharge = $request->officer_in_charge;
        $customOfficerName = null;
        if ($officerInCharge === 'lota') {
            $officerInCharge = 'lota'; // Keep as string identifier for Mr. Stanley M. Lota
        }

        // Build names detail JSON
        $namesDetail = [];
        if ($request->names) {
            foreach ($request->names as $index => $name) {
                if (!empty($name)) {
                    $namesDetail[] = [
                        'name' => $name,
                        'quantity' => $request->quantities[$index] ?? 1,
                        'amount' => $request->amounts[$index] ?? 0,
                    ];
                }
            }
        }

        $assessment = Assessment::create([
            'assessment_number' => $assessmentNumber,
            'bill_number' => $assessmentNumber, // Keep for backward compatibility
            'responsibility_center' => $request->responsibility_center,
            'inquiry_id' => null, // Direct assessment, no inquiry
            'queue_number' => 'DIRECT-' . time(),
            'guest_name' => $request->guest_name,
            'address' => $request->address,
            'category_id' => null, // Not required for direct assessment
            'reference' => null,
            'request_type' => $request->description_type,
            'names_detail' => json_encode($namesDetail),
            'fees' => $request->fees,
            'remarks' => $request->remarks,
            'processed_by' => Auth::user()->id,
            'officer_in_charge' => $officerInCharge,
            'custom_officer_name' => $customOfficerName,
            'assessment_date' => $request->assessment_date,
        ]);

        return redirect()->route('admin.assessments')
            ->with('success', 'Assessment created successfully.');
    }

    /**
     * Generate unique assessment number
     */
    private function generateAssessmentNumber()
    {
        $year = date('Y');
        $month = date('m');
        
        // Use the sequence model to ensure uniqueness
        return \App\Models\AssessmentSequence::getNextNumber($year, $month);
    }

    /**
     * Remove the specified assessment from storage.
     */
    public function destroyAssessment(Assessment $assessment)
    {
        try {
            // Log the deletion event
            EventLog::create([
                'user_id' => Auth::user()->id,
                'action' => 'deleted',
                'assessment_number' => $assessment->assessment_number,
                'description' => 'Assessment record deleted by ' . Auth::user()->name,
                'old_values' => json_encode($assessment->toArray()),
                'new_values' => null,
            ]);

            // Delete the assessment
            $assessment->delete();

            return redirect()->route('admin.assessments')
                ->with('success', 'Assessment deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.assessments')
                ->with('error', 'Failed to delete assessment: ' . $e->getMessage());
        }
    }

    /**
     * Get the last assessment number for the given year and month
     */
    public function getLastAssessmentNumber($year, $month)
    {
        try {
            // Get the last assessment number for the given year and month
            $lastAssessment = Assessment::whereRaw(
                "DATE_FORMAT(created_at, '%Y-%m') = ?", 
                ["{$year}-{$month}"]
            )
            ->orderBy('assessment_number', 'desc')
            ->first();

            if ($lastAssessment) {
                // Extract the number from the existing format (YYYY-MM-NNNN)
                $parts = explode('-', $lastAssessment->assessment_number);
                if (isset($parts[2]) && is_numeric($parts[2])) {
                    $lastNumber = intval($parts[2]);
                } else {
                    $lastNumber = 0;
                }
            } else {
                $lastNumber = 0;
            }

            return response()->json([
                'last_number' => $lastNumber
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'last_number' => 0
            ]);
        }
    }

    /**
     * Get next inquiry using priority queuing algorithm for admin interface (SECTION-WIDE FIFO)
     * Algorithm: First-Come, First-Serve per section, regardless of service type (category)
     * - Only the earliest waiting queue in that section can be served
     * - Priority alternation: NORMAL → PRIORITY → NORMAL → PRIORITY
     * - All categories within the same section share one unified queue
     * - Each section operates independently
     */
    public function getNextInquiryByPriorityForAdmin($categoryId)
    {
        // Get the category to determine which section we're working with
        $category = Category::find($categoryId);
        if (!$category) {
            \Log::info("  [getNextInquiry] Category not found for ID: {$categoryId}");
            return null;
        }
        
        $section = $category->section;
        \Log::info("  [getNextInquiry] Processing section: {$section}");
        
        // Get ALL waiting inquiries in this SECTION (across all categories), ordered by creation time (FIFO)
        $waitingInquiries = Inquiry::today()
            ->join('categories', 'inquiries.category_id', '=', 'categories.id')
            ->where('categories.section', $section)
            ->where('inquiries.status', 'waiting')
            ->select('inquiries.*')
            ->orderBy('inquiries.created_at')
            ->get();

        if ($waitingInquiries->isEmpty()) {
            \Log::info("  [getNextInquiry] No waiting inquiries in section: {$section}");
            return null;
        }
        
        \Log::info("  [getNextInquiry] Found {$waitingInquiries->count()} waiting inquiries in {$section}");

        // Get the currently serving inquiry in this section (if any) and last completed inquiry in this section
        $currentlyServing = Inquiry::today()
            ->join('categories', 'inquiries.category_id', '=', 'categories.id')
            ->where('categories.section', $section)
            ->where('inquiries.status', 'serving')
            ->select('inquiries.*')
            ->first();
            
        $lastServedInquiry = Inquiry::today()
            ->join('categories', 'inquiries.category_id', '=', 'categories.id')
            ->where('categories.section', $section)
            ->where('inquiries.status', 'completed')
            ->select('inquiries.*')
            ->orderBy('inquiries.completed_at', 'desc')
            ->first();

        // Determine the last served priority type
        if ($currentlyServing) {
            $lastServedType = $currentlyServing->priority;
        } else {
            $lastServedType = $lastServedInquiry ? $lastServedInquiry->priority : null;
        }

        // Separate priority and normal inquiries across the entire section
        $priorityInquiries = $waitingInquiries->filter(function ($inquiry) {
            return $inquiry->priority === 'priority';
        });

        $normalInquiries = $waitingInquiries->filter(function ($inquiry) {
            return $inquiry->priority === 'normal';
        });

        \Log::info("  [getNextInquiry] Priority count: {$priorityInquiries->count()}, Normal count: {$normalInquiries->count()}");
        \Log::info("  [getNextInquiry] Last served type: " . ($lastServedType ?? 'none'));
        \Log::info("  [getNextInquiry] Currently serving: " . ($currentlyServing ? "#{$currentlyServing->queue_number} ({$currentlyServing->priority})" : 'none'));

        // If there are no priority inquiries, return the oldest normal inquiry (first in section queue)
        if ($priorityInquiries->isEmpty()) {
            $result = $normalInquiries->first();
            \Log::info("  [getNextInquiry] Returning oldest NORMAL: #{$result->queue_number} (ID: {$result->id})");
            return $result;
        }

        // If there are no normal inquiries, return the oldest priority inquiry (first in section queue)
        if ($normalInquiries->isEmpty()) {
            $result = $priorityInquiries->first();
            \Log::info("  [getNextInquiry] Returning oldest PRIORITY: #{$result->queue_number} (ID: {$result->id})");
            return $result;
        }

        // If starting fresh (no one currently serving and no last served), 
        // serve the oldest normal inquiry first to establish fairness
        if ($lastServedType === null) {
            $result = $normalInquiries->first();
            \Log::info("  [getNextInquiry] Starting fresh - returning NORMAL: #{$result->queue_number} (ID: {$result->id})");
            return $result;
        }
        
        // If last served was priority and there are normal inquiries available,
        // return the oldest normal inquiry to avoid serving two priority in a row
        if ($lastServedType === 'priority') {
            $result = $normalInquiries->first();
            \Log::info("  [getNextInquiry] Last was PRIORITY - returning NORMAL: #{$result->queue_number} (ID: {$result->id})");
            return $result;
        }

        // Otherwise (last served was normal), return the oldest priority inquiry (priority first rule)
        $result = $priorityInquiries->first();
        \Log::info("  [getNextInquiry] Last was NORMAL - returning PRIORITY: #{$result->queue_number} (ID: {$result->id})");
        return $result;
    }
}
