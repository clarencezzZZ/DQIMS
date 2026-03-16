<?php

/**
 * Add test data to database
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Adding Test Inquiry Data ===\n\n";

try {
    // Get DB connection
    $db = DB::connection();
    echo "✓ Connected to database: " . $db->getDatabaseName() . "\n\n";
    
    // Read SQL file
    $sql = file_get_contents(__DIR__ . '/add_test_inquiries.sql');
    
    // Execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    $executed = 0;
    
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }
        
        try {
            DB::statement($statement);
            $executed++;
        } catch (Exception $e) {
            // Skip errors (likely duplicate entries)
        }
    }
    
    echo "✓ Executed $executed SQL statements\n\n";
    
    // Count inquiries
    $marchCount = DB::table('inquiries')
        ->whereBetween('date', ['2026-03-01', '2026-03-31'])
        ->count();
    
    $totalCount = DB::table('inquiries')->count();
    
    echo "=== Results ===\n";
    echo "Total inquiries in database: $totalCount\n";
    echo "Inquiries in March 2026: $marchCount\n\n";
    
    if ($marchCount > 0) {
        echo "✅ SUCCESS! Now you can export March 2026 report!\n";
        echo "\nTry these exports:\n";
        echo "  - March 1-31, 2026 (Monthly)\n";
        echo "  - March 5, 2026 (Juan Dela Cruz)\n";
        echo "  - March 10, 2026 (Maria Santos)\n";
    } else {
        echo "⚠ No inquiries added. They might already exist.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n";
