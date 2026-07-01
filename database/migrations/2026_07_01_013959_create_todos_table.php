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
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Para sa Task Title / Description
            $table->string('priority')->default('low'); // High, Medium, Low
            $table->dateTime('deadline')->nullable(); // Target Deadline
            $table->boolean('is_completed')->default(false); // Para sa checkbox kung tapos na
            $table->timestamps(); // Gagawa ng created_at at updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};
