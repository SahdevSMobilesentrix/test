<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->string('meta_description', 200)->nullable();
            $table->string('focus_keyword')->nullable();
            $table->json('secondary_keywords')->nullable();
            $table->string('category')->index();
            $table->json('tags')->nullable();
            $table->string('featured_image_alt')->nullable();
            $table->json('faq')->nullable();
            $table->string('status')->default('scheduled')->index(); // draft | scheduled | published
            $table->timestamp('published_at')->nullable()->index();
            $table->unsignedSmallInteger('seo_score')->default(0);
            $table->unsignedInteger('word_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
