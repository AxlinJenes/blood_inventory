<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Refrigerator extends Model
{
    protected $fillable = [
        'blood_bank_id',
        'name',
        'location',
    ];

    // A Refrigerator belongs to one Blood Bank
    public function bloodBank(): BelongsTo
    {
        return $this->belongsTo(BloodBank::class);
    }

    // A Refrigerator has many Temperature Logs
    public function temperatureLogs(): HasMany
    {
        return $this->hasMany(TemperatureLog::class);
    }

    // A Refrigerator has many Blood Bags
    public function bloodBags(): HasMany
    {
        return $this->hasMany(BloodBag::class);
    }
}