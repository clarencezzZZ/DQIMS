<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First, update any 'urgent' records to 'priority'
        DB::statement("UPDATE inquiries SET priority = 'priority' WHERE priority = 'urgent'");
        
        // Modify the enum to only allow 'normal' and 'priority'
        DB::statement("ALTER TABLE inquiries MODIFY COLUMN priority ENUM('normal', 'priority') DEFAULT 'normal'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert to allowing 'urgent' as well
        DB::statement("ALTER TABLE inquiries MODIFY COLUMN priority ENUM('normal', 'priority', 'urgent') DEFAULT 'normal'");
    }
};
