<?php
/**
 * Fix remaining isSectionStaff references after role rename
 */

echo "=== Fixing Remaining isSectionStaff References ===\n\n";

$files = [
    'resources/views/layouts/app.blade.php',
    'resources/views/section/index.blade.php',
    'app/Http/Controllers/SectionController.php',
    'app/Http/Controllers/Auth/LoginController.php',
];

foreach ($files as $file) {
    $filePath = __DIR__ . '/' . $file;
    
    if (!file_exists($filePath)) {
        echo "⚠️  File not found: $file\n";
        continue;
    }
    
    $content = file_get_contents($filePath);
    $count = substr_count($content, 'isSectionStaff');
    
    if ($count > 0) {
        $content = str_replace('isSectionStaff', 'isSectionOfficer', $content);
        file_put_contents($filePath, $content);
        echo "✅ Updated: $file ($count occurrences)\n";
    } else {
        echo "⊘ No changes needed: $file\n";
    }
}

echo "\n=== Complete! ===\n";
echo "Clear cache with: php artisan view:clear\n";
?>
