<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyImage extends Model
{
    protected $fillable = [
        'property_id', 'path', 'thumbnail_path', 'is_primary', 'sort_order', 'category'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer'
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}