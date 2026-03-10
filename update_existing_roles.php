<?php
/**
 * Update existing users from section_staff to section_officer
 * 
 * Run via: php update_existing_roles.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== Updating Existing User Roles ===\n\n";

// Count affected users
$count = User::where('role', 'section_staff')->count();

if ($count > 0) {
    echo "Found $count user(s) with role='section_staff'\n";
    echo "Updating to role='section_officer'...\n\n";
    
    // Update them
    $updated = User::where('role', 'section_staff')->update(['role' => 'section_officer']);
    
    echo "✅ Updated $updated user(s) successfully!\n\n";
    
    // Show updated users
    $users = User::where('role', 'section_officer')->get();
    echo "Current Section Officers:\n";
    foreach ($users as $user) {
        echo "  - {$user->username} ({$user->email})\n";
    }
} else {
    echo "No users found with role='section_staff'\n";
    echo "All users are already up to date!\n";
}

echo "\n=== Complete! ===\n";
?>
