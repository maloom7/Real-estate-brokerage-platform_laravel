<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyDocument extends Model
{
    protected $fillable = [
        'property_id', 'document_type_id', 'file_path', 'file_name',
        'mime_type', 'file_size', 'is_confidential', 'expiry_date',
        'verification_status', 'verified_by'
    ];

    protected $casts = [
        'is_confidential' => 'boolean',
        'expiry_date' => 'date',
        'verified_by' => 'integer'
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}