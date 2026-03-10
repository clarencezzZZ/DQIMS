<?php
/**
 * Restore section_staff role and create both roles (section_staff AND section_officer)
 */

echo "=== Restoring section_staff Role ===\n\n";

$files = [
    'app/Models/User.php',
    'database/migrations/2026_02_23_030048_add_role_to_users_table.php',
    'routes/web.php',
];

// Add back section_staff role alongside section_officer
foreach ($files as $file) {
    $filePath = __DIR__ . '/' . $file;
    
    if (!file_exists($filePath)) {
        echo "⚠️  File not found: $file\n";
        continue;
    }
    
    $content = file_get_contents($filePath);
    
    // For User.php - add back the constant and method
    if ($file === 'app/Models/User.php') {
        if (strpos($content, 'ROLE_SECTION_STAFF') === false) {
            $content = str_replace(
                "const ROLE_SECTION_OFFICER = 'section_officer';",
                "const ROLE_SECTION_STAFF = 'section_staff';\n    const ROLE_SECTION_OFFICER = 'section_officer';",
                $content
            );
            
            if (strpos($content, 'isSectionStaff') === false) {
                $content = str_replace(
                    "public function isSectionOfficer(): bool\n    {\n        return \$this->role === self::ROLE_SECTION_OFFICER;\n    }",
                    "public function isSectionStaff(): bool\n    {\n        return \$this->role === self::ROLE_SECTION_STAFF;\n    }\n\n    public function isSectionOfficer(): bool\n    {\n        return \$this->role === self::ROLE_SECTION_OFFICER;\n    }",
                    $content
                );
            }
        }
        
        file_put_contents($filePath, $content);
        echo "✅ Updated: $file\n";
    }
}

// Update migration to include both roles
$migrationFile = __DIR__ . '/database/migrations/2026_02_23_030048_add_role_to_users_table.php';
if (file_exists($migrationFile)) {
    $content = file_get_contents($migrationFile);
    $content = str_replace(
        "ENUM('front_desk', 'section_officer', 'admin')",
        "ENUM('front_desk', 'section_staff', 'section_officer', 'admin')",
        $content
    );
    $content = str_replace(
        "->default('section_officer')",
        "->default('section_staff')",
        $content
    );
    file_put_contents($migrationFile, $content);
    echo "✅ Updated migration\n";
}

// Update routes to handle both roles
$routeFile = __DIR__ . '/routes/web.php';
if (file_exists($routeFile)) {
    $content = file_get_contents($routeFile);
    // Change section_officer back to section_staff for middleware
    $content = str_replace(
        "Route::middleware(['role:section_officer,admin'])",
        "Route::middleware(['role:section_staff,admin'])",
        $content
    );
    // But keep dashboard redirect checking both
    if (strpos($content, 'isSectionStaff()') === false) {
        $content = str_replace(
            "} elseif (\$user->isSectionOfficer()) {",
            "} elseif (\$user->isSectionStaff() || \$user->isSectionOfficer()) {",
            $content
        );
    }
    file_put_contents($routeFile, $content);
    echo "✅ Updated routes\n";
}

echo "\n=== Updating Database ===\n";

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Update ENUM to include both roles
    DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50)");
    DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('front_desk', 'section_staff', 'section_officer', 'admin') DEFAULT 'section_staff'");
    echo "✅ Database updated with both roles\n";
} catch (\Exception $e) {
    echo "⚠️  Database update notice: " . $e->getMessage() . "\n";
}

echo "\n=== Complete! ===\n";
echo "Now you have BOTH roles:\n";
echo "- section_staff (original)\n";
echo "- section_officer (new)\n";
?>
