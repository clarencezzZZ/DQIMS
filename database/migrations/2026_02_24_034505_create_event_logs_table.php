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
        Schema::create('event_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // User who performed the action
            $table->string('action'); // Type of action (created, updated, deleted)
            $table->string('assessment_number')->nullable(); // Assessment number affected
            $table->text('description'); // Description of what happened
            $table->json('old_values')->nullable(); // Store old values before change
            $table->json('new_values')->nullable(); // Store new values after change
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_logs');
    }
};