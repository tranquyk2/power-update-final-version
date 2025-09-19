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
        Schema::create('barcodes', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('factory_id');
            $table->tinyInteger('line_id');
            $table->integer('model_id');
            $table->string('code', 50);
            $table->string('device_name', 30);
            $table->dateTime('datetime');
            $table->tinyInteger('type_id');
            $table->unsignedTinyInteger('char_count');
            $table->string('note')->nullable();
            $table->string('status', 10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barcodes');
    }
};
