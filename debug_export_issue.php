<?php

/**
 * Debug why export shows no data
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Inquiry;

echo "=== Debugging Export Issue ===\n\n";

// Check March 2026 data
$dateRange = [
    'start' => \Carbon\Carbon::parse('2026-03-01'),
    'end' => \Carbon\Carbon::parse('2026-03-31')
];

echo "Date Range: {$dateRange['start']->format('Y-m-d')} to {$dateRange['end']->format('Y-m-d')}\n\n";

// Query exactly like ReportController does
$query = Inquiry::whereBetween('date', [$dateRange['start']->toDateString(), $dateRange['end']->toDateString()]);

$total = $query->count();
echo "Total inquiries in date range: $total\n\n";

if ($total > 0) {
    // Get the actual inquiries
    $inquiries = $query->with(['category', 'servedBy'])->get();
    
    echo "Status breakdown:\n";
    $byStatus = $inquiries->groupBy('status');
    foreach ($byStatus as $status => $items) {
        echo "  - $status: " . $items->count() . "\n";
    }
    
    echo "\nFirst 5 inquiries:\n";
    foreach ($inquiries->take(5) as $inq) {
        $categoryName = $inq->category ? $inq->category->name : 'N/A';
        echo "  Date: {$inq->date} | Name: {$inq->name} | Category: $categoryName | Status: {$inq->status}\n";
    }
    
    echo "\n✅ DATA EXISTS! The export should work.\n";
    echo "\nPossible issue: Your browser might be showing cached error message.\n";
    echo "Try:\n";
    echo "  1. Hard refresh (Ctrl+F5)\n";
    echo "  2. Clear browser cache\n";
    echo "  3. Try a different browser\n";
    echo "  4. Check if status filter is set to 'completed' only\n";
    
} else {
    echo "❌ No inquiries found. Check your database.\n";
}

echo "\n";
