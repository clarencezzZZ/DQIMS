<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::table('assessments', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign('assessments_officer_of_day_foreign');
            // Change the column to string type
            $table->string('officer_of_day')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assessments', function (Blueprint $table) {
            // Revert to foreignId
            $table->dropColumn('officer_of_day');
            $table->foreignId('officer_of_day')->nullable()->constrained('users')->onDelete('set null');
        });
    }
};
