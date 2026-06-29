<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->string('featured_image_prompt')->nullable()->after('featured_image_alt');
            $table->string('excerpt', 320)->nullable()->after('meta_description');
            $table->string('type')->default('general')->index()->after('category'); // general | market
            $table->boolean('is_featured')->default(false)->index()->after('status');
            $table->unsignedInteger('views')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn(['featured_image_prompt', 'excerpt', 'type', 'is_featured', 'views']);
        });
    }
};
