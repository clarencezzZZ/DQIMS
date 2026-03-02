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
        Schema::create('assessment_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('year_month', 7)->unique(); // Format: YYYY-MM
            $table->unsignedInteger('current_value');
            $table->timestamps();
            
            $table->index('year_month');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assessment_sequences');
    }
};
