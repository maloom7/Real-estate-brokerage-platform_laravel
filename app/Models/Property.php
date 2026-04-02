<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Property extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'agent_id', 'client_id', 'reference_number', 'title_ar', 'title_en',
        'description_ar', 'description_en', 'price', 'commission_percentage',
        'commission_amount', 'currency', 'payment_type', 'category', 'type',
        'area', 'rooms', 'bathrooms', 'floors', 'parking_spaces', 'year_built',
        'country', 'city', 'district', 'street', 'building_number',
        'latitude', 'longitude', 'map_link', 'amenities', 'features',
        'status', 'visibility', 'is_featured', 'is_verified',
        'available_from', 'listing_expires_at', 'published_at',
        'views_count', 'leads_count', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'area' => 'integer',
        'rooms' => 'integer',
        'bathrooms' => 'integer',
        'floors' => 'integer',
        'parking_spaces' => 'integer',
        'year_built' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'amenities' => 'array',
        'features' => 'array',
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
        'views_count' => 'integer',
        'leads_count' => 'integer',
        'available_from' => 'date',
        'listing_expires_at' => 'date',
        'published_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($property) {
            $property->reference_number = 'PROP-' . strtoupper(Str::random(8));
            $property->created_by = auth()->id();
        });

        static::updating(function ($property) {
            $property->updated_by = auth()->id();
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['price', 'status', 'title_ar'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PropertyDocument::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(PropertyImage::class)->where('is_primary', true);
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 0) . ' ' . $this->currency;
    }

    public function getFullAddressAttribute(): string
    {
        return collect([
            $this->building_number,
            $this->street,
            $this->district,
            $this->city,
            $this->country
        ])->filter()->implode(', ');
    }

    public function getCommissionAmountAttribute($value)
    {
        return $value ?? ($this->price * ($this->commission_percentage / 100));
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }

    public function scopePriceRange($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title_ar', 'LIKE', "%{$search}%")
              ->orWhere('description_ar', 'LIKE', "%{$search}%")
              ->orWhere('reference_number', 'LIKE', "%{$search}%");
        });
    }

    public function isAvailable()
    {
        return in_array($this->status, ['active', 'approved']);
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function incrementLeads()
    {
        $this->increment('leads_count');
    }
}