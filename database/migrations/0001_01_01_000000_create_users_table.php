<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone', 20)->unique();
            $table->string('email')->nullable()->unique();
            $table->string('password');
            $table->string('photo')->nullable();
            $table->text('bio')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Sénégal');
            $table->string('role')->default('member'); // member, association, admin, oustaze
            $table->boolean('is_active')->default(true);
            $table->boolean('phone_verified')->default(false);
            $table->string('verification_code', 6)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index('phone');
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
