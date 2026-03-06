<?php

use Illuminate\Support\Facades\DB;
use App\Models\Assessment;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking all assessment numbers for 2026-03...\n\n";

// Get all assessments for March 2026
$assessments = Assessment::whereRaw("assessment_number LIKE '2026-03-%'")
    ->orderBy('assessment_number')
    ->get(['id', 'assessment_number']);

echo "Found " . $assessments->count() . " assessments:\n";
foreach ($assessments as $a) {
    echo "ID: {$a->id} => {$a->assessment_number}\n";
}

// Find the maximum sequence number
$maxNumber = 0;
foreach ($assessments as $a) {
    $parts = explode('-', $a->assessment_number);
    if (isset($parts[2])) {
        $num = intval($parts[2]);
        if ($num > $maxNumber) {
            $maxNumber = $num;
        }
    }
}

echo "\nMaximum sequence number found: {$maxNumber}\n";

// Update the sequence table
\App\Models\AssessmentSequence::updateOrCreate(
    ['year_month' => '2026'],
    ['current_value' => $maxNumber]
);

echo "Updated sequence table. Next assessment will be: 2026-03-" . str_pad($maxNumber + 1, 4, '0', STR_PAD_LEFT) . "\n";

// Check for duplicates
echo "\nChecking for duplicates...\n";
$duplicates = Assessment::selectRaw('assessment_number, COUNT(*) as count')
    ->groupBy('assessment_number')
    ->havingRaw('COUNT(*) > 1')
    ->get();

if ($duplicates->count() > 0) {
    echo "DUPLICATES FOUND:\n";
    foreach ($duplicates as $dup) {
        echo "  {$dup->assessment_number} appears {$dup->count} times\n";
    }
} else {
    echo "No duplicates found. All good!\n";
}

echo "\nDone!\n";
