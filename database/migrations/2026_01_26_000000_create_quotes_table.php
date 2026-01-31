<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->string('source')->nullable(); // e.g., "HR. Bukhari", "Warren Buffet"
            $table->string('type')->default('quote'); // 'hadith' or 'quote'
            $table->date('is_active_for_date')->nullable()->unique(); // Ensuring only one quote per day if we pre-fill, or just index it
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
