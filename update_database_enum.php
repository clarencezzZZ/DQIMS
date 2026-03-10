<?php
/**
 * Update database enum values from section_staff to section_officer
 * 
 * Run via: php update_database_enum.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Updating Database Enum Values ===\n\n";

try {
    // First, modify the column to accept any string temporarily
    echo "Step 1: Modifying column to allow new values...\n";
    DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50)");
    echo "✅ Column type changed to VARCHAR(50)\n\n";
    
    // Update existing records
    echo "Step 2: Updating existing user roles...\n";
    $count = DB::table('users')->where('role', 'section_staff')->count();
    if ($count > 0) {
        DB::table('users')->where('role', 'section_staff')->update(['role' => 'section_officer']);
        echo "✅ Updated $count user(s)\n\n";
    } else {
        echo "No users to update\n\n";
    }
    
    // Change back to ENUM with new values
    echo "Step 3: Changing column back to ENUM with new values...\n";
    DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('front_desk', 'section_officer', 'admin') DEFAULT 'section_officer'");
    echo "✅ Column type changed back to ENUM with new values\n\n";
    
    echo "=== SUCCESS! ===\n";
    echo "The database has been updated successfully.\n";
    echo "New role values: front_desk, section_officer, admin\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nThis might fail if there are foreign key constraints.\n";
    echo "Alternative: Manually run this SQL:\n";
    echo "ALTER TABLE users MODIFY COLUMN role VARCHAR(50);\n";
    echo "UPDATE users SET role = 'section_officer' WHERE role = 'section_staff';\n";
    echo "ALTER TABLE users MODIFY COLUMN role ENUM('front_desk', 'section_officer', 'admin') DEFAULT 'section_officer';\n";
}
?>
