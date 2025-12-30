<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('association_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('oustaze_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('audio_url');
            $table->string('thumbnail')->nullable();
            $table->integer('duration')->nullable(); // en secondes
            $table->string('category')->nullable(); // dars, khoutba, conference
            $table->json('tags')->nullable();
            $table->integer('plays_count')->default(0);
            $table->integer('downloads_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('association_id');
            $table->index('oustaze_id');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audios');
    }
};
