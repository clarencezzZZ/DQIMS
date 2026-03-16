<?php

/**
 * Check what inquiry data exists in the database
 */

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Inquiry;
use App\Models\Category;

echo "=== Checking Database for Inquiries ===\n\n";

// Get total count
$total = Inquiry::count();
echo "Total inquiries in database: $total\n\n";

if ($total > 0) {
    // Show date range
    $oldest = Inquiry::orderBy('date', 'asc')->first();
    $newest = Inquiry::orderBy('date', 'desc')->first();
    
    echo "Date range: {$oldest->date} to {$newest->date}\n\n";
    
    // Show breakdown by status
    echo "By Status:\n";
    $byStatus = Inquiry::selectRaw('status, COUNT(*) as count')
        ->groupBy('status')
        ->get();
    foreach ($byStatus as $item) {
        echo "  - {$item->status}: {$item->count}\n";
    }
    
    echo "\nLast 5 inquiries:\n";
    $recent = Inquiry::with('category')->orderBy('date', 'desc')->limit(5)->get();
    foreach ($recent as $inq) {
        $categoryName = $inq->category ? $inq->category->name : 'N/A';
        echo "  Date: {$inq->date} | Name: {$inq->name} | Category: $categoryName | Status: {$inq->status}\n";
    }
    
    // Check March 2026 specifically
    echo "\nMarch 2026 inquiries:\n";
    $marchCount = Inquiry::whereBetween('date', ['2026-03-01', '2026-03-31'])->count();
    echo "  Total in March 2026: $marchCount\n";
    
} else {
    echo "❌ NO INQUIRIES FOUND IN DATABASE!\n\n";
    
    // Check if categories exist
    $categoryCount = Category::count();
    echo "Categories in database: $categoryCount\n";
    
    if ($categoryCount > 0) {
        echo "\nAvailable categories:\n";
        $categories = Category::all();
        foreach ($categories as $cat) {
            echo "  - {$cat->name} (Code: {$cat->code})\n";
        }
    } else {
        echo "❌ NO CATEGORIES EITHER!\n";
    }
}

echo "\n";
