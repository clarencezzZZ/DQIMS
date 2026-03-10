<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Inquiry;
use App\Models\Category;

echo "=== WHO IS SERVING? ===\n\n";

$today = now()->toDateString();
echo "Today: {$today}\n\n";

// Check who's serving in Aggregate and Correction Section
echo "1. CHECKING AGGREGATE AND CORRECTION SECTION:\n";
$serving = Inquiry::today()
    ->join('categories', 'inquiries.category_id', '=', 'categories.id')
    ->where('categories.section', 'Aggregate and Correction Section')
    ->where('inquiries.status', 'serving')
    ->select('inquiries.*')
    ->first();

if ($serving) {
    echo "   Currently Serving:\n";
    echo "     ID: {$serving->id}\n";
    echo "     Queue #: {$serving->queue_number}\n";
    echo "     Name: {$serving->guest_name}\n";
    echo "     Status: {$serving->status}\n";
    echo "     Priority: {$serving->priority}\n";
} else {
    echo "   ❌ NO ONE CURRENTLY SERVING\n";
}

echo "\n2. WAITING INQUIRIES IN SAME SECTION:\n";
$waiting = Inquiry::today()
    ->join('categories', 'inquiries.category_id', '=', 'categories.id')
    ->where('categories.section', 'Aggregate and Correction Section')
    ->where('inquiries.status', 'waiting')
    ->select('inquiries.*')
    ->orderBy('inquiries.created_at')
    ->get();

foreach ($waiting as $w) {
    echo "   - #{$w->queue_number} (ID: {$w->id}) | {$w->guest_name} | {$w->priority} | Created: {$w->created_at->format('H:i:s')}\n";
}

echo "\n3. LAST COMPLETED IN THIS SECTION:\n";
$lastCompleted = Inquiry::today()
    ->join('categories', 'inquiries.category_id', '=', 'categories.id')
    ->where('categories.section', 'Aggregate and Correction Section')
    ->where('inquiries.status', 'completed')
    ->select('inquiries.*')
    ->orderBy('inquiries.completed_at', 'desc')
    ->first();

if ($lastCompleted) {
    echo "   Last Completed:\n";
    echo "     Queue #: {$lastCompleted->queue_number}\n";
    echo "     Name: {$lastCompleted->guest_name}\n";
    echo "     Priority: {$lastCompleted->priority}\n";
    echo "     Completed At: {$lastCompleted->completed_at->format('H:i:s')}\n";
} else {
    echo "   No completed inquiries today\n";
}

echo "\n4. JOSHUA'S INQUIRY (ID: 65) DETAILS:\n";
$joshua = Inquiry::find(65);
if ($joshua) {
    echo "   Queue #: {$joshua->queue_number}\n";
    echo "   Status: {$joshua->status}\n";
    echo "   Priority: {$joshua->priority}\n";
    echo "   Category ID: {$joshua->category_id}\n";
    echo "   Created: {$joshua->created_at->format('Y-m-d H:i:s')}\n";
    
    if ($joshua->category) {
        echo "   Category Section: {$joshua->category->section}\n";
    }
}

echo "\n5. NEXT INQUIRY LOGIC TEST:\n";
// Simulate what controller does
$category = Category::find($joshua->category_id);
$section = $category->section;

$waitingInquiries = Inquiry::today()
    ->join('categories', 'inquiries.category_id', '=', 'categories.id')
    ->where('categories.section', $section)
    ->where('inquiries.status', 'waiting')
    ->select('inquiries.*')
    ->orderBy('inquiries.created_at')
    ->get();

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

echo "   Section: {$section}\n";
echo "   Waiting count: {$waitingInquiries->count()}\n";
echo "   Currently serving: " . ($currentlyServing ? "#{$currentlyServing->queue_number}" : "NONE") . "\n";
echo "   Last served: " . ($lastServedInquiry ? "#{$lastServedInquiry->queue_number} ({$lastServedInquiry->priority})" : "NONE") . "\n";

// Determine next
$priorityInquiries = $waitingInquiries->filter(fn($i) => $i->priority === 'priority');
$normalInquiries = $waitingInquiries->filter(fn($i) => $i->priority === 'normal');

$lastServedType = null;
if ($currentlyServing) {
    $lastServedType = $currentlyServing->priority;
} elseif ($lastServedInquiry) {
    $lastServedType = $lastServedInquiry->priority;
}

echo "   Last served type: " . ($lastServedType ?? 'none') . "\n";
echo "   Priority waiting: {$priorityInquiries->count()}\n";
echo "   Normal waiting: {$normalInquiries->count()}\n";

// Who should be next?
if ($priorityInquiries->isEmpty()) {
    $next = $normalInquiries->first();
    echo "   → Should be next: #{$next->queue_number} (NORMAL - only option)\n";
} elseif ($normalInquiries->isEmpty()) {
    $next = $priorityInquiries->first();
    echo "   → Should be next: #{$next->queue_number} (PRIORITY - only option)\n";
} elseif ($lastServedType === null) {
    $next = $normalInquiries->first();
    echo "   → Should be next: #{$next->queue_number} (NORMAL - starting fresh)\n";
} elseif ($lastServedType === 'priority') {
    $next = $normalInquiries->first();
    echo "   → Should be next: #{$next->queue_number} (NORMAL - after PRIORITY)\n";
} else {
    $next = $priorityInquiries->first();
    echo "   → Should be next: #{$next->queue_number} (PRIORITY - after NORMAL)\n";
}

echo "\n   Joshua is first waiting? " . ($waitingInquiries->first()->id == 65 ? "YES ✅" : "NO ❌") . "\n";
echo "   Joshua should be next? " . ($next && $next->id == 65 ? "YES ✅" : "NO ❌") . "\n";

?>
