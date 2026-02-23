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
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('queue_number', 20)->unique(); // e.g., ACS-001, OOSS-042
            $table->string('name');
            $table->string('contact_number', 20);
            $table->string('email')->nullable();
            $table->foreignId('category_id')->constrained('categories');
            $table->string('request_type');
            $table->text('description')->nullable();
            $table->enum('status', ['waiting', 'serving', 'completed', 'skipped', 'forwarded'])->default('waiting');
            $table->foreignId('served_by')->nullable()->constrained('users');
            $table->timestamp('called_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('forwarded_to')->nullable()->constrained('users');
            $table->date('date'); // For daily reset tracking
            $table->timestamps();
            
            $table->index(['date', 'category_id', 'status']);
            $table->index(['queue_number', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inquiries');
    }
};
