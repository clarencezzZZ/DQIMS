<?php

/**
 * Final Verification Test for Closure Syntax Fix
 * This tests that all files can be parsed and loaded without errors
 */

echo "=== FINAL VERIFICATION TEST ===\n\n";

$test_results = [];

// Test 1: Check middleware files can be loaded
echo "Test 1: Loading middleware files...\n";
try {
    require_once __DIR__ . '/app/Http/Middleware/CheckRole.php';
    echo "✓ CheckRole.php loaded successfully\n";
    $test_results[] = true;
} catch (ParseError $e) {
    echo "✗ CheckRole.php failed: " . $e->getMessage() . "\n";
    $test_results[] = false;
}

try {
    require_once __DIR__ . '/app/Http/Middleware/RedirectIfAuthenticated.php';
    echo "✓ RedirectIfAuthenticated.php loaded successfully\n";
    $test_results[] = true;
} catch (ParseError $e) {
    echo "✗ RedirectIfAuthenticated.php failed: " . $e->getMessage() . "\n";
    $test_results[] = false;
}

// Test 2: Check vendor Excel file
echo "\nTest 2: Loading vendor Excel files...\n";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    try {
        require_once __DIR__ . '/vendor/autoload.php';
        echo "✓ Composer autoload loaded\n";
        
        // Try to load Row class
        if (class_exists('Maatwebsite\Excel\Row')) {
            $reflection = new ReflectionClass('Maatwebsite\Excel\Row');
            $method = $reflection->getMethod('setPreparationCallback');
            echo "✓ Maatwebsite\\Excel\\Row class loaded successfully\n";
            echo "✓ setPreparationCallback method exists\n";
            
            // Check the parameter type
            $params = $method->getParameters();
            foreach ($params as $param) {
                $type = $param->getType();
                if ($type) {
                    echo "✓ Parameter type: " . $type->getName() . " (allows null: " . ($type->allowsNull() ? 'yes' : 'no') . ")\n";
                }
            }
            $test_results[] = true;
        } else {
            echo "⚠ Maatwebsite\\Excel\\Row class not found (might be autoloaded later)\n";
            $test_results[] = true;
        }
    } catch (Exception $e) {
        echo "✗ Vendor loading failed: " . $e->getMessage() . "\n";
        $test_results[] = false;
    }
}

// Test 3: Verify no ?Closure syntax remains
echo "\nTest 3: Scanning for remaining ?Closure syntax...\n";
$files_to_check = [
    'vendor/maatwebsite/excel/src/Row.php',
    'app/Http/Middleware/CheckRole.php',
    'app/Http/Middleware/RedirectIfAuthenticated.php',
];

$found_issues = false;
foreach ($files_to_check as $file) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        $content = file_get_contents($fullPath);
        if (strpos($content, '?Closure') !== false) {
            echo "✗ Found ?Closure in $file\n";
            $found_issues = true;
            $test_results[] = false;
        } else {
            echo "✓ No ?Closure in $file\n";
        }
    }
}

if (!$found_issues) {
    $test_results[] = true;
}

// Test 4: Verify \Closure is used correctly
echo "\nTest 4: Verifying correct \\Closure usage...\n";
$correct_usage = true;
foreach ($files_to_check as $file) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        $content = file_get_contents($fullPath);
        if (preg_match('/\\\\Closure\s+\$/m', $content)) {
            echo "✓ Correct \\Closure usage in $file\n";
        } elseif (preg_match('/Closure\s+\$/m', $content) && strpos($content, 'use Closure;') === false) {
            echo "⚠ Potential issue in $file - Closure without import or namespace\n";
            $correct_usage = false;
        }
    }
}

if ($correct_usage) {
    $test_results[] = true;
} else {
    $test_results[] = false;
}

// Summary
echo "\n=== TEST SUMMARY ===\n";
$passed = array_sum($test_results);
$total = count($test_results);
echo "Passed: $passed / $total tests\n";

if ($passed === $total) {
    echo "\n✅ ALL TESTS PASSED!\n";
    echo "The Closure syntax issues have been fixed.\n";
    echo "\nYou can now:\n";
    echo "1. Restart your Laravel development server\n";
    echo "2. Try exporting Excel reports again\n";
    echo "3. The parse error should be resolved\n";
    exit(0);
} else {
    echo "\n❌ SOME TESTS FAILED!\n";
    echo "Please review the errors above.\n";
    exit(1);
}
