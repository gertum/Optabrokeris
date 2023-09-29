<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {

            // 'if' statements because rollback function was wrong

            if (!Schema::hasColumn('jobs', 'flag_uploaded')) {
                $table->boolean('flag_uploaded')->default(false);
            }
            if (!Schema::hasColumn('jobs', 'flag_solved')) {
                $table->boolean('flag_solved')->default(false);
            }
            if (!Schema::hasColumn('jobs', 'flag_solving')) {
                $table->boolean('flag_solving')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('flag_uploaded');
            $table->dropColumn('flag_solving');
            $table->dropColumn('flag_solved');
        });
    }
};
