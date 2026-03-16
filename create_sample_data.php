<?php

/**
 * Create sample test data for testing Excel export
 */

require __DIR__ . '/vendor/autoload.php';

use App\Models\Inquiry;
use App\Models\Category;
use Carbon\Carbon;

echo "=== Creating Sample Test Data ===\n\n";

// Get active categories
$categories = Category::where('is_active', true)->get();

if ($categories->isEmpty()) {
    echo "❌ No active categories found! Please seed categories first.\n";
    exit(1);
}

echo "Found {$categories->count()} active category/categories.\n\n";

// Create sample inquiries for today and past dates
$sampleData = [
    ['name' => 'Juan Dela Cruz', 'contact' => '09171234567', 'address' => 'Manila', 'days_ago' => 0],
    ['name' => 'Maria Santos', 'contact' => '09181234567', 'address' => 'Quezon City', 'days_ago' => 1],
    ['name' => 'Pedro Reyes', 'contact' => '09191234567', 'address' => 'Makati', 'days_ago' => 2],
    ['name' => 'Ana Garcia', 'contact' => '09201234567', 'address' => 'Pasig', 'days_ago' => 3],
    ['name' => 'Jose Ramos', 'contact' => '09211234567', 'address' => 'Taguig', 'days_ago' => 5],
];

$created = 0;
foreach ($sampleData as $index => $data) {
    $category = $categories->random();
    $date = Carbon::today()->subDays($data['days_ago']);
    
    // Check if inquiry already exists for this date
    $exists = Inquiry::where('name', $data['name'])
                     ->where('date', $date->toDateString())
                     ->exists();
    
    if ($exists) {
        echo "⚠ Skipping {$data['name']} (already exists)\n";
        continue;
    }
    
    $inquiry = new Inquiry();
    $inquiry->category_id = $category->id;
    $inquiry->name = $data['name'];
    $inquiry->address = $data['address'];
    $inquiry->request_type = rand(0, 1) ? 'walk-in' : 'online';
    $inquiry->priority = rand(0, 2) ? 'normal' : 'high';
    $inquiry->status = 'completed';
    $inquiry->date = $date->toDateString();
    $inquiry->queue_number = 'Q-' . rand(1000, 9999);
    $inquiry->served_by = 2; // Admin user
    $inquiry->served_at = $date->copy()->addHours(2);
    $inquiry->completed_at = $date->copy()->addHours(3);
    
    if ($inquiry->save()) {
        echo "✓ Created inquiry for {$data['name']} (Date: {$date->toDateString()}, Status: completed)\n";
        $created++;
    } else {
        echo "✗ Failed to create inquiry for {$data['name']}\n";
    }
}

echo "\n=== Summary ===\n";
echo "Created: $created new inquiries\n";
echo "\nYou can now export reports for these dates!\n";
echo "Try date ranges from " . Carbon::today()->subDays(5)->toDateString() . " to " . Carbon::today()->toDateString() . "\n";
