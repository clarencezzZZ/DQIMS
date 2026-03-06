<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== WAITING INQUIRIES ===\n\n";
$waiting = DB::table('inquiries')
    ->select('id', 'queue_number', 'guest_name', 'status', 'category_id', 'created_at')
    ->where('status', 'waiting')
    ->orderBy('created_at', 'desc')
    ->limit(15)
    ->get();

foreach ($waiting as $inquiry) {
    echo "ID: {$inquiry->id}\n";
    echo "Queue #: {$inquiry->queue_number}\n";
    echo "Guest: {$inquiry->guest_name}\n";
    echo "Created: {$inquiry->created_at}\n";
    echo "---\n";
}

echo "\n=== CATEGORIES WITH SECTIONS ===\n\n";
$categories = DB::table('categories')
    ->select('id', 'code', 'name', 'section')
    ->get();

foreach ($categories as $cat) {
    echo "ID: {$cat->id} | Code: {$cat->code} | Section: {$cat->section} | Name: {$cat->name}\n";
}
