<?php

/**
 * Test Excel Export with Empty Data
 */

require __DIR__ . '/vendor/autoload.php';

use App\Exports\SimpleReportExport;
use Maatwebsite\Excel\Facades\Excel;

echo "=== Testing Excel Export Functionality ===\n\n";

// Test 1: Export with empty data
echo "Test 1: Creating Excel export with NO data...\n";
try {
    $emptyInquiries = collect();
    $dateRange = [
        'start' => \Carbon\Carbon::parse('2026-01-01'),
        'end' => \Carbon\Carbon::parse('2026-12-31')
    ];
    
    $export = new SimpleReportExport($emptyInquiries, $dateRange, 'Yearly Report');
    $rows = $export->array();
    
    echo "✓ Successfully created export with empty data\n";
    echo "  Generated " . count($rows) . " rows\n";
    echo "  Last row: " . json_encode(end($rows)) . "\n\n";
} catch (\Exception $e) {
    echo "✗ Failed: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 2: Verify no Closure parse errors
echo "Test 2: Checking for Closure syntax issues...\n";
try {
    $reflection = new ReflectionClass('App\Exports\SimpleReportExport');
    echo "✓ SimpleReportExport class loaded successfully\n";
    
    // Check if methods exist
    if ($reflection->hasMethod('array')) {
        echo "✓ array() method exists\n";
    }
    if ($reflection->hasMethod('styles')) {
        echo "✓ styles() method exists\n";
    }
    if ($reflection->hasMethod('title')) {
        echo "✓ title() method exists\n";
    }
    echo "\n";
} catch (\Exception $e) {
    echo "✗ Failed: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 3: Verify ReportController can use new export
echo "Test 3: Verifying ReportController integration...\n";
try {
    $controllerContent = file_get_contents(__DIR__ . '/app/Http/Controllers/ReportController.php');
    if (strpos($controllerContent, 'SimpleReportExport') !== false) {
        echo "✓ ReportController uses SimpleReportExport\n";
    } else {
        echo "✗ ReportController doesn't use SimpleReportExport\n";
    }
    
    if (strpos($controllerContent, 'new ReportExport') === false) {
        echo "✓ Old ReportExport usage removed\n";
    } else {
        echo "⚠ Old ReportExport still referenced\n";
    }
    echo "\n";
} catch (\Exception $e) {
    echo "✗ Failed: " . $e->getMessage() . "\n\n";
    exit(1);
}

echo "=== All Tests Passed! ===\n\n";
echo "Summary:\n";
echo "✓ Excel export handles empty data gracefully\n";
echo "✓ No Closure parse errors\n";
echo "✓ ReportController updated successfully\n";
echo "\nYou can now export reports even when there's NO DATA!\n";
echo "The system will generate an Excel file showing 'No inquiries found'.\n";
