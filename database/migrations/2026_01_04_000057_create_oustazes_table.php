<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oustazes', function (Blueprint $table) {
            $table->id();
            $table->string('nom_complet');
            $table->text('specialites');
            $table->string('telephone');
            $table->unsignedBigInteger('association_id');
            $table->foreign('association_id')
                ->references('id')
                ->on('associations')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oustazes');
    }
};
