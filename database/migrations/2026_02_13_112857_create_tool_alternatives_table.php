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
        Schema::create('tool_alternatives', function (Blueprint $table) {
            $table->foreignUlid('tool_id')->constrained('tools')->cascadeOnDelete();
            $table->foreignUlid('alternative_id')->constrained('tools')->cascadeOnDelete();
            $table->primary(['tool_id', 'alternative_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tool_alternatives');
    }
};
