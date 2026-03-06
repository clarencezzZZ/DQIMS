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
            // Drop foreign key constraint first
            $table->dropForeign(['forwarded_to']);
            
            // Remove unused columns
            $table->dropColumn(['remarks', 'forwarded_to', 'email']);
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
            $table->string('remarks')->nullable();
            $table->string('forwarded_to')->nullable();
            $table->string('email')->nullable();
            
            // Add back foreign key
            $table->foreign('forwarded_to')->references('id')->on('users');
        });
    }
};
