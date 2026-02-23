<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Category;
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
        $categories = Category::where('is_active', true)->get();
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
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('guest_name', 'like', "%{$search}%")
                  ->orWhere('queue_number', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%");
            });
        }

        $inquiries = $query->oldest()->paginate(20);
        $categories = Category::where('is_active', true)->get();

        return view('admin.inquiries', compact('inquiries', 'categories'));
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

        return view('admin.assessment-create', compact('inquiry'));
    }

    /**
     * Store assessment
     */
    public function storeAssessment(Request $request, Inquiry $inquiry)
    {
        $validator = Validator::make($request->all(), [
            'fees' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
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
        return view('admin.assessment-show', compact('assessment'));
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
            $query->byCategory($request->category);
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
            'code' => 'required|string|max:10|unique:categories',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Category::create($request->all());

        return back()->with('success', 'Category created successfully.');
    }

    /**
     * Update category
     */
    public function updateCategory(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:10|unique:categories,code,' . $category->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $category->update([
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'is_active' => $request->is_active ?? false,
        ]);

        return back()->with('success', 'Category updated successfully.');
    }

    /**
     * Store direct assessment (from modal)
     */
    public function storeDirectAssessment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bill_number' => 'required|string|max:50',
            'responsibility_center' => 'required|string|max:50',
            'assessment_date' => 'required|date',
            'guest_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'legal_basis' => 'required|string|max:50',
            'description_type' => 'required|string|max:100',
            'names' => 'nullable|array',
            'names.*' => 'nullable|string|max:255',
            'quantities' => 'nullable|array',
            'quantities.*' => 'nullable|integer|min:1',
            'amounts' => 'nullable|array',
            'amounts.*' => 'nullable|numeric|min:0',
            'fees' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
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
            'assessment_number' => $this->generateAssessmentNumber(),
            'bill_number' => $request->bill_number,
            'responsibility_center' => $request->responsibility_center,
            'inquiry_id' => null, // Direct assessment, no inquiry
            'queue_number' => 'DIRECT-' . time(),
            'guest_name' => $request->guest_name,
            'address' => $request->address,
            'category_id' => null, // Not required for direct assessment
            'reference' => null,
            'legal_basis' => $request->legal_basis,
            'request_type' => $request->description_type,
            'names_detail' => json_encode($namesDetail),
            'fees' => $request->fees,
            'remarks' => $request->remarks,
            'processed_by' => Auth::id(),
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
        $prefix = 'DENR';
        $year = date('Y');
        $lastAssessment = \App\Models\Assessment::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastAssessment) {
            $lastNumber = intval(substr($lastAssessment->assessment_number, -6));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . '-' . $year . '-' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }
}
