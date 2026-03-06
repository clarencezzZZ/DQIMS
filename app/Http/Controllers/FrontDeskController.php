<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FrontDeskController extends Controller
{
    /**
     * Get full section name from acronym
     */
    private function getSectionFullName($section)
    {
        $sectionNames = [
            'ACS' => 'AGGREGATE AND CORRECTION SECTION',
            'OOSS' => 'ORIGINAL AND OTHER SURVEYS SECTION',
            'LES' => 'LAND EVALUATION SECTION',
            'SCS' => 'SURVEYS AND CONTROL SECTION',
        ];
        return $sectionNames[$section] ?? $section;
    }
    
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
            'contact_number' => 'nullable|string|regex:/^09\d{9}$/',
            'category_id' => 'required|exists:categories,id',
            'purpose' => 'nullable|string',
            'priority' => 'nullable|in:normal,priority',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $category = Category::findOrFail($request->category_id);
        
        // Generate queue number with retry logic
        $maxRetries = 5;
        $queueNumber = null;
        
        for ($i = 0; $i < $maxRetries; $i++) {
            $queueNumber = $this->generateQueueNumber($category);
            
            // Check if queue number already exists
            if (!Inquiry::where('queue_number', $queueNumber)->exists()) {
                break;
            }
            
            // If we've tried max retries, throw an exception
            if ($i === $maxRetries - 1) {
                // Try one final time with timestamp-based approach
                $timestamp = now()->format('His');
                $lastNumber = $category->getTodayCounter()->last_number + 1;
                $queueNumber = sprintf('%s-%03d-%s', $category->code, $lastNumber, $timestamp);
                
                // Final check
                if (Inquiry::where('queue_number', $queueNumber)->exists()) {
                    return back()->withErrors(['queue_number' => 'Unable to generate unique queue number after multiple attempts. Please contact system administrator.'])
                               ->withInput();
                }
                break;
            }
            
            // Wait a small amount before retrying
            usleep(100000); // 0.1 second
        }

        $inquiry = Inquiry::create([
            'queue_number' => $queueNumber,
            'guest_name' => $request->guest_name,
            'address' => $request->address,
            'category_id' => $request->category_id,
            'request_type' => $category->name,
            'purpose' => $request->purpose,
            'priority' => $request->priority ?? 'normal',
            'status' => 'waiting',
            'date' => now()->toDateString(),
        ]);

        // Return JSON response for AJAX handling
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Generated ticket has been submitted successfully!',
                'queue_number' => $queueNumber,
                'guest_name' => $request->guest_name,
                'category' => $category->name
            ]);
        }

        // Fallback for non-AJAX requests (redirect to monitor)
        return redirect()->route('monitor.lobby')
            ->with('success', 'Inquiry created successfully! Queue number: ' . $queueNumber);
    }

    /**
     * Generate queue number for category
     */
    private function generateQueueNumber($category)
    {
        return $category->generateQueueNumber();
    }

    /**
     * Display the ticket for printing
     */
    public function printTicket(Inquiry $inquiry)
    {
        return view('front-desk.ticket', compact('inquiry'));
    }

    /**
     * Get current queue status for all sections (JSON API)
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
            
            $data['sections'][$this->getSectionFullName($section)] = [
                'acronym' => $section,
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
     * Display the live queue status page
     */
    public function showQueueStatus()
    {
        return view('front-desk.queue-status');
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
                    <td colspan="7" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p class="mt-2">No inquiries yet today</p>
                    </td>
                </tr>
            ';
        } else {
            foreach ($inquiries as $index => $inquiry) {
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
                    ? '<span class="badge" style="background-color: ' . $inquiry->category->color . '">' . $inquiry->category->name . '</span>'
                    : '<span class="badge bg-secondary">N/A</span>';

                // Format queue number to show only #N
                $formattedQueueNumber = $this->formatQueueNumber($inquiry->queue_number);

                // Get section name
                $sectionName = 'N/A';
                if ($inquiry->category && $inquiry->category->section) {
                    $sectionName = $this->getSectionFullName($inquiry->category->section);
                }

                $html .= '
                    <tr>
                        <td><span class="badge bg-dark fs-6">' . $formattedQueueNumber . '</span></td>
                        <td>' . $inquiry->guest_name . '</td>
                        <td>' . $categoryBadge . '</td>
                        <td class="text-center">' . $statusBadge . '</td>
                        <td class="text-center">' . $inquiry->created_at->format('h:i A') . '</td>
                        <td><span class="badge bg-secondary">' . $sectionName . '</span></td>
                        <td class="text-center">
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

    /**
     * Format queue number to show only sequential number as #N
     */
    private function formatQueueNumber($fullQueueNumber)
    {
        if (!$fullQueueNumber) return '---';
        
        // Try to extract the last number after the last hyphen
        // e.g., "SECSIME NO.R4A-L_SMD-01-009" -> "#9"
        $parts = explode('-', $fullQueueNumber);
        if (count($parts) > 0) {
            $lastPart = end($parts);
            $num = intval(preg_replace('/^0+/', '', $lastPart)); // Remove leading zeros
            if ($num > 0 || $lastPart === '0') {
                return '#' . $num;
            }
        }
        // Fallback: if no number found, return the original
        return $fullQueueNumber;
    }
}
