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
            $table->string('responsibility_center', 50)->nullable()->after('bill_number');
            $table->string('legal_basis', 50)->nullable()->after('address');
            $table->json('names_detail')->nullable()->after('request_type');
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
            $table->dropColumn(['responsibility_center', 'legal_basis', 'names_detail']);
        });
    }
};
