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
                'name' => $section,
                'categories' => $categories,
                'color' => $this->getSectionColor($section),
            ];
        }
        
        return view('monitor.lobby', compact('sectionData'));
    }
    
    /**
     * Display lobby1 monitor (SCS and LES only)
     */
    public function lobby1()
    {
        // Get only SCS and LES sections
        $sections = ['SCS', 'LES'];
        
        $sectionData = [];
        foreach ($sections as $section) {
            $categories = Category::where('section', $section)->where('is_active', true)->get();
            $sectionData[$section] = [
                'name' => $section,
                'categories' => $categories,
                'color' => $this->getSectionColor($section),
            ];
        }
        
        return view('monitor.lobby1', compact('sectionData'));
    }
    
    /**
     * Display lobby2 monitor (ACS and OOSS only)
     */
    public function lobby2()
    {
        // Get only ACS and OOSS sections
        $sections = ['ACS', 'OOSS'];
        
        $sectionData = [];
        foreach ($sections as $section) {
            $categories = Category::where('section', $section)->where('is_active', true)->get();
            $sectionData[$section] = [
                'name' => $section,
                'categories' => $categories,
                'color' => $this->getSectionColor($section),
            ];
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

            // Get latest waiting inquiry
            $latestWaiting = Inquiry::today()
                ->whereIn('category_id', $categories)
                ->where('status', 'waiting')
                ->with('category')
                ->oldest('created_at')
                ->first();

            $data['sections'][$section] = [
                'section' => $section,
                'color' => $this->getSectionColor($section),
                'now_serving' => $nowServing ? $nowServing->queue_number : null,
                'now_serving_category' => $nowServing && $nowServing->category ? $nowServing->category->code : null,
                'latest_waiting' => $latestWaiting ? $latestWaiting->queue_number : null,
                'latest_waiting_category' => $latestWaiting && $latestWaiting->category ? $latestWaiting->category->code : null,
                'waiting_count' => $waitingCount,
            ];
        }

        return response()->json($data);
    }
    
    /**
     * Get current queue data for lobby1 monitor (SCS and LES only)
     */
    public function queueDataLobby1()
    {
        $sections = ['SCS', 'LES'];
        
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

            // Get latest waiting inquiry
            $latestWaiting = Inquiry::today()
                ->whereIn('category_id', $categories)
                ->where('status', 'waiting')
                ->with('category')
                ->oldest('created_at')
                ->first();

            $data['sections'][$section] = [
                'section' => $section,
                'color' => $this->getSectionColor($section),
                'now_serving' => $nowServing ? $nowServing->queue_number : null,
                'now_serving_category' => $nowServing && $nowServing->category ? $nowServing->category->code : null,
                'latest_waiting' => $latestWaiting ? $latestWaiting->queue_number : null,
                'latest_waiting_category' => $latestWaiting && $latestWaiting->category ? $latestWaiting->category->code : null,
                'waiting_count' => $waitingCount,
            ];
        }

        return response()->json($data);
    }
    
    /**
     * Get current queue data for lobby2 monitor (ACS and OOSS only)
     */
    public function queueDataLobby2()
    {
        $sections = ['ACS', 'OOSS'];
        
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

            // Get latest waiting inquiry
            $latestWaiting = Inquiry::today()
                ->whereIn('category_id', $categories)
                ->where('status', 'waiting')
                ->with('category')
                ->oldest('created_at')
                ->first();

            $data['sections'][$section] = [
                'section' => $section,
                'color' => $this->getSectionColor($section),
                'now_serving' => $nowServing ? $nowServing->queue_number : null,
                'now_serving_category' => $nowServing && $nowServing->category ? $nowServing->category->code : null,
                'latest_waiting' => $latestWaiting ? $latestWaiting->queue_number : null,
                'latest_waiting_category' => $latestWaiting && $latestWaiting->category ? $latestWaiting->category->code : null,
                'waiting_count' => $waitingCount,
            ];
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
