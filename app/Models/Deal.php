<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_id', 'buyer_client_id', 'seller_client_id', 'agent_id',
        'manager_id', 'reference_number', 'type', 'deal_value',
        'commission_percentage', 'commission_amount', 'currency',
        'status', 'offer_date', 'contract_date', 'payment_date',
        'handover_date', 'expected_closing_date', 'actual_closing_date',
        'terms_conditions', 'internal_notes', 'is_confidential'
    ];

    protected $casts = [
        'deal_value' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'offer_date' => 'date',
        'contract_date' => 'date',
        'payment_date' => 'date',
        'handover_date' => 'date',
        'expected_closing_date' => 'date',
        'actual_closing_date' => 'date',
        'is_confidential' => 'boolean'
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'buyer_client_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'seller_client_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}