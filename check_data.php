<?php

require __DIR__ . '/vendor/autoload.php';

echo "=== Checking Database for Inquiries ===\n\n";

// Check all inquiries in the selected date range
$inquiries = App\Models\Inquiry::whereBetween('date', ['2026-03-09', '2026-03-15'])->get();

echo "Total inquiries from 2026-03-09 to 2026-03-15: " . $inquiries->count() . "\n\n";

if ($inquiries->count() > 0) {
    echo "Breakdown by status:\n";
    $byStatus = $inquiries->groupBy('status');
    foreach ($byStatus as $status => $items) {
        echo "  - $status: " . $items->count() . "\n";
    }
    
    echo "\nCompleted inquiries details:\n";
    $completed = $inquiries->where('status', 'completed');
    foreach ($completed as $inq) {
        echo "  ID: {$inq->id} | Queue: {$inq->queue_number} | Name: {$inq->name} | Category: {$inq->category_id}\n";
    }
} else {
    echo "❌ No inquiries found in this date range!\n";
    echo "\nAvailable date ranges with data:\n";
    
    // Find what dates actually have data
    $allInquiries = App\Models\Inquiry::orderBy('date', 'desc')->limit(10)->get();
    if ($allInquiries->count() > 0) {
        foreach ($allInquiries as $inq) {
            echo "  Date: {$inq->date} | Status: {$inq->status} | Name: {$inq->name}\n";
        }
    }
}

echo "\n";
