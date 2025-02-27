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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->dateTime('start')->nullable()->default(null);
            $table->dateTime('end')->nullable()->default(null);
            $table->foreignId('location_id')->constrained()->nullable()->default(null);
            $table->string('requiredSkill')->nullable()->default(null);
            $table->foreignId('employee_id')->constrained()->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
