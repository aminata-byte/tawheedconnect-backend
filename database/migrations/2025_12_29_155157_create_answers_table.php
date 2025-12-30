<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->foreignId('oustaze_id')->constrained('users')->onDelete('cascade');
            $table->text('answer');
            $table->string('audio_url')->nullable();
            $table->string('video_url')->nullable();
            $table->json('references')->nullable();
            $table->timestamps();

            $table->index('question_id');
            $table->index('oustaze_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
