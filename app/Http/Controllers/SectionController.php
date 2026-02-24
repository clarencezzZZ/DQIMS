<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
{
    /**
     * Display the section dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Debug logging
        \Log::info('SectionController@index accessed by user: ' . $user->id . ' with role: ' . $user->role);
        
        $category = $user->assignedCategory;
        
        \Log::info('Category assigned: ' . ($category ? $category->id : 'none'));

        // Allow admin and section staff users to access section dashboard even without category assignment
        if (!$category && !$user->isAdmin() && !$user->isSectionStaff()) {
            return redirect()->route('dashboard')->with('error', 'No category assigned to you.');
        }

        // For admin and section staff users without category, show all categories or a selection interface
        if ((!$category && $user->isAdmin()) || (!$category && $user->isSectionStaff())) {
            // For now, let's pass null category and handle it in the view
            return view('section.index', ['category' => null]);
        }

        return view('section.index', compact('category'));
    }

    /**
     * Get waiting list for the section
     */
    public function waitingList()
    {
        $user = Auth::user();
        $categoryId = $user->assigned_category_id;

        // Allow section staff to access all categories if not assigned to a specific one
        if (!$categoryId && $user->isSectionStaff()) {
            $waiting = Inquiry::today()
                ->waiting()
                ->orderBy('created_at')
                ->get();
        } elseif (!$categoryId && !$user->isAdmin() && !$user->isSectionStaff()) {
            return response()->json(['error' => 'No category assigned'], 403);
        } else {
            $waiting = Inquiry::today()
                ->byCategory($categoryId)
                ->waiting()
                ->orderBy('created_at')
                ->get();
        }

        return response()->json($waiting);
    }

    /**
     * Get currently serving inquiry
     */
    public function currentlyServing()
    {
        $user = Auth::user();
        $categoryId = $user->assigned_category_id;

        // Allow section staff to access all categories if not assigned to a specific one
        if (!$categoryId && $user->isSectionStaff()) {
            $serving = Inquiry::today()
                ->where('status', 'serving')
                ->with('servedBy', 'category')
                ->first();
        } elseif (!$categoryId && !$user->isAdmin() && !$user->isSectionStaff()) {
            return response()->json(['error' => 'No category assigned'], 403);
        } else {
            $serving = Inquiry::today()
                ->byCategory($categoryId)
                ->where('status', 'serving')
                ->with('servedBy')
                ->first();
        }

        return response()->json($serving);
    }

    /**
     * Call next inquiry
     */
    public function callNext()
    {
        $user = Auth::user();
        $categoryId = $user->assigned_category_id;

        // Allow section staff to access all categories if not assigned to a specific one
        if (!$categoryId && $user->isSectionStaff()) {
            // For section staff with no assigned category, we need to specify which category to serve
            // Since they can access all categories, we'll need to get the category from the request
            $targetCategoryId = request()->input('category_id');
            
            if (!$targetCategoryId) {
                return response()->json(['error' => 'Category ID required for section staff'], 400);
            }
            
            // Check if already serving someone in the target category
            $currentlyServing = Inquiry::today()
                ->byCategory($targetCategoryId)
                ->where('status', 'serving')
                ->first();

            if ($currentlyServing) {
                return response()->json([
                    'error' => 'Already serving ' . $currentlyServing->queue_number
                ], 422);
            }

            // Get next waiting inquiry in the target category
            $nextInquiry = Inquiry::today()
                ->byCategory($targetCategoryId)
                ->waiting()
                ->orderBy('created_at')
                ->first();
        } elseif (!$categoryId && !$user->isAdmin() && !$user->isSectionStaff()) {
            return response()->json(['error' => 'No category assigned'], 403);
        } else {
            // Check if already serving someone
            $currentlyServing = Inquiry::today()
                ->byCategory($categoryId)
                ->where('status', 'serving')
                ->first();

            if ($currentlyServing) {
                return response()->json([
                    'error' => 'Already serving ' . $currentlyServing->queue_number
                ], 422);
            }

            // Get next waiting inquiry
            $nextInquiry = Inquiry::today()
                ->byCategory($categoryId)
                ->waiting()
                ->orderBy('created_at')
                ->first();
        }

        if (!$nextInquiry) {
            return response()->json(['error' => 'No waiting inquiries'], 404);
        }

        $nextInquiry->markAsServing($user->id);

        // Broadcast event
        // event(new InquiryCalled($nextInquiry));

        return response()->json([
            'success' => true,
            'inquiry' => $nextInquiry->load('category')
        ]);
    }

    /**
     * Complete current inquiry
     */
    public function complete(Request $request)
    {
        $user = Auth::user();
        $categoryId = $user->assigned_category_id;
        
        // Allow section staff to access all categories if not assigned to a specific one
        if (!$categoryId && $user->isSectionStaff()) {
            // For section staff with no assigned category, we need to specify which category to serve
            $targetCategoryId = request()->input('category_id');
            $inquiryId = request()->input('inquiry_id');
            
            if (!$targetCategoryId) {
                return response()->json(['error' => 'Category ID required for section staff'], 400);
            }
            
            if ($inquiryId) {
                // Complete specific inquiry by ID
                $inquiry = Inquiry::today()
                    ->where('id', $inquiryId)
                    ->where('status', 'serving')
                    ->first();
            } else {
                // Complete inquiry in target category
                $inquiry = Inquiry::today()
                    ->byCategory($targetCategoryId)
                    ->where('status', 'serving')
                    ->first();
            }
        } elseif (!$categoryId && !$user->isAdmin() && !$user->isSectionStaff()) {
            return response()->json(['error' => 'No category assigned'], 403);
        } else {
            $inquiry = Inquiry::today()
                ->byCategory($categoryId)
                ->where('status', 'serving')
                ->first();
        }

        if (!$inquiry) {
            return response()->json(['error' => 'No inquiry being served'], 404);
        }

        $inquiry->update([
            'remarks' => $request->remarks,
        ]);
        $inquiry->markAsCompleted();

        // Broadcast event
        // event(new InquiryCompleted($inquiry));

        return response()->json([
            'success' => true,
            'message' => 'Inquiry completed'
        ]);
    }

    /**
     * Skip current inquiry
     */
    public function skip(Request $request)
    {
        $user = Auth::user();
        $categoryId = $user->assigned_category_id;
        
        // Allow section staff to access all categories if not assigned to a specific one
        if (!$categoryId && $user->isSectionStaff()) {
            // For section staff with no assigned category, we need to specify which category to serve
            $targetCategoryId = request()->input('category_id');
            $inquiryId = request()->input('inquiry_id');
            
            if (!$targetCategoryId) {
                return response()->json(['error' => 'Category ID required for section staff'], 400);
            }
            
            if ($inquiryId) {
                // Skip specific inquiry by ID
                $inquiry = Inquiry::today()
                    ->where('id', $inquiryId)
                    ->where('status', 'serving')
                    ->first();
            } else {
                // Skip inquiry in target category
                $inquiry = Inquiry::today()
                    ->byCategory($targetCategoryId)
                    ->where('status', 'serving')
                    ->first();
            }
        } elseif (!$categoryId && !$user->isAdmin() && !$user->isSectionStaff()) {
            return response()->json(['error' => 'No category assigned'], 403);
        } else {
            $inquiry = Inquiry::today()
                ->byCategory($categoryId)
                ->where('status', 'serving')
                ->first();
        }

        if (!$inquiry) {
            return response()->json(['error' => 'No inquiry being served'], 404);
        }

        $inquiry->update([
            'remarks' => $request->remarks,
        ]);
        $inquiry->markAsSkipped();

        return response()->json([
            'success' => true,
            'message' => 'Inquiry skipped'
        ]);
    }

    /**
     * Forward inquiry to admin
     */
    public function forwardToAdmin(Request $request)
    {
        $user = Auth::user();
        $categoryId = $user->assigned_category_id;
        
        // Allow section staff to access all categories if not assigned to a specific one
        if (!$categoryId && $user->isSectionStaff()) {
            // For section staff with no assigned category, we need to specify which category to serve
            $targetCategoryId = request()->input('category_id');
            $inquiryId = request()->input('inquiry_id');
            
            if (!$targetCategoryId) {
                return response()->json(['error' => 'Category ID required for section staff'], 400);
            }
            
            if ($inquiryId) {
                // Forward specific inquiry by ID
                $inquiry = Inquiry::today()
                    ->where('id', $inquiryId)
                    ->where('status', 'serving')
                    ->first();
            } else {
                // Forward inquiry in target category
                $inquiry = Inquiry::today()
                    ->byCategory($targetCategoryId)
                    ->where('status', 'serving')
                    ->first();
            }
        } elseif (!$categoryId && !$user->isAdmin() && !$user->isSectionStaff()) {
            return response()->json(['error' => 'No category assigned'], 403);
        } else {
            $inquiry = Inquiry::today()
                ->byCategory($categoryId)
                ->where('status', 'serving')
                ->first();
        }

        if (!$inquiry) {
            return response()->json(['error' => 'No inquiry being served'], 404);
        }

        $inquiry->update([
            'status' => 'forwarded',
            'remarks' => $request->remarks,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inquiry forwarded to admin'
        ]);
    }

    /**
     * Get section statistics
     */
    public function statistics()
    {
        $user = Auth::user();
        $categoryId = $user->assigned_category_id;

        // Allow section staff to access all categories if not assigned to a specific one
        if (!$categoryId && $user->isSectionStaff()) {
            // For section staff with no assigned category, get statistics for all categories
            $targetCategoryId = request()->input('category_id');
            
            if ($targetCategoryId) {
                // Get statistics for a specific category
                $today = Inquiry::today()->byCategory($targetCategoryId);
            } else {
                // Get statistics for all categories
                $today = Inquiry::today();
            }
        } elseif (!$categoryId && !$user->isAdmin() && !$user->isSectionStaff()) {
            return response()->json(['error' => 'No category assigned'], 403);
        } else {
            $today = Inquiry::today()->byCategory($categoryId);
        }

        return response()->json([
            'waiting' => (clone $today)->waiting()->count(),
            'serving' => (clone $today)->where('status', 'serving')->count(),
            'completed' => (clone $today)->completed()->count(),
            'skipped' => (clone $today)->where('status', 'skipped')->count(),
        ]);
    }
}
