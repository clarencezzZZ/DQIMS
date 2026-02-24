<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Find and fix duplicate queue numbers
        $duplicates = DB::select("
            SELECT queue_number, COUNT(*) as count 
            FROM inquiries 
            GROUP BY queue_number 
            HAVING COUNT(*) > 1
        ");
        
        foreach ($duplicates as $duplicate) {
            $queueNumber = $duplicate->queue_number;
            
            // Get all records with this queue number
            $records = DB::select("
                SELECT id, created_at 
                FROM inquiries 
                WHERE queue_number = ? 
                ORDER BY created_at ASC
            ", [$queueNumber]);
            
            // Keep the first one, update the rest with new queue numbers
            for ($i = 1; $i < count($records); $i++) {
                $record = $records[$i];
                
                // Generate a new unique queue number by appending a suffix
                $newQueueNumber = $queueNumber . '-' . ($i + 1);
                
                // Make sure the new queue number is unique
                $existing = DB::select("SELECT id FROM inquiries WHERE queue_number = ?", [$newQueueNumber]);
                $suffix = 2;
                while (!empty($existing)) {
                    $newQueueNumber = $queueNumber . '-' . ($i + $suffix);
                    $existing = DB::select("SELECT id FROM inquiries WHERE queue_number = ?", [$newQueueNumber]);
                    $suffix++;
                }
                
                // Update the record with new queue number
                DB::update("
                    UPDATE inquiries 
                    SET queue_number = ? 
                    WHERE id = ?
                ", [$newQueueNumber, $record->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This migration cannot be safely reversed
        // The duplicate queue numbers would need manual intervention to restore
    }
};