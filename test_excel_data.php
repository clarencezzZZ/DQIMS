<?php

/**
 * Test Excel Export Data
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;

echo "=== Testing Excel Export Data ===\n\n";

// Simulate request
$request = new Request([
    'report_type' => 'custom',
    'date_from' => '2026-03-01',
    'date_to' => '2026-03-31',
    'status' => 'completed'
]);

$controller = new ReportController();

// Use reflection to call private methods
$reflection = new ReflectionClass($controller);

// Get date range
$getDateRangeMethod = $reflection->getMethod('getDateRange');
$getDateRangeMethod->setAccessible(true);
$dateRange = $getDateRangeMethod->invoke($controller, $request);

echo "Date Range: {$dateRange['start']->format('Y-m-d')} to {$dateRange['end']->format('Y-m-d')}\n\n";

// Get report data
$getReportDataMethod = $reflection->getMethod('getReportData');
$getReportDataMethod->setAccessible(true);
$data = $getReportDataMethod->invoke($controller, $dateRange, $request);

echo "Report Data:\n";
echo "  Total Inquiries: " . $data['inquiries']->count() . "\n";
echo "  Completed: " . $data['inquiries']->where('status', 'completed')->count() . "\n";
echo "  Assessments: " . ($data['assessments'] ? $data['assessments']->count() : 0) . "\n";
echo "  Category Stats: " . count($data['category_stats'] ?? []) . " categories\n\n";

if ($data['inquiries']->count() > 0) {
    echo "First 3 inquiries:\n";
    foreach ($data['inquiries']->take(3) as $inq) {
        $categoryName = $inq->category ? $inq->category->name : 'NO CATEGORY';
        echo "  - {$inq->queue_number} | {$inq->name} | {$categoryName} | {$inq->status}\n";
    }
} else {
    echo "❌ NO INQUIRIES IN DATA!\n";
    echo "\nChecking database directly...\n";
    
    $directInquiries = App\Models\Inquiry::whereBetween('date', ['2026-03-01', '2026-03-31'])
        ->where('status', 'completed')
        ->with('category')
        ->get();
    
    echo "Direct DB query found: " . $directInquiries->count() . " inquiries\n";
    if ($directInquiries->count() > 0) {
        echo "\nFirst 3 from DB:\n";
        foreach ($directInquiries->take(3) as $inq) {
            $catName = $inq->category ? $inq->category->name : 'NONE';
            echo "  - {$inq->queue_number} | {$inq->name} | {$catName}\n";
        }
    }
}

echo "\n";
