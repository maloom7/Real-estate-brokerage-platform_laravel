<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'email', 'phone', 'phone_alt', 'type', 'status',
        'nationality', 'id_number', 'id_type', 'id_expiry_date',
        'preferences', 'budget_min', 'budget_max', 'source',
        'referred_by', 'assigned_agent', 'notes', 'priority',
        'last_contacted_at', 'next_follow_up_at'
    ];

    protected $casts = [
        'preferences' => 'array',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'id_expiry_date' => 'date',
        'priority' => 'integer',
        'last_contacted_at' => 'datetime',
        'next_follow_up_at' => 'datetime'
    ];

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_agent');
    }

    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'client_id');
    }

    public function buyDeals(): HasMany
    {
        return $this->hasMany(Deal::class, 'buyer_client_id');
    }

    public function sellDeals(): HasMany
    {
        return $this->hasMany(Deal::class, 'seller_client_id');
    }
}