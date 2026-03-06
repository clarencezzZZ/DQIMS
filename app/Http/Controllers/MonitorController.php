<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Inquiry;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    /**
     * Display lobby monitor (Ground Floor)
     */
    public function lobby()
    {
        // Get unique sections with their categories (exclude RECORDS)
        $sections = Category::where('is_active', true)
            ->where('section', '!=', 'RECORDS')
            ->select('section')
            ->distinct()
            ->pluck('section');
        
        $sectionData = [];
        foreach ($sections as $section) {
            $categories = Category::where('section', $section)->where('is_active', true)->get();
            $sectionData[$section] = [
                'name' => $this->getSectionFullName($section),
                'acronym' => $section,
                'categories' => $categories,
                'color' => $this->getSectionColor($section),
            ];
        }
        
        return view('monitor.lobby', compact('sectionData'));
    }
    
    /**
     * Display lobby1 monitor (categories assigned to lobby1)
     */
    public function lobby1()
    {
        // Get categories assigned to lobby1
        $categories = Category::where('lobby', 'lobby1')->where('is_active', true)->get();
        
        $sectionData = [];
        foreach ($categories as $category) {
            $section = $category->section;
            if (!isset($sectionData[$section])) {
                $sectionData[$section] = [
                    'name' => $this->getSectionFullName($section),
                    'acronym' => $section,
                    'categories' => [],
                    'color' => $this->getSectionColor($section),
                ];
            }
            $sectionData[$section]['categories'][] = $category;
        }
        
        return view('monitor.lobby1', compact('sectionData'));
    }
    
    /**
     * Display lobby2 monitor (categories assigned to lobby2)
     */
    public function lobby2()
    {
        // Get categories assigned to lobby2
        $categories = Category::where('lobby', 'lobby2')->where('is_active', true)->get();
        
        $sectionData = [];
        foreach ($categories as $category) {
            $section = $category->section;
            if (!isset($sectionData[$section])) {
                $sectionData[$section] = [
                    'name' => $this->getSectionFullName($section),
                    'acronym' => $section,
                    'categories' => [],
                    'color' => $this->getSectionColor($section),
                ];
            }
            $sectionData[$section]['categories'][] = $category;
        }
        
        return view('monitor.lobby2', compact('sectionData'));
    }
    
    /**
     * Get color for section
     */
    private function getSectionColor($section)
    {
        $colors = [
            'ACS' => '#e74c3c',
            'OOSS' => '#3498db',
            'SCS' => '#2ecc71',
            'LES' => '#9b59b6',
        ];
        return $colors[$section] ?? '#6c757d';
    }
    
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
     * Display third floor monitor
     */
    public function thirdFloor()
    {
        $categories = Category::where('is_active', true)->get();
        return view('monitor.third-floor', compact('categories'));
    }

    /**
     * Get current queue data for monitors
     */
    public function queueData()
    {
        $sections = Category::where('is_active', true)
            ->where('section', '!=', 'RECORDS')
            ->select('section')
            ->distinct()
            ->pluck('section');
        
        $data = ['sections' => []];

        foreach ($sections as $section) {
            $categories = Category::where('section', $section)->where('is_active', true)->pluck('id');
            
            // Get currently serving (only serving status, not completed)
            $nowServing = Inquiry::today()
                ->whereIn('category_id', $categories)
                ->where('status', 'serving')
                ->with('servedBy', 'category')
                ->first();

            // Get waiting count (only waiting status)
            $waitingCount = Inquiry::today()
                ->whereIn('category_id', $categories)
                ->where('status', 'waiting')
                ->count();

            // Get all waiting inquiries ordered by created_at
            $waitingInquiries = Inquiry::today()
                ->whereIn('category_id', $categories)
                ->where('status', 'waiting')
                ->with('category')
                ->oldest('created_at')
                ->get();

            // First in queue (next to be served)
            $firstInQueue = $waitingInquiries->first();
            
            // Second in queue (next after first)
            $secondInQueue = $waitingInquiries->skip(1)->first();

            $data['sections'][$section] = [
                'section' => $this->getSectionFullName($section),
                'acronym' => $section,
                'color' => $this->getSectionColor($section),
                'now_serving' => $nowServing ? $nowServing->queue_number : null,
                'now_serving_category' => $nowServing && $nowServing->category ? $nowServing->category->code : null,
                'first_in_queue' => $firstInQueue ? $firstInQueue->queue_number : null,
                'first_in_queue_category' => $firstInQueue && $firstInQueue->category ? $firstInQueue->category->code : null,
                'second_in_queue' => $secondInQueue ? $secondInQueue->queue_number : null,
                'second_in_queue_category' => $secondInQueue && $secondInQueue->category ? $secondInQueue->category->code : null,
                'waiting_count' => $waitingCount,
            ];
        }

        return response()->json($data);
    }
    
    /**
     * Get current queue data for lobby1 monitor (categories assigned to lobby1)
     */
    public function queueDataLobby1()
    {
        $categories = Category::where('lobby', 'lobby1')->where('is_active', true)->get();
        
        $data = ['sections' => []];

        foreach ($categories as $category) {
            $section = $category->section;
            if (!isset($data['sections'][$section])) {
                $data['sections'][$section] = [
                    'section' => $this->getSectionFullName($section),
                    'acronym' => $section,
                    'color' => $this->getSectionColor($section),
                    'now_serving' => null,
                    'now_serving_category' => null,
                    'first_in_queue' => null,
                    'first_in_queue_category' => null,
                    'second_in_queue' => null,
                    'second_in_queue_category' => null,
                    'waiting_count' => 0,
                ];
            }
            
            // Get all waiting inquiries ordered by created_at for this category
            $waitingInquiries = Inquiry::today()
                ->where('category_id', $category->id)
                ->where('status', 'waiting')
                ->with('category')
                ->oldest('created_at')
                ->get();

            // First in queue (next to be served)
            $firstInQueue = $waitingInquiries->first();
            
            // Second in queue (next after first)
            $secondInQueue = $waitingInquiries->skip(1)->first();

            // Get currently serving for this category
            $nowServing = Inquiry::today()
                ->where('category_id', $category->id)
                ->where('status', 'serving')
                ->with('servedBy', 'category')
                ->first();

            // Get waiting count for this category
            $waitingCount = Inquiry::today()
                ->where('category_id', $category->id)
                ->where('status', 'waiting')
                ->count();

            // Update section data with category information
            if ($nowServing) {
                $data['sections'][$section]['now_serving'] = $nowServing->queue_number;
                $data['sections'][$section]['now_serving_category'] = $nowServing->category ? $nowServing->category->code : null;
            }
            
            if ($firstInQueue) {
                $data['sections'][$section]['first_in_queue'] = $firstInQueue->queue_number;
                $data['sections'][$section]['first_in_queue_category'] = $firstInQueue->category ? $firstInQueue->category->code : null;
            }
            
            if ($secondInQueue) {
                $data['sections'][$section]['second_in_queue'] = $secondInQueue->queue_number;
                $data['sections'][$section]['second_in_queue_category'] = $secondInQueue->category ? $secondInQueue->category->code : null;
            }
            
            $data['sections'][$section]['waiting_count'] += $waitingCount;
        }

        return response()->json($data);
    }
    
    /**
     * Get current queue data for lobby2 monitor (categories assigned to lobby2)
     */
    public function queueDataLobby2()
    {
        $categories = Category::where('lobby', 'lobby2')->where('is_active', true)->get();
        
        $data = ['sections' => []];

        foreach ($categories as $category) {
            $section = $category->section;
            if (!isset($data['sections'][$section])) {
                $data['sections'][$section] = [
                    'section' => $this->getSectionFullName($section),
                    'acronym' => $section,
                    'color' => $this->getSectionColor($section),
                    'now_serving' => null,
                    'now_serving_category' => null,
                    'first_in_queue' => null,
                    'first_in_queue_category' => null,
                    'second_in_queue' => null,
                    'second_in_queue_category' => null,
                    'waiting_count' => 0,
                ];
            }
            
            // Get all waiting inquiries ordered by created_at for this category
            $waitingInquiries = Inquiry::today()
                ->where('category_id', $category->id)
                ->where('status', 'waiting')
                ->with('category')
                ->oldest('created_at')
                ->get();

            // First in queue (next to be served)
            $firstInQueue = $waitingInquiries->first();
            
            // Second in queue (next after first)
            $secondInQueue = $waitingInquiries->skip(1)->first();

            // Get currently serving for this category
            $nowServing = Inquiry::today()
                ->where('category_id', $category->id)
                ->where('status', 'serving')
                ->with('servedBy', 'category')
                ->first();

            // Get waiting count for this category
            $waitingCount = Inquiry::today()
                ->where('category_id', $category->id)
                ->where('status', 'waiting')
                ->count();

            // Update section data with category information
            if ($nowServing) {
                $data['sections'][$section]['now_serving'] = $nowServing->queue_number;
                $data['sections'][$section]['now_serving_category'] = $nowServing->category ? $nowServing->category->code : null;
            }
            
            if ($firstInQueue) {
                $data['sections'][$section]['first_in_queue'] = $firstInQueue->queue_number;
                $data['sections'][$section]['first_in_queue_category'] = $firstInQueue->category ? $firstInQueue->category->code : null;
            }
            
            if ($secondInQueue) {
                $data['sections'][$section]['second_in_queue'] = $secondInQueue->queue_number;
                $data['sections'][$section]['second_in_queue_category'] = $secondInQueue->category ? $secondInQueue->category->code : null;
            }
            
            $data['sections'][$section]['waiting_count'] += $waitingCount;
        }

        return response()->json($data);
    }

    /**
     * Get queue data for specific category
     */
    public function categoryData(Category $category)
    {
        $nowServing = Inquiry::today()
            ->byCategory($category->id)
            ->where('status', 'serving')
            ->with('servedBy')
            ->first();

        $waiting = Inquiry::today()
            ->byCategory($category->id)
            ->waiting()
            ->orderBy('created_at')
            ->take(5)
            ->get();

        $recentlyCalled = Inquiry::today()
            ->byCategory($category->id)
            ->whereIn('status', ['completed', 'skipped'])
            ->latest('completed_at')
            ->take(3)
            ->get();

        return response()->json([
            'category' => $category,
            'now_serving' => $nowServing,
            'waiting' => $waiting,
            'recently_called' => $recentlyCalled,
        ]);
    }

    /**
     * Get announcements or notifications
     */
    public function announcements()
    {
        // This can be extended to show announcements
        return response()->json([
            'announcements' => [],
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}
