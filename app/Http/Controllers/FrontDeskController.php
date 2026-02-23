<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FrontDeskController extends Controller
{
    /**
     * Display the front desk dashboard
     */
    public function index()
    {
        $categories = Category::where('is_active', true)->get();
        $todayInquiries = Inquiry::today()->with('category')->latest()->take(10)->get();
        
        return view('front-desk.index', compact('categories', 'todayInquiries'));
    }

    /**
     * Show the form for creating a new inquiry
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('front-desk.create', compact('categories'));
    }

    /**
     * Store a newly created inquiry
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guest_name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'category_id' => 'required|exists:categories,id',
            'purpose' => 'nullable|string',
            'priority' => 'nullable|in:normal,priority,urgent',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $category = Category::findOrFail($request->category_id);
        $queueNumber = $this->generateQueueNumber($category);

        $inquiry = Inquiry::create([
            'queue_number' => $queueNumber,
            'guest_name' => $request->guest_name,
            'contact_number' => $request->contact_number,
            'category_id' => $request->category_id,
            'request_type' => $category->name,
            'purpose' => $request->purpose,
            'priority' => $request->priority ?? 'normal',
            'status' => 'waiting',
            'date' => now()->toDateString(),
        ]);

        // Redirect to monitor display
        return redirect()->route('monitor.lobby')
            ->with('success', 'Inquiry created successfully! Queue number: ' . $queueNumber);
    }

    /**
     * Generate queue number for category
     */
    private function generateQueueNumber($category)
    {
        $today = now()->format('Y-m-d');
        $prefix = $category->code;
        
        $lastInquiry = Inquiry::where('category_id', $category->id)
            ->whereDate('created_at', $today)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInquiry) {
            $lastNumber = intval(substr($lastInquiry->queue_number, -3));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Display the ticket for printing
     */
    public function printTicket(Inquiry $inquiry)
    {
        return view('front-desk.ticket', compact('inquiry'));
    }

    /**
     * Get current queue status for all sections
     */
    public function queueStatus()
    {
        $today = now()->toDateString();
        
        $data = [
            'today_count' => Inquiry::whereDate('created_at', $today)->count(),
            'waiting_count' => Inquiry::whereDate('created_at', $today)->where('status', 'waiting')->count(),
            'serving_count' => Inquiry::whereDate('created_at', $today)->where('status', 'serving')->count(),
            'completed_count' => Inquiry::whereDate('created_at', $today)->where('status', 'completed')->count(),
            'sections' => []
        ];

        // Get sections (excluding RECORDS)
        $sections = Category::where('is_active', true)
            ->where('section', '!=', 'RECORDS')
            ->select('section')
            ->distinct()
            ->pluck('section');
        
        foreach ($sections as $section) {
            $categoryIds = Category::where('section', $section)->pluck('id');
            
            // Get waiting count for this section
            $waitingCount = Inquiry::whereDate('created_at', $today)
                ->whereIn('category_id', $categoryIds)
                ->where('status', 'waiting')
                ->count();
            
            // Get now serving for this section
            $nowServing = Inquiry::whereDate('created_at', $today)
                ->whereIn('category_id', $categoryIds)
                ->where('status', 'serving')
                ->with('category')
                ->first();
            
            // Get latest waiting inquiry
            $latestWaiting = Inquiry::whereDate('created_at', $today)
                ->whereIn('category_id', $categoryIds)
                ->where('status', 'waiting')
                ->with('category')
                ->latest()
                ->first();
            
            $data['sections'][$section] = [
                'waiting_count' => $waitingCount,
                'now_serving' => $nowServing ? $nowServing->queue_number : null,
                'now_serving_category' => $nowServing ? $nowServing->category->code : null,
                'latest_waiting' => $latestWaiting ? $latestWaiting->queue_number : null,
                'latest_waiting_category' => $latestWaiting ? $latestWaiting->category->code : null,
            ];
        }

        return response()->json($data);
    }

    /**
     * Get recent inquiries for display
     */
    public function recentInquiries()
    {
        $inquiries = Inquiry::today()
            ->with('category')
            ->latest()
            ->take(10)
            ->get();

        $html = '';
        
        if ($inquiries->isEmpty()) {
            $html = '
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p class="mt-2">No inquiries yet today</p>
                    </td>
                </tr>
            ';
        } else {
            foreach ($inquiries as $inquiry) {
                $statusBadge = '';
                if ($inquiry->status == 'waiting') {
                    $statusBadge = '<span class="badge bg-warning text-dark">Waiting</span>';
                } elseif ($inquiry->status == 'serving') {
                    $statusBadge = '<span class="badge bg-info">Serving</span>';
                } elseif ($inquiry->status == 'completed') {
                    $statusBadge = '<span class="badge bg-success">Completed</span>';
                } elseif ($inquiry->status == 'skipped') {
                    $statusBadge = '<span class="badge bg-danger">Skipped</span>';
                }

                $categoryBadge = $inquiry->category 
                    ? '<span class="badge" style="background-color: ' . $inquiry->category->color . '">' . $inquiry->category->code . '</span>'
                    : '<span class="badge bg-secondary">N/A</span>';

                $html .= '
                    <tr>
                        <td><span class="badge bg-dark fs-6">' . $inquiry->queue_number . '</span></td>
                        <td>' . $inquiry->guest_name . '</td>
                        <td>' . $categoryBadge . '</td>
                        <td>' . $statusBadge . '</td>
                        <td>' . $inquiry->created_at->format('h:i A') . '</td>
                        <td>
                            <a href="' . route('front-desk.ticket', $inquiry) . '" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-printer"></i> Print
                            </a>
                        </td>
                    </tr>
                ';
            }
        }

        return response($html);
    }
}
