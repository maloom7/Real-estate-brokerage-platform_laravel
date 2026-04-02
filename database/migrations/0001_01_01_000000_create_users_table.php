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
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'manager', 'agent', 'assistant'])->default('agent');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->json('permissions')->nullable();
            $table->foreignId('manager_id')->nullable()->constrained('users');
            $table->string('profile_photo_path')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('role');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};