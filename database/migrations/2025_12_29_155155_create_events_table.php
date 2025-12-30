<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('association_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('location');
            $table->string('city')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->string('type')->default('event');
            $table->string('category')->nullable();
            $table->integer('max_participants')->nullable();
            $table->integer('participants_count')->default(0);
            $table->boolean('requires_registration')->default(false);
            $table->string('status')->default('upcoming'); // draft, upcoming, ongoing, finished, cancelled
            $table->json('organizers')->nullable();
            $table->json('tags')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('shares_count')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('association_id');
            $table->index('start_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
