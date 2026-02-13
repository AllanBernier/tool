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
        Schema::create('comparisons', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('tool_a_id')->constrained('tools');
            $table->foreignUlid('tool_b_id')->constrained('tools');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->text('verdict')->nullable();
            $table->string('generation_status')->default('pending');
            $table->boolean('is_published')->default(false);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->dateTime('generated_at')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->timestamps();

            $table->unique(['tool_a_id', 'tool_b_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comparisons');
    }
};
