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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->float('maxWorkingHours')->default(0); // seems like would be nicer defined somewhere else
            $table->integer('row')->default(0); // excel
            $table->integer('sequenceNumber')->default(0);
            $table->float('workingHoursPerDay')->default(8.0); // Darbo valandų skaičius per dieną. seems like would be nicer defined somewhere else
            $table->float('positionAmount')->default(1.0); // Etatų skaičius.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
