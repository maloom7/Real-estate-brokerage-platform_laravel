<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('users');
            $table->foreignId('client_id')->nullable()->constrained('clients');
            $table->string('reference_number')->unique();
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->text('description_ar');
            $table->text('description_en')->nullable();
            $table->decimal('price', 15, 2);
            $table->decimal('commission_percentage', 5, 2)->default(2.5);
            $table->decimal('commission_amount', 15, 2)->nullable();
            $table->string('currency')->default('SAR');
            $table->enum('payment_type', ['sale', 'rent', 'lease'])->default('sale');
            $table->enum('category', ['residential', 'commercial', 'industrial', 'land']);
            $table->enum('type', ['apartment', 'villa', 'office', 'showroom', 'warehouse', 'land']);
            $table->integer('area')->nullable();
            $table->integer('rooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->integer('floors')->nullable();
            $table->integer('parking_spaces')->nullable();
            $table->year('year_built')->nullable();
            $table->string('country')->default('Saudi Arabia');
            $table->string('city');
            $table->string('district');
            $table->string('street')->nullable();
            $table->string('building_number')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->json('amenities')->nullable();
            $table->json('features')->nullable();
            $table->enum('status', ['draft', 'pending_review', 'approved', 'active', 'under_offer', 'reserved', 'sold', 'rented', 'archived'])->default('draft');
            $table->enum('visibility', ['public', 'internal', 'vip'])->default('internal');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->date('available_from')->nullable();
            $table->date('listing_expires_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('leads_count')->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status', 'visibility']);
            $table->index(['city', 'district']);
            $table->index(['category', 'type']);
            $table->index('price');
            $table->fullText(['title_ar', 'description_ar']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};