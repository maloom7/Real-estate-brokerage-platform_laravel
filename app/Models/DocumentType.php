<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends Model
{
    protected $fillable = [
        'name_ar', 'name_en', 'requires_verification', 'is_confidential_default'
    ];

    protected $casts = [
        'requires_verification' => 'boolean',
        'is_confidential_default' => 'boolean'
    ];

    public function documents(): HasMany
    {
        return $this->hasMany(PropertyDocument::class);
    }
}