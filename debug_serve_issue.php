<?php

/**
 * Debug script to diagnose why serve button is disabled for first queue
 * 
 * Run this via browser: http://localhost/DQIMS/debug_serve_issue.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Inquiry;
use App\Models\Category;

echo "<h1>Serve Button Debug Diagnostic</h1>";
echo "<p><strong>Date:</strong> " . now()->format('Y-m-d H:i:s') . "</p>";

// Test 1: Check waiting inquiries
echo "<h2>1. Waiting Inquiries Today</h2>";
$todayStr = now()->toDateString();
$waitingInquiries = Inquiry::whereDate('date', $todayStr)
    ->where('status', 'waiting')
    ->orderBy('created_at')
    ->get();

if ($waitingInquiries->isEmpty()) {
    echo "<p style='color: red;'>⚠️ NO WAITING INQUIRIES FOUND! This is why all serve buttons are disabled.</p>";
} else {
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr>
            <th>ID</th>
            <th>Queue #</th>
            <th>Guest Name</th>
            <th>Category ID</th>
            <th>Category Section</th>
            <th>Priority</th>
            <th>Created At</th>
          </tr>";
    
    foreach ($waitingInquiries as $inq) {
        $section = $inq->category ? $inq->category->section : 'NO CATEGORY';
        echo "<tr>";
        echo "<td>{$inq->id}</td>";
        echo "<td>{$inq->queue_number}</td>";
        echo "<td>{$inq->guest_name}</td>";
        echo "<td>" . ($inq->category_id ?? 'NULL') . "</td>";
        echo "<td>{$section}</td>";
        echo "<td>{$inq->priority}</td>";
        echo "<td>{$inq->created_at->format('Y-m-d H:i:s')}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Test 2: Check sections and next inquiries
echo "<h2>2. Section Analysis (Simulating Controller Logic)</h2>";

$categories = Category::where('is_active', true)->get();
$sectionNames = [
    'ACS' => 'AGGREGATE AND CORRECTION',
    'OOSS' => 'ORIGINAL AND OTHER SURVEYS',
    'LES' => 'LAND EVALUATION',
    'SCS' => 'SURVEYS AND CONTROL',
];

$processedSections = [];
$nextInquiries = [];

foreach ($categories as $category) {
    $section = $category->section;
    
    if (!isset($processedSections[$section])) {
        $sectionFullName = isset($sectionNames[$section]) ? $sectionNames[$section] : 'Unknown';
        echo "<h3>Section: {$section} ({$sectionFullName})</h3>";
        
        // Get waiting inquiries for this section
        $sectionWaiting = Inquiry::today()
            ->join('categories', 'inquiries.category_id', '=', 'categories.id')
            ->where('categories.section', $section)
            ->where('inquiries.status', 'waiting')
            ->select('inquiries.*')
            ->orderBy('inquiries.created_at')
            ->get();
        
        echo "<p>Waiting inquiries: <strong>{$sectionWaiting->count()}</strong></p>";
        
        if ($sectionWaiting->isEmpty()) {
            echo "<p style='color: orange;'>⚠️ No waiting inquiries in this section</p>";
        } else {
            // Separate by priority
            $priority = $sectionWaiting->filter(fn($i) => $i->priority === 'priority');
            $normal = $sectionWaiting->filter(fn($i) => $i->priority === 'normal');
            
            echo "<ul>";
            echo "<li>Normal: {$normal->count()}</li>";
            echo "<li>Priority: {$priority->count()}</li>";
            echo "</ul>";
            
            // Determine next
            $lastServedType = null;
            $currentlyServing = Inquiry::today()
                ->join('categories', 'inquiries.category_id', '=', 'categories.id')
                ->where('categories.section', $section)
                ->where('inquiries.status', 'serving')
                ->select('inquiries.*')
                ->first();
                
            $lastCompleted = Inquiry::today()
                ->join('categories', 'inquiries.category_id', '=', 'categories.id')
                ->where('categories.section', $section)
                ->where('inquiries.status', 'completed')
                ->select('inquiries.*')
                ->orderBy('inquiries.completed_at', 'desc')
                ->first();
            
            if ($currentlyServing) {
                $lastServedType = $currentlyServing->priority;
                echo "<p>Currently serving: <strong>#{$currentlyServing->queue_number}</strong> ({$currentlyServing->priority})</p>";
            } elseif ($lastCompleted) {
                $lastServedType = $lastCompleted->priority;
                echo "<p>Last completed: <strong>#{$lastCompleted->queue_number}</strong> ({$lastCompleted->priority})</p>";
            } else {
                echo "<p>No one currently serving or completed today</p>";
            }
            
            // Apply algorithm
            $next = null;
            if ($priority->isEmpty()) {
                $next = $normal->first();
                echo "<p style='color: green;'>✅ NEXT: <strong>#{$next->queue_number}</strong> (NORMAL - only option)</p>";
            } elseif ($normal->isEmpty()) {
                $next = $priority->first();
                echo "<p style='color: green;'>✅ NEXT: <strong>#{$next->queue_number}</strong> (PRIORITY - only option)</p>";
            } elseif ($lastServedType === null) {
                $next = $normal->first();
                echo "<p style='color: green;'>✅ NEXT: <strong>#{$next->queue_number}</strong> (NORMAL - starting fresh)</p>";
            } elseif ($lastServedType === 'priority') {
                $next = $normal->first();
                echo "<p style='color: green;'>✅ NEXT: <strong>#{$next->queue_number}</strong> (NORMAL - after PRIORITY)</p>";
            } else {
                $next = $priority->first();
                echo "<p style='color: green;'>✅ NEXT: <strong>#{$next->queue_number}</strong> (PRIORITY - after NORMAL)</p>";
            }
            
            if ($next) {
                $nextInquiries[$section] = $next->id;
                echo "<p><strong>📌 STORED: nextInquiries['{$section}'] = {$next->id}</strong></p>";
            }
        }
        
        $processedSections[$section] = true;
    }
}

// Test 3: Show final nextInquiries array
echo "<h2>3. Final Next Inquiries Array (What View Will Use)</h2>";
echo "<pre style='background: #f5f5f5; padding: 15px; border-left: 4px solid #007bff;'>";
print_r($nextInquiries);
echo "</pre>";

// Test 4: Check for potential key mismatch
echo "<h2>4. Potential Key Mismatch Check</h2>";
echo "<p>The view checks: <code>\$nextInquiries[\$sectionAcronym]</code> where sectionAcronym comes from <code>\$inquiry->category->section</code></p>";

if (!empty($nextInquiries)) {
    echo "<p>✅ Next inquiries array has " . count($nextInquiries) . " section(s)</p>";
    
    // Check first waiting inquiry
    if ($waitingInquiries->isNotEmpty()) {
        $firstInquiry = $waitingInquiries->first();
        $sectionKey = $firstInquiry->category ? $firstInquiry->category->section : null;
        $isNext = isset($nextInquiries[$sectionKey]) && $nextInquiries[$sectionKey] == $firstInquiry->id;
        
        echo "<div style='background: #e7f3ff; padding: 15px; border-left: 4px solid #007bff;'>";
        echo "<h4>First Queue Analysis (#{$firstInquiry->queue_number}):</h4>";
        echo "<ul>";
        echo "<li>Section Key: <strong>{$sectionKey}</strong></li>";
        echo "<li>Inquiry ID: <strong>{$firstInquiry->id}</strong></li>";
        echo "<li>Expected Next ID for this section: <strong>" . ($nextInquiries[$sectionKey] ?? 'NOT SET') . "</strong></li>";
        echo "<li>Is Next? <strong>" . ($isNext ? '✅ YES' : '❌ NO') . "</strong></li>";
        echo "</ul>";
        
        if (!$isNext) {
            echo "<p style='color: red;'><strong>⚠️ PROBLEM DETECTED!</strong> The first queue is NOT marked as next.</p>";
            echo "<p>This could be caused by:</p>";
            echo "<ol>";
            echo "<li>Section field not populated in categories table</li>";
            echo "<li>Mismatch between section acronym used as array key</li>";
            echo "<li>Algorithm logic issue</li>";
            echo "</ol>";
        } else {
            echo "<p style='color: green;'><strong>✅ LOOKS GOOD!</strong> The first queue should have an enabled serve button.</p>";
        }
        echo "</div>";
    }
} else {
    echo "<p style='color: red;'>❌ Next inquiries array is EMPTY! This is the problem.</p>";
}

// Test 5: Check category sections
echo "<h2>5. Category Sections Verification</h2>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>ID</th><th>Code</th><th>Name</th><th>Section</th><th>Is Active</th></tr>";
foreach ($categories as $cat) {
    echo "<tr>";
    echo "<td>{$cat->id}</td>";
    echo "<td>{$cat->code}</td>";
    echo "<td>{$cat->name}</td>";
    echo "<td><strong>{$cat->section}</strong></td>";
    echo "<td>" . ($cat->is_active ? 'Yes' : 'No') . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<hr>";
echo "<h2>Recommendations</h2>";
echo "<ol>";
echo "<li>Check Laravel logs at <code>storage/logs/laravel.log</code> for detailed debug output</li>";
echo "<li>Verify that the section field in categories table matches what's expected (ACS, OOSS, LES, SCS)</li>";
echo "<li>Ensure the date field on inquiries matches today's date</li>";
echo "<li>Check if there are any filters active on the page (search, category, status)</li>";
echo "</ol>";

?>
