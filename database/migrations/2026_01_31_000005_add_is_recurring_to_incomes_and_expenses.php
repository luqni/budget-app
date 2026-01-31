<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monthly_incomes', function (Blueprint $table) {
            $table->boolean('is_recurring')->default(true)->after('income');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->boolean('is_recurring')->default(false)->after('month');
        });
    }

    public function down(): void
    {
        Schema::table('monthly_incomes', function (Blueprint $table) {
            $table->dropColumn('is_recurring');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('is_recurring');
        });
    }
};
