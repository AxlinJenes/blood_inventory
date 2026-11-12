<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class BloodBag extends Model
{
    protected $fillable = [
        'bag_number',
        'blood_group',
        'donor_name',
        'collection_date',
        'expiry_date',
        'quantity',
        'status',
        'refrigerator_id',
    ];

    protected $casts = [
        'collection_date' => 'date',
        'expiry_date' => 'datetime',
        'quantity' => 'integer',
    ];

    // A Blood Bag belongs to one Refrigerator
    public function refrigerator(): BelongsTo
    {
        return $this->belongsTo(Refrigerator::class);
    }

        protected function bloodGroup(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => strtoupper($value),
        );
    }

    protected function donorName(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => trim($value),
        );
    }
}