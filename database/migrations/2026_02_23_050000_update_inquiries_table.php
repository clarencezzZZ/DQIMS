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
            // Rename name to guest_name
            $table->renameColumn('name', 'guest_name');
            
            // Make contact_number nullable
            $table->string('contact_number', 20)->nullable()->change();
            
            // Add new columns
            $table->string('purpose')->nullable()->after('request_type');
            $table->enum('priority', ['normal', 'priority'])->default('normal')->after('description');
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
            $table->renameColumn('guest_name', 'name');
            $table->dropColumn(['purpose', 'priority']);
        });
    }
};
