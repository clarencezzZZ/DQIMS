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
        
        return view('admin.index', compact('categories', 'todayStats'));
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
        
        // Get next inquiry for each category to show in the view
        $nextInquiries = [];
        foreach ($categories as $category) {
            $nextInquiry = $this->getNextInquiryByPriorityForAdmin($category->id);
            if ($nextInquiry) {
                $nextInquiries[$category->id] = $nextInquiry->id;
            }
        }

        return view('admin.inquiries', compact('inquiries', 'inquiriesBySection', 'sections', 'paginatedInquiries', 'categories', 'nextInquiries'));
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

            // If trying to serve a waiting inquiry, check if it's the correct next one
            if ($newStatus == 'serving' && $oldStatus == 'waiting') {
                // Get the next inquiry that should be served according to priority rules
                $nextInquiry = $this->getNextInquiryByPriorityForAdmin($inquiry->category_id);
                
                if ($nextInquiry && $nextInquiry->id != $inquiry->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot serve this inquiry out of order. Next in queue is ' . $nextInquiry->queue_number . ' (' . ucfirst($nextInquiry->priority) . ')'
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
            'section' => 'required|string|max:20',
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
                
        Category::create([
            'section' => $request->section,
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
            'section' => 'required|string|max:20',
            'section_name' => 'required|string|max:100',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:categories,code,' . $category->id,
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
            'lobby' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $category->update([
            'section' => $request->section,
            'section_name' => $request->section_name,
            'code' => $request->code,
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
     * Get next inquiry using priority queuing algorithm for admin interface
     */
    public function getNextInquiryByPriorityForAdmin($categoryId)
    {
        // Get all waiting inquiries in the category, ordered by creation time
        $waitingInquiries = Inquiry::today()
            ->byCategory($categoryId)
            ->waiting()
            ->orderBy('created_at')
            ->get();

        if ($waitingInquiries->isEmpty()) {
            return null;
        }

        // Get the currently serving inquiry (if any) and last completed inquiry
        $currentlyServing = Inquiry::today()
            ->byCategory($categoryId)
            ->where('status', 'serving')
            ->first();
            
        $lastServedInquiry = Inquiry::today()
            ->byCategory($categoryId)
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->first();

        // If someone is currently being served, use their priority type
        // Otherwise, if there's a last served record, use that
        // If starting fresh, we'll serve normal first
        if ($currentlyServing) {
            $lastServedType = $currentlyServing->priority;
        } else {
            $lastServedType = $lastServedInquiry ? $lastServedInquiry->priority : null;
        }

        // Separate priority and normal inquiries
        $priorityInquiries = $waitingInquiries->filter(function ($inquiry) {
            return $inquiry->priority === 'priority';
        });

        $normalInquiries = $waitingInquiries->filter(function ($inquiry) {
            return $inquiry->priority === 'normal';
        });

        // If there are no priority inquiries, return the oldest normal inquiry
        if ($priorityInquiries->isEmpty()) {
            return $normalInquiries->first();
        }

        // If there are no normal inquiries, return the oldest priority inquiry
        if ($normalInquiries->isEmpty()) {
            return $priorityInquiries->first();
        }

        // If starting fresh (no one currently serving and no last served), 
        // serve the oldest normal inquiry first to establish fairness
        if ($lastServedType === null) {
            return $normalInquiries->first();
        }
        
        // If last served was priority and there are normal inquiries available,
        // return the oldest normal inquiry to avoid serving two priority in a row
        if ($lastServedType === 'priority') {
            return $normalInquiries->first();
        }

        // Otherwise (last served was normal), return the oldest priority inquiry (priority first rule)
        return $priorityInquiries->first();
    }
}
