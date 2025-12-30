<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('followers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Celui qui suit
            $table->foreignId('association_id')->constrained()->onDelete('cascade'); // Association suivie
            $table->timestamps();

            $table->unique(['user_id', 'association_id']);
            $table->index('user_id');
            $table->index('association_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('followers');
    }
};
