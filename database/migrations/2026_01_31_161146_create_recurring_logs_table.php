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
        Schema::create('recurring_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_expense_id')->constrained('expenses')->onDelete('cascade');
            $table->string('target_month'); // Y-m format
            $table->timestamps();
            
            // Prevent same expense being copied to same month multiple times
            $table->unique(['source_expense_id', 'target_month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_logs');
    }
};
