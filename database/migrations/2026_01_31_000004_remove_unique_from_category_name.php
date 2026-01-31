<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Drop unique constraint on name (users can have same category names)
            $table->dropUnique(['name']);
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->unique('name');
        });
    }
};
