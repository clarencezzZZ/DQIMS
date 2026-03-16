<?php

use Illuminate\Database\Seeder;
use App\Models\Inquiry;
use App\Models\Category;
use Carbon\Carbon;

class AddTestDataSeeder extends Seeder
{
    public function run()
    {
        echo "=== Adding Test Inquiry Data ===\n\n";
        
        // Get categories
        $categories = Category::where('is_active', true)->get();
        
        if ($categories->isEmpty()) {
            echo "❌ No active categories found! Please run: php artisan db:seed --class=CategorySeeder\n";
            return;
        }
        
        echo "Found {$categories->count()} active category/categories.\n\n";
        
        // Sample data for March 2026
        $sampleData = [
            ['name' => 'Juan Dela Cruz', 'address' => 'Manila', 'date' => '2026-03-05', 'status' => 'completed'],
            ['name' => 'Maria Santos', 'address' => 'Quezon City', 'date' => '2026-03-10', 'status' => 'completed'],
            ['name' => 'Pedro Reyes', 'address' => 'Makati', 'date' => '2026-03-15', 'status' => 'completed'],
            ['name' => 'Ana Garcia', 'address' => 'Pasig', 'date' => '2026-03-20', 'status' => 'waiting'],
            ['name' => 'Jose Ramos', 'address' => 'Taguig', 'date' => '2026-03-25', 'status' => 'completed'],
            ['name' => 'Linda Flores', 'address' => ' Mandaluyong', 'date' => '2026-03-28', 'status' => 'skipped'],
        ];
        
        $created = 0;
        foreach ($sampleData as $data) {
            // Check if already exists
            $exists = Inquiry::where('name', $data['name'])
                           ->where('date', $data['date'])
                           ->exists();
            
            if ($exists) {
                echo "⚠ Skipping {$data['name']} (already exists)\n";
                continue;
            }
            
            $category = $categories->random();
            
            $inquiry = new Inquiry();
            $inquiry->category_id = $category->id;
            $inquiry->name = $data['name'];
            $inquiry->address = $data['address'];
            $inquiry->request_type = rand(0, 1) ? 'walk-in' : 'online';
            $inquiry->priority = rand(0, 2) ? 'normal' : 'high';
            $inquiry->status = $data['status'];
            $inquiry->date = $data['date'];
            $inquiry->queue_number = 'Q-' . rand(1000, 9999);
            $inquiry->served_by = 2; // Admin user
            
            if ($data['status'] === 'completed') {
                $inquiry->served_at = Carbon::parse($data['date'])->addHours(2);
                $inquiry->completed_at = Carbon::parse($data['date'])->addHours(3);
            }
            
            if ($inquiry->save()) {
                echo "✓ Created: {$data['name']} ({$data['date']}, {$data['status']})\n";
                $created++;
            } else {
                echo "✗ Failed to create: {$data['name']}\n";
            }
        }
        
        echo "\n=== Summary ===\n";
        echo "Created: $created new inquiries\n";
        echo "\nNow try exporting March 2026 report again!\n";
    }
}

// Run the seeder
echo "Running test data seeder...\n\n";
$app = app();
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$seeder = new AddTestDataSeeder();
$seeder->run();
