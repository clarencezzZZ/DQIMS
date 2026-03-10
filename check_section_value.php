<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Inquiry;
use App\Models\Category;

echo "=== DIRECT DATABASE CHECK ===\n\n";

// Get Joshua's first inquiry (ID: 65)
$joshua = Inquiry::find(65);

if (!$joshua) {
    echo "❌ Inquiry ID 65 not found!\n";
    exit;
}

echo "Joshua's Inquiry:\n";
echo "  ID: {$joshua->id}\n";
echo "  Queue #: {$joshua->queue_number}\n";
echo "  Guest Name: {$joshua->guest_name}\n";
echo "  Category ID: {$joshua->category_id}\n";
echo "  Status: {$joshua->status}\n";
echo "  Priority: {$joshua->priority}\n\n";

// Get the category
$category = Category::find($joshua->category_id);

if (!$category) {
    echo "❌ Category ID {$joshua->category_id} not found!\n";
    exit;
}

echo "Category Details:\n";
echo "  ID: {$category->id}\n";
echo "  Code: {$category->code}\n";
echo "  Name: {$category->name}\n";
echo "  Section: [{$category->section}]\n";
echo "  Section Length: " . strlen($category->section) . "\n\n";

// Check if this matches what's in nextInquiries
$expectedSectionKey = $category->section;
echo "Expected nextInquiries key: [{$expectedSectionKey}]\n\n";

// Simulate what controller builds
$nextInquiries = ["Aggregate and Correction Section" => 65];
echo "Simulated nextInquiries: " . json_encode($nextInquiries) . "\n\n";

// Check match
$doesMatch = ($expectedSectionKey === "Aggregate and Correction Section");
echo "Does section match 'Aggregate and Correction Section'? " . ($doesMatch ? "YES ✅" : "NO ❌") . "\n";
echo "Actual comparison: [{$expectedSectionKey}] === [Aggregate and Correction Section]\n";

if (!$doesMatch) {
    echo "\n⚠️  MISMATCH DETECTED!\n";
    echo "The section field might have extra spaces or different casing.\n";
    
    // Try trimming
    $trimmed = trim($expectedSectionKey);
    echo "After trim: [{$trimmed}]\n";
    echo "Trimmed matches? " . ($trimmed === "Aggregate and Correction Section" ? "YES" : "NO") . "\n";
}

?>
