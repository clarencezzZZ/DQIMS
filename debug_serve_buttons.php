<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Category;
use App\Models\Inquiry;

echo "=== DIAGNOSTIC TEST FOR SERVE BUTTONS ===\n\n";

// Test 1: Check Categories
echo "1. CHECKING CATEGORIES:\n";
$categories = Category::where('is_active', true)->get();
echo "   Active Categories: {$categories->count()}\n";
foreach ($categories as $cat) {
    echo "   - ID: {$cat->id}, Name: {$cat->name}, Section: {$cat->section}\n";
}
echo "\n";

// Test 2: Check Today's Inquiries
echo "2. CHECKING TODAY'S INQUIRIES:\n";
$today = now()->toDateString();
$inquiries = Inquiry::whereDate('date', $today)->get();
echo "   Total Today: {$inquiries->count()}\n";

$statusCounts = $inquiries->groupBy('status')->map(function($q) {
    return $q->count();
});
foreach ($statusCounts as $status => $count) {
    echo "   - {$status}: {$count}\n";
}
echo "\n";

// Test 3: Check Waiting Inquiries by Section
echo "3. WAITING INQUIRIES BY SECTION:\n";
$waitingBySection = [];
$waiting = Inquiry::whereDate('date', $today)->where('status', 'waiting')->get();
foreach ($waiting as $inq) {
    if ($inq->category) {
        $section = $inq->category->section;
        if (!isset($waitingBySection[$section])) {
            $waitingBySection[$section] = [];
        }
        $waitingBySection[$section][] = $inq;
    }
}

if (empty($waitingBySection)) {
    echo "   ⚠️  NO WAITING INQUIRIES FOUND!\n";
    echo "   This is why serve buttons are disabled.\n";
} else {
    foreach ($waitingBySection as $section => $inqs) {
        echo "   Section {$section}: " . count($inqs) . " waiting\n";
        foreach ($inqs as $inq) {
            echo "      - #{$inq->queue_number} (ID: {$inq->id}) - Priority: {$inq->priority} - Created: {$inq->created_at}\n";
        }
    }
}
echo "\n";

// Test 4: Simulate getNextInquiryByPriorityForAdmin Logic
echo "4. SIMULATING NEXT INQUIRY LOGIC:\n";
foreach ($waitingBySection as $section => $inquiries) {
    echo "   Section {$section}:\n";
    
    // Sort by created_at
    $sorted = collect($inquiries)->sortBy('created_at');
    
    // Separate by priority
    $priority = $sorted->filter(fn($i) => $i->priority === 'priority');
    $normal = $sorted->filter(fn($i) => $i->priority === 'normal');
    
    echo "      - Normal: {$normal->count()}, Priority: {$priority->count()}\n";
    
    // Determine next
    if ($priority->isEmpty()) {
        $next = $normal->first();
        echo "      → Next: #{$next->queue_number} (NORMAL - only option)\n";
    } elseif ($normal->isEmpty()) {
        $next = $priority->first();
        echo "      → Next: #{$next->queue_number} (PRIORITY - only option)\n";
    } else {
        // Both exist - would need to check last served
        $lastServedType = null; // Simplified - assume starting fresh
        if ($lastServedType === null) {
            $next = $normal->first();
            echo "      → Next: #{$next->queue_number} (NORMAL - starting fresh)\n";
        } elseif ($lastServedType === 'priority') {
            $next = $normal->first();
            echo "      → Next: #{$next->queue_number} (NORMAL - after PRIORITY)\n";
        } else {
            $next = $priority->first();
            echo "      → Next: #{$next->queue_number} (PRIORITY - after NORMAL)\n";
        }
    }
}
echo "\n";

// Test 5: Check Currently Serving
echo "5. CURRENTLY SERVING:\n";
$serving = Inquiry::whereDate('date', $today)->where('status', 'serving')->get();
if ($serving->isEmpty()) {
    echo "   No one currently serving\n";
} else {
    foreach ($serving as $inq) {
        $section = $inq->category ? $inq->category->section : 'Unknown';
        echo "   - Section {$section}: #{$inq->queue_number} ({$inq->priority})\n";
    }
}
echo "\n";

echo "=== END DIAGNOSTIC ===\n";
echo "\nNext Steps:\n";
echo "1. If NO waiting inquiries found → Create some test inquiries\n";
echo "2. If waiting inquiries exist → Check if their sections match categories\n";
echo "3. Visit /admin/inquiries and check storage/logs/laravel.log for detailed logs\n";
