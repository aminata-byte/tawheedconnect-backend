<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('association_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('oustaze_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('video_url');
            $table->string('thumbnail')->nullable();
            $table->integer('duration')->nullable();
            $table->string('category')->nullable();
            $table->json('tags')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('shares_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('association_id');
            $table->index('oustaze_id');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
