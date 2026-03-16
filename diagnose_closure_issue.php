<?php

/**
 * Comprehensive Closure Parse Error Diagnostic Tool
 * This script identifies ALL files with potential Closure syntax issues
 */

echo "=== COMPREHENSIVE CLOSURE SYNTAX DIAGNOSTIC ===\n\n";

// Set error handler to catch parse errors
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    echo "ERROR: $errstr in $errfile on line $errline\n";
    return true;
});

$files_with_issues = [];

// Scan all PHP files in app directory
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(__DIR__ . '/app')
);

foreach ($iterator as $file) {
    if ($file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $relativePath = str_replace(__DIR__ . '\\', '', $file->getPathname());
        
        // Check for problematic patterns
        $patterns = [
            '/\\\\Closure\s+\$/m' => 'Backslash Closure without proper context',
            '/(?<!use\s)(?<!\\\\)Closure\s+\$/m' => 'Closure without import or namespace',
        ];
        
        foreach ($patterns as $pattern => $description) {
            if (preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
                $line_number = substr_count(substr($content, 0, $matches[0][1]), "\n") + 1;
                $files_with_issues[] = [
                    'file' => $relativePath,
                    'line' => $line_number,
                    'issue' => $description,
                    'match' => $matches[0][0]
                ];
            }
        }
    }
}

echo "Scanning vendor/maatwebsite/excel directory...\n";

// Specifically check Excel package
$excelFiles = [
    __DIR__ . '/vendor/maatwebsite/excel/src/Row.php',
    __DIR__ . '/vendor/maatwebsite/excel/src/Concerns/WithMapping.php',
    __DIR__ . '/vendor/maatwebsite/excel/src/Concerns/WithHeadingRow.php',
];

foreach ($excelFiles as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        if (strpos($content, '?Closure') !== false) {
            echo "⚠ FOUND ?Closure in: $file\n";
            $lines = explode("\n", $content);
            foreach ($lines as $i => $line) {
                if (strpos($line, '?Closure') !== false) {
                    echo "  Line " . ($i + 1) . ": " . trim($line) . "\n";
                }
            }
        }
    }
}

echo "\n=== SCAN RESULTS ===\n";
if (empty($files_with_issues)) {
    echo "✓ No obvious Closure syntax issues found in app/ directory\n";
} else {
    echo "✗ Found " . count($files_with_issues) . " potential issue(s):\n\n";
    foreach ($files_with_issues as $issue) {
        echo "File: {$issue['file']}\n";
        echo "Line: {$issue['line']}\n";
        echo "Issue: {$issue['issue']}\n";
        echo "Match: {$issue['match']}\n";
        echo "---\n";
    }
}

echo "\n=== CHECKING OPCODE CACHE ===\n";
if (function_exists('opcache_reset')) {
    echo "ℹ OpCache is enabled. After fixing files, run: opcache_reset()\n";
} else {
    echo "ℹ OpCache is disabled (good for debugging)\n";
}

echo "\n=== PHP VERSION INFO ===\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "PHP Version ID: " . PHP_VERSION_ID . "\n";

if (PHP_VERSION_ID < 80100) {
    echo "\n⚠ WARNING: You are using PHP " . PHP_VERSION . "\n";
    echo "The ?Closure syntax has issues in PHP 8.0.x\n";
    echo "Recommended solutions:\n";
    echo "  1. Use \\Closure instead of ?Closure\n";
    echo "  2. Upgrade to PHP 8.1+\n";
    echo "  3. Update Laravel Excel to latest version\n";
}

echo "\n=== DIAGNOSTIC COMPLETE ===\n";
