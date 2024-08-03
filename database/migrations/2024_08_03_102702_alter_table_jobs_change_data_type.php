<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement( /** @lang MySQL */"ALTER TABLE jobs change data data LONGBLOB");
        DB::statement( /** @lang MySQL */"ALTER TABLE jobs change result result LONGBLOB");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
