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
        Schema::table('histories', function (Blueprint $table) {
            $table->tinyInteger('week')->nullable()->after('day');
            $table->integer('month')->nullable()->after('week');
            $table->integer('year')->nullable()->after('month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('histories', function (Blueprint $table) {
            $table->dropColumn('week');
            $table->dropColumn('month');
            $table->dropColumn('year');
        });
    }
};
