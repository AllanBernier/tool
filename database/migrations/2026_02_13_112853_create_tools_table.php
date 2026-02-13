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
        Schema::create('tools', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('url');
            $table->string('logo_path')->nullable();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->json('pricing')->nullable();
            $table->json('pros')->nullable();
            $table->json('cons')->nullable();
            $table->json('features')->nullable();
            $table->json('faq')->nullable();
            $table->json('platforms')->nullable();
            $table->foreignUlid('category_id')->constrained();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_sponsored')->default(false);
            $table->dateTime('sponsored_until')->nullable();
            $table->string('generation_status')->default('pending');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->dateTime('generated_at')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tools');
    }
};
