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
        
        // Check if category is passed via URL parameter (for admin viewing specific category)
        if (request()->has('category')) {
            $categoryId = request()->input('category');
            $category = \App\Models\Category::find($categoryId);
            \Log::info('Category from URL parameter: ' . ($category ? $category->id : 'none'));
        } else {
            \Log::info('Category assigned: ' . ($category ? $category->id : 'none'));
        }

        // Allow admin and section officer users to access section dashboard even without category assignment
        if (!$category && !$user->isAdmin() && !$user->isSectionOfficer()) {
            return redirect()->route('dashboard')->with('error', 'No category assigned to you.');
        }

        // For admin and section officer users without category, show all categories or a selection interface
        if ((!$category && $user->isAdmin()) || (!$category && $user->isSectionOfficer())) {
            // Pass all categories for modal
            $categories = \App\Models\Category::where('is_active', true)->get();
            return view('section.index', ['category' => null, 'categories' => $categories]);
        }

        // Get all active categories for modal even when viewing specific category
        $categories = \App\Models\Category::where('is_active', true)->get();
        return view('section.index', compact('category', 'categories'));
    }

    /**
     * Get waiting list for the section
     */
    public function waitingList()
    {
        $user = Auth::user();
        $categoryId = $user->assigned_category_id;

        // Allow section officer to access all categories if not assigned to a specific one
        if (!$categoryId && $user->isSectionOfficer()) {
            $waiting = Inquiry::today()
                ->waiting()
                ->orderBy('created_at')
                ->get();
        } elseif (!$categoryId && !$user->isAdmin() && !$user->isSectionOfficer()) {
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

        // Allow section officer to access all categories if not assigned to a specific one
        if (!$categoryId && $user->isSectionOfficer()) {
            $serving = Inquiry::today()
                ->where('status', 'serving')
                ->with('servedBy', 'category')
                ->first();
        } elseif (!$categoryId && !$user->isAdmin() && !$user->isSectionOfficer()) {
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
        if (!$categoryId && $user->isSectionOfficer()) {
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

            // Get next waiting inquiry using priority queuing algorithm
            $nextInquiry = $this->getNextInquiryByPriority($targetCategoryId);
        } elseif (!$categoryId && !$user->isAdmin() && !$user->isSectionOfficer()) {
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

            // Get next waiting inquiry using priority queuing algorithm
            $nextInquiry = $this->getNextInquiryByPriority($categoryId);
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
     * Get next inquiry using priority queuing algorithm (SECTION-WIDE FIFO)
     * Algorithm: First-Come, First-Serve per section, regardless of service type (category)
     * - Only the earliest waiting queue in that section can be served
     * - Priority alternation: NORMAL → PRIORITY → NORMAL → PRIORITY
     * - All categories within the same section share one unified queue
     * - Each section operates independently
     */
    private function getNextInquiryByPriority($categoryId)
    {
        // Get the category to determine which section we're working with
        $category = Category::find($categoryId);
        if (!$category) {
            return null;
        }
        
        $section = $category->section;
        
        // Get ALL waiting inquiries in this SECTION (across all categories), ordered by creation time (FIFO)
        $waitingInquiries = Inquiry::today()
            ->join('categories', 'inquiries.category_id', '=', 'categories.id')
            ->where('categories.section', $section)
            ->where('inquiries.status', 'waiting')
            ->select('inquiries.*')
            ->orderBy('inquiries.created_at')
            ->get();

        if ($waitingInquiries->isEmpty()) {
            return null;
        }

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

        // If there are no priority inquiries, return the oldest normal inquiry (first in section queue)
        if ($priorityInquiries->isEmpty()) {
            return $normalInquiries->first();
        }

        // If there are no normal inquiries, return the oldest priority inquiry (first in section queue)
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

    /**
     * Complete current inquiry
     */
    public function complete(Request $request)
    {
        $user = Auth::user();
        $categoryId = $user->assigned_category_id;
        
        // Allow section staff to access all categories if not assigned to a specific one
        if (!$categoryId && $user->isSectionOfficer()) {
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
        } elseif (!$categoryId && !$user->isAdmin() && !$user->isSectionOfficer()) {
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
        if (!$categoryId && $user->isSectionOfficer()) {
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
        } elseif (!$categoryId && !$user->isAdmin() && !$user->isSectionOfficer()) {
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
        if (!$categoryId && $user->isSectionOfficer()) {
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
        } elseif (!$categoryId && !$user->isAdmin() && !$user->isSectionOfficer()) {
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
        $targetCategoryId = request()->input('category_id');

        \Log::info('=== Statistics Request ===');
        \Log::info('User ID: ' . $user->id);
        \Log::info('Username: ' . $user->username);
        \Log::info('User Role: ' . $user->role);
        \Log::info('Assigned Category ID: ' . ($categoryId ?? 'null'));
        \Log::info('Requested category_id param: ' . ($targetCategoryId ?? 'null'));

        // Allow section staff to access all categories if not assigned to a specific one
        if (!$categoryId && $user->isSectionOfficer()) {
            // For section staff with no assigned category, get statistics for all categories
            if ($targetCategoryId) {
                // Get statistics for a specific category
                \Log::info('Path: Section staff - Stats for specific category: ' . $targetCategoryId);
                $today = Inquiry::today()->byCategory($targetCategoryId);
            } else {
                // Get statistics for all categories
                \Log::info('Path: Section staff - Stats for all categories');
                $today = Inquiry::today();
            }
        } elseif (!$categoryId && $user->isAdmin()) {
            // For admin users with no category, check if requesting specific category
            if ($targetCategoryId) {
                // Get statistics for a specific category
                \Log::info('Path: Admin - Stats for specific category: ' . $targetCategoryId);
                $today = Inquiry::today()->byCategory($targetCategoryId);
            } else {
                // Get statistics for ALL categories (admin overview)
                \Log::info('Path: Admin - Stats for all categories');
                $today = Inquiry::today();
            }
        } elseif (!$categoryId && !$user->isAdmin() && !$user->isSectionOfficer()) {
            \Log::error('Path: Unauthorized - No category assigned');
            return response()->json(['error' => 'No category assigned'], 403);
        } else {
            // User has an assigned category
            \Log::info('Path: User with assigned category - Stats for category: ' . $categoryId);
            $today = Inquiry::today()->byCategory($categoryId);
        }

        $stats = [
            'waiting' => (clone $today)->waiting()->count(),
            'serving' => (clone $today)->where('status', 'serving')->count(),
            'completed' => (clone $today)->completed()->count(),
            'skipped' => (clone $today)->where('status', 'skipped')->count(),
        ];

        \Log::info('Statistics result:', $stats);
        \Log::info('=== End Statistics Request ===');

        return response()->json($stats);
    }
}
