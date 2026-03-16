<?php

// Test script to diagnose Closure parse error in PHP 8.0

echo "=== PHP Version Test ===\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "PHP Version ID: " . PHP_VERSION_ID . "\n\n";

echo "=== Testing Closure Syntax ===\n";

// Test 1: Check if Closure class exists
if (class_exists('Closure')) {
    echo "✓ Closure class exists\n";
} else {
    echo "✗ Closure class does NOT exist\n";
}

// Test 2: Check reflection on Closure
try {
    $reflection = new ReflectionClass('Closure');
    echo "✓ Can reflect on Closure class\n";
} catch (Exception $e) {
    echo "✗ Cannot reflect on Closure: " . $e->getMessage() . "\n";
}

// Test 3: Test different Closure type hint syntaxes
echo "\n=== Testing Different Syntax Approaches ===\n";

// Approach 1: Using 'Closure' without import (should work in PHP 8.0+)
$code1 = '
class TestClass1 {
    public function handle($request, Closure $next) {
        return $next();
    }
}
';
try {
    eval($code1);
    echo "✓ Approach 1 (Closure without import) works\n";
} catch (ParseError $e) {
    echo "✗ Approach 1 failed: " . $e->getMessage() . "\n";
}

// Approach 2: Using '\Closure' explicitly
$code2 = '
class TestClass2 {
    public function handle($request, \Closure $next) {
        return $next();
    }
}
';
try {
    eval($code2);
    echo "✓ Approach 2 (\\Closure explicit) works\n";
} catch (ParseError $e) {
    echo "✗ Approach 2 failed: " . $e->getMessage() . "\n";
}

// Approach 3: Importing Closure first
$code3 = '
use Closure;
class TestClass3 {
    public function handle($request, Closure $next) {
        return $next();
    }
}
';
try {
    eval($code3);
    echo "✓ Approach 3 (use Closure; then Closure) works\n";
} catch (ParseError $e) {
    echo "✗ Approach 3 failed: " . $e->getMessage() . "\n";
}

echo "\n=== File Analysis ===\n";

// Check the actual problematic file
$checkRoleFile = __DIR__ . '/app/Http/Middleware/CheckRole.php';
if (file_exists($checkRoleFile)) {
    echo "Checking: $checkRoleFile\n";
    $content = file_get_contents($checkRoleFile);
    
    // Look for \Closure usage
    if (preg_match('/\\\\Closure\s+\$next/', $content)) {
        echo "✗ Found \\Closure (with backslash) - This may cause issues in PHP 8.0\n";
    } elseif (preg_match('/use\s+Closure;/',$content)) {
        echo "✓ Found 'use Closure;' import\n";
        if (preg_match('/Closure\s+\$next/',$content)) {
            echo "✓ Using 'Closure $next' correctly after import\n";
        }
    } else {
        echo "? No Closure type hints found\n";
    }
} else {
    echo "File not found: $checkRoleFile\n";
}

echo "\n=== Diagnostic Complete ===\n";
