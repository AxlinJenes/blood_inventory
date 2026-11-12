<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BloodBank extends Model
{
    protected $fillable = [
        'name',
        'address',
        'contact_number',
    ];

    // One Blood Bank has many Refrigerators
    public function refrigerators(): HasMany
    {
        return $this->hasMany(Refrigerator::class);
    }

    // One Blood Bank can have many Users
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}