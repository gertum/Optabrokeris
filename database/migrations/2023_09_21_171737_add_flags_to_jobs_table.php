<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->boolean('flag_uploaded');
            $table->boolean('flag_solving');
            $table->boolean('flag_solved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->removeColumn('flag_uploaded');
            $table->removeColumn('flag_solving');
            $table->removeColumn('flag_solved');
        });
    }
};
