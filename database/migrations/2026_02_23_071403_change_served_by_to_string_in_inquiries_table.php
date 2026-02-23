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
        Schema::table('inquiries', function (Blueprint $table) {
            // Drop foreign key constraint if exists
            $table->dropForeign(['served_by']);
        });
        
        Schema::table('inquiries', function (Blueprint $table) {
            // Change served_by from integer to string to store username
            $table->string('served_by', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->unsignedBigInteger('served_by')->nullable()->change();
        });
        
        Schema::table('inquiries', function (Blueprint $table) {
            $table->foreign('served_by')->references('id')->on('users');
        });
    }
};
