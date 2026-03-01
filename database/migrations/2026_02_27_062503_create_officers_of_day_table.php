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
        Schema::create('officers_of_day', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('custom'); // 'user' or 'custom'
            $table->unsignedBigInteger('user_id')->nullable(); // For linking to users table
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->unique(['name', 'type']); // Prevent duplicate names
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('officers_of_day');
    }
};
