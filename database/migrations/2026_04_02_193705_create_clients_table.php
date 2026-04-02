<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('phone_alt')->nullable();
            $table->enum('type', ['buyer', 'seller', 'landlord', 'tenant', 'investor']);
            $table->enum('status', ['new', 'active', 'inactive', 'converted', 'blacklisted'])->default('new');
            $table->string('nationality')->nullable();
            $table->string('id_number')->nullable();
            $table->string('id_type')->nullable();
            $table->date('id_expiry_date')->nullable();
            $table->json('preferences')->nullable();
            $table->decimal('budget_min', 15, 2)->nullable();
            $table->decimal('budget_max', 15, 2)->nullable();
            $table->string('source')->nullable();
            $table->foreignId('referred_by')->nullable()->constrained('users');
            $table->foreignId('assigned_agent')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            $table->integer('priority')->default(5);
            $table->timestamp('last_contacted_at')->nullable();
            $table->timestamp('next_follow_up_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['type', 'status']);
            $table->index('assigned_agent');
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};