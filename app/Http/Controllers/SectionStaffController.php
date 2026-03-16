<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionStaffController extends Controller
{
    /**
     * Display the section staff dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Debug logging
        \Log::info('SectionStaffController@index accessed by user: ' . $user->id . ' with role: ' . $user->role);
        
        // Get the user's assigned section from their assigned category
        $category = $user->assignedCategory;
        
        if (!$category) {
            // Log out the user and show error
            \Log::error('Section staff user ' . $user->id . ' has no category assigned');
            abort(403, 'Your account does not have a section assigned. Please contact the administrator.');
        }
        
        // Get the section from the assigned category
        $section = $category->section;
        
        // Get all categories in this section
        $sectionCategories = \App\Models\Category::where('section', $section)
            ->where('is_active', true)
            ->get();
        
        \Log::info('Section assigned: ' . $section . ' with ' . $sectionCategories->count() . ' categories');

        // Get all active categories for modal
        $categories = \App\Models\Category::where('is_active', true)->get();
        
        return view('section-staff.index', compact('section', 'sectionCategories', 'categories'));
    }

    /**
     * Get waiting list for the section staff's section (all categories)
     */
    public function waitingList()
    {
        $user = Auth::user();
        $category = $user->assignedCategory;

        // Section staff must have a category to determine their section
        if (!$category) {
            return response()->json(['error' => 'No section assigned'], 403);
        }

        // Get the section from the assigned category
        $section = $category->section;
        
        // Get all category IDs in this section
        $categoryIds = \App\Models\Category::where('section', $section)
            ->where('is_active', true)
            ->pluck('id');

        // Get all waiting inquiries for this section (all categories)
        $waiting = Inquiry::today()
            ->whereIn('category_id', $categoryIds)
            ->waiting()
            ->get();
            
        // Sort using priority rules
        $sortedWaiting = Inquiry::sortInquiriesByPriority($waiting, $section);

        return response()->json($sortedWaiting->where('status', 'waiting')->values());
    }

    /**
     * Get currently serving inquiry for section staff's section
     */
    public function currentlyServing()
    {
        $user = Auth::user();
        $category = $user->assignedCategory;

        // Section staff must have a category to determine their section
        if (!$category) {
            return response()->json(['error' => 'No section assigned'], 403);
        }

        // Get the section from the assigned category
        $section = $category->section;
        
        // Get all category IDs in this section
        $categoryIds = \App\Models\Category::where('section', $section)
            ->where('is_active', true)
            ->pluck('id');

        // Get currently serving inquiry for this section
        $serving = Inquiry::today()
            ->whereIn('category_id', $categoryIds)
            ->where('status', 'serving')
            ->with('servedBy', 'category')
            ->first();

        return response()->json($serving);
    }

    /**
     * Call next inquiry in queue (from any category in the section)
     */
    public function callNext(Request $request)
    {
        $user = Auth::user();
        $category = $user->assignedCategory;

        if (!$category) {
            return response()->json(['error' => 'No section assigned'], 403);
        }

        // Get the section from the assigned category
        $section = $category->section;
        
        // Get all category IDs in this section
        $categoryIds = \App\Models\Category::where('section', $section)
            ->where('is_active', true)
            ->pluck('id');

        // Get next waiting inquiry for this section (any category) using priority rules
        $nextInquiry = Inquiry::getNextInquiryInSection($section);

        if (!$nextInquiry) {
            return response()->json(['error' => 'No one in queue'], 404);
        }

        // Update inquiry status
        $nextInquiry->update([
            'status' => 'serving',
            'served_by' => $user->id,
            'served_at' => now(),
        ]);

        // Log the event
        \App\Models\EventLog::create([
            'inquiry_id' => $nextInquiry->id,
            'event' => 'called',
            'user_id' => $user->id,
            'details' => 'Called next inquiry from queue (Section: ' . $section . ')',
        ]);

        return response()->json([
            'success' => true,
            'inquiry' => $nextInquiry->load('category'),
        ]);
    }

    /**
     * Mark inquiry as complete
     */
    public function complete(Request $request)
    {
        $user = Auth::user();
        $inquiryId = $request->input('inquiry_id');
        
        $inquiry = Inquiry::findOrFail($inquiryId);
        
        // Verify this inquiry belongs to the user's section
        $category = $user->assignedCategory;
        if (!$category || $inquiry->category_id !== $category->id) {
            // Check if inquiry belongs to any category in the user's section
            $section = $category->section;
            $categoryInUserSection = \App\Models\Category::where('id', $inquiry->category_id)
                ->where('section', $section)
                ->first();
            
            if (!$categoryInUserSection) {
                return response()->json(['error' => 'Unauthorized - Inquiry not in your section'], 403);
            }
        }

        $inquiry->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Log the event
        \App\Models\EventLog::create([
            'inquiry_id' => $inquiry->id,
            'event' => 'completed',
            'user_id' => $user->id,
            'details' => 'Inquiry service completed',
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Skip current inquiry
     */
    public function skip(Request $request)
    {
        $user = Auth::user();
        $inquiryId = $request->input('inquiry_id');
        
        $inquiry = Inquiry::findOrFail($inquiryId);
        
        // Verify this inquiry belongs to the user's section
        $category = $user->assignedCategory;
        if (!$category || $inquiry->category_id !== $category->id) {
            // Check if inquiry belongs to any category in the user's section
            $section = $category->section;
            $categoryInUserSection = \App\Models\Category::where('id', $inquiry->category_id)
                ->where('section', $section)
                ->first();
            
            if (!$categoryInUserSection) {
                return response()->json(['error' => 'Unauthorized - Inquiry not in your section'], 403);
            }
        }

        $inquiry->update([
            'status' => 'waiting',
            'served_by' => null,
            'served_at' => null,
        ]);

        // Log the event
        \App\Models\EventLog::create([
            'inquiry_id' => $inquiry->id,
            'event' => 'skipped',
            'user_id' => $user->id,
            'details' => 'Skipped inquiry - person not present',
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Forward inquiry to admin/other section
     */
    public function forwardToAdmin(Request $request)
    {
        $user = Auth::user();
        $inquiryId = $request->input('inquiry_id');
        $forwardReason = $request->input('forward_reason', 'Requires admin assistance');
        
        $inquiry = Inquiry::findOrFail($inquiryId);
        
        // Verify this inquiry belongs to the user's section
        $category = $user->assignedCategory;
        if (!$category || $inquiry->category_id !== $category->id) {
            // Check if inquiry belongs to any category in the user's section
            $section = $category->section;
            $categoryInUserSection = \App\Models\Category::where('id', $inquiry->category_id)
                ->where('section', $section)
                ->first();
            
            if (!$categoryInUserSection) {
                return response()->json(['error' => 'Unauthorized - Inquiry not in your section'], 403);
            }
        }

        $inquiry->update([
            'status' => 'forwarded',
            'completed_at' => now(),
        ]);

        // Create new inquiry for admin
        $newInquiry = Inquiry::create([
            'category_id' => null, // Admin can see all
            'queue_number' => 'ADM-' . now()->format('Ymd') . '-' . str_pad(Inquiry::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT),
            'name' => $inquiry->name,
            'address' => $inquiry->address,
            'email' => $inquiry->email,
            'contact_number' => $inquiry->contact_number,
            'purpose' => $inquiry->purpose . ' (Forwarded from ' . ($inquiry->category ? $inquiry->category->name : 'Unknown') . ': ' . $forwardReason . ')',
            'priority' => $inquiry->priority,
            'status' => 'waiting',
            'reference_number' => $inquiry->reference_number,
        ]);

        // Log the event
        \App\Models\EventLog::create([
            'inquiry_id' => $inquiry->id,
            'event' => 'forwarded',
            'user_id' => $user->id,
            'details' => "Forwarded to admin: {$forwardReason}",
        ]);

        \App\Models\EventLog::create([
            'inquiry_id' => $newInquiry->id,
            'event' => 'created',
            'user_id' => $user->id,
            'details' => "Forwarded from " . ($inquiry->category ? $inquiry->category->name : 'Unknown'),
        ]);

        return response()->json([
            'success' => true,
            'new_inquiry' => $newInquiry,
        ]);
    }

    /**
     * Get statistics for section staff's section (all categories)
     */
    public function statistics()
    {
        $user = Auth::user();
        $category = $user->assignedCategory;

        if (!$category) {
            return response()->json(['error' => 'No section assigned'], 403);
        }

        // Get the section from the assigned category
        $section = $category->section;
        
        // Get all category IDs in this section
        $categoryIds = \App\Models\Category::where('section', $section)
            ->where('is_active', true)
            ->pluck('id');

        $today = now()->startOfDay();
        
        $stats = [
            'total' => Inquiry::whereIn('category_id', $categoryIds)
                ->whereDate('created_at', '>=', $today)
                ->count(),
            'served' => Inquiry::whereIn('category_id', $categoryIds)
                ->whereDate('created_at', '>=', $today)
                ->where('status', 'completed')
                ->count(),
            'waiting' => Inquiry::whereIn('category_id', $categoryIds)
                ->whereDate('created_at', '>=', $today)
                ->where('status', 'waiting')
                ->count(),
            'serving' => Inquiry::whereIn('category_id', $categoryIds)
                ->whereDate('created_at', '>=', $today)
                ->where('status', 'serving')
                ->count(),
        ];

        return response()->json($stats);
    }
}
