<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained();
            $table->foreignId('buyer_client_id')->constrained('clients');
            $table->foreignId('seller_client_id')->constrained('clients');
            $table->foreignId('agent_id')->constrained('users');
            $table->foreignId('manager_id')->nullable()->constrained('users');
            $table->string('reference_number')->unique();
            $table->enum('type', ['sale', 'rent', 'lease']);
            $table->decimal('deal_value', 15, 2);
            $table->decimal('commission_percentage', 5, 2);
            $table->decimal('commission_amount', 15, 2);
            $table->string('currency')->default('SAR');
            $table->enum('status', ['lead', 'negotiation', 'offer_made', 'offer_accepted', 'contract_signed', 'payment_pending', 'completed', 'cancelled'])->default('lead');
            $table->date('offer_date')->nullable();
            $table->date('contract_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->date('handover_date')->nullable();
            $table->date('expected_closing_date')->nullable();
            $table->date('actual_closing_date')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->text('internal_notes')->nullable();
            $table->boolean('is_confidential')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status', 'agent_id']);
            $table->index('deal_value');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};