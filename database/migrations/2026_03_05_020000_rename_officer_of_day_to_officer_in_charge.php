<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rename the column from officer_of_day to officer_in_charge
        Schema::table('assessments', function (Blueprint $table) {
            $table->renameColumn('officer_of_day', 'officer_in_charge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename the column back to officer_of_day
        Schema::table('assessments', function (Blueprint $table) {
            $table->renameColumn('officer_in_charge', 'officer_of_day');
        });
    }
};
