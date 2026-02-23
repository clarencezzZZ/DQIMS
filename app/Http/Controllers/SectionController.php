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
        $category = $user->assignedCategory;

        if (!$category) {
            return redirect()->route('home')->with('error', 'No category assigned to you.');
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

        if (!$categoryId) {
            return response()->json(['error' => 'No category assigned'], 403);
        }

        $waiting = Inquiry::today()
            ->byCategory($categoryId)
            ->waiting()
            ->orderBy('created_at')
            ->get();

        return response()->json($waiting);
    }

    /**
     * Get currently serving inquiry
     */
    public function currentlyServing()
    {
        $user = Auth::user();
        $categoryId = $user->assigned_category_id;

        if (!$categoryId) {
            return response()->json(['error' => 'No category assigned'], 403);
        }

        $serving = Inquiry::today()
            ->byCategory($categoryId)
            ->where('status', 'serving')
            ->with('servedBy')
            ->first();

        return response()->json($serving);
    }

    /**
     * Call next inquiry
     */
    public function callNext()
    {
        $user = Auth::user();
        $categoryId = $user->assigned_category_id;

        if (!$categoryId) {
            return response()->json(['error' => 'No category assigned'], 403);
        }

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

        $inquiry = Inquiry::today()
            ->byCategory($categoryId)
            ->where('status', 'serving')
            ->first();

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

        $inquiry = Inquiry::today()
            ->byCategory($categoryId)
            ->where('status', 'serving')
            ->first();

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

        $inquiry = Inquiry::today()
            ->byCategory($categoryId)
            ->where('status', 'serving')
            ->first();

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

        if (!$categoryId) {
            return response()->json(['error' => 'No category assigned'], 403);
        }

        $today = Inquiry::today()->byCategory($categoryId);

        return response()->json([
            'waiting' => (clone $today)->waiting()->count(),
            'serving' => (clone $today)->where('status', 'serving')->count(),
            'completed' => (clone $today)->completed()->count(),
            'skipped' => (clone $today)->where('status', 'skipped')->count(),
        ]);
    }
}
