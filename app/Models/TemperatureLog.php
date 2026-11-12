<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class TemperatureLog extends Model
{
    protected $fillable = [
        'refrigerator_id',
        'temperature',
        'recorded_at',
        'notes',
    ];

    // A Temperature Log belongs to one Refrigerator
    public function refrigerator(): BelongsTo
    {
        return $this->belongsTo(Refrigerator::class);
    }

    protected function temperatureStatus(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->temperature < 2 || $this->temperature > 8) {
                    return 'Critical';
                }

                if ($this->temperature > 6) {
                    return 'Warning';
                }

                return 'Safe';
            }
        );
    }
}