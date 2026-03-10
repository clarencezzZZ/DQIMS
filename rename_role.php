<?php
/**
 * Role Rename Script: section_staff → section_officer
 * 
 * Run via: php rename_role.php
 */

$files = [
    'app/Models/User.php',
    'database/migrations/2026_02_23_030048_add_role_to_users_table.php',
    'database/seeders/FixStanleyLotaSeeder.php',
    'database/seeders/UserSeeder.php',
    'database/seeders/OfficerSeeder.php',
    'app/Http/Controllers/AdminController.php',
    'routes/web.php',
    'resources/views/admin/users.blade.php',
];

$replacements = [
    "'section_staff'" => "'section_officer'",
    '"section_staff"' => '"section_officer"',
    'ROLE_SECTION_STAFF' => 'ROLE_SECTION_OFFICER',
    'isSectionStaff' => 'isSectionOfficer',
    'Section Staff' => 'Section Officer',
    'section staff' => 'section officer',
];

echo "=== Role Rename: section_staff → section_officer ===\n\n";

foreach ($files as $file) {
    $filePath = __DIR__ . '/' . $file;
    
    if (!file_exists($filePath)) {
        echo "⚠️  File not found: $file\n";
        continue;
    }
    
    $content = file_get_contents($filePath);
    $originalContent = $content;
    $count = 0;
    
    foreach ($replacements as $search => $replace) {
        $occurrences = substr_count($content, $search);
        if ($occurrences > 0) {
            $content = str_replace($search, $replace, $content);
            $count += $occurrences;
            echo "✓ Replaced '$search' → '$replace' in $file ($occurrences occurrences)\n";
        }
    }
    
    if ($count > 0) {
        file_put_contents($filePath, $content);
        echo "✅ Updated: $file (total changes: $count)\n\n";
    } else {
        echo "⊘ No changes needed: $file\n\n";
    }
}

// Update migration specifically
$migrationFile = __DIR__ . '/database/migrations/2026_02_23_030048_add_role_to_users_table.php';
if (file_exists($migrationFile)) {
    $content = file_get_contents($migrationFile);
    // Update default value too
    $content = str_replace(
        "->default('section_staff')",
        "->default('section_officer')",
        $content
    );
    file_put_contents($migrationFile, $content);
    echo "✅ Updated migration default value\n";
}

echo "\n=== Complete! ===\n";
echo "Don't forget to:\n";
echo "1. Clear cache: php artisan cache:clear\n";
echo "2. Update database if needed (existing users with role='section_staff')\n";
?>
