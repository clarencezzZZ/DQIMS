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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->string('assessment_number', 30)->unique(); // Auto-generated
            $table->foreignId('inquiry_id')->constrained('inquiries');
            $table->string('queue_number', 20);
            $table->string('guest_name');
            $table->foreignId('category_id')->constrained('categories');
            $table->string('request_type');
            $table->decimal('fees', 10, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->foreignId('processed_by')->constrained('users');
            $table->date('assessment_date');
            $table->timestamps();
            
            $table->index(['assessment_date', 'category_id']);
            $table->index('inquiry_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assessments');
    }
};
