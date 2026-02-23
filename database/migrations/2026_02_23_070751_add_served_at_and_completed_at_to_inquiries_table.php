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
            // Only add if column doesn't exist
            if (!Schema::hasColumn('inquiries', 'served_at')) {
                $table->timestamp('served_at')->nullable()->after('served_by');
            }
            if (!Schema::hasColumn('inquiries', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('served_at');
            }
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
            if (Schema::hasColumn('inquiries', 'served_at')) {
                $table->dropColumn('served_at');
            }
            if (Schema::hasColumn('inquiries', 'completed_at')) {
                $table->dropColumn('completed_at');
            }
        });
    }
};
