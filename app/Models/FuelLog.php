<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FuelLog extends Model
{
      protected $fillable = [
        'fuel_request_id',
        'vehicle_id',
        'driver_id',
        'litres_dispensed',
        'odometer_reading',
        'fuel_type',
        'station_name',
        'filled_at',
        'total_cost',
        'payment_method',
        'logged_by',
        'notes',
        'odometer_photo_path',
        'receipt_photo_path',
    ];

    protected $casts = [
        'filled_at'               => 'datetime',
        'litres_dispensed'        => 'decimal:2',
        'total_cost'              => 'decimal:2',
        'odometer_reading'        => 'integer',
    ];

    // Relationships
    public function fuelRequest()
    {
        return $this->belongsTo(FuelRequest::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function loggedBy()
    {
        return $this->belongsTo(User::class, 'logged_by');
    }

    // Accessors - Photos
    public function getOdometerPhotoUrlAttribute(): ?string
    {
        return $this->odometer_photo_path 
            ? Storage::disk('public')->url($this->odometer_photo_path) 
            : null;
    }

    public function getReceiptPhotoUrlAttribute(): ?string
    {
        return $this->receipt_photo_path 
            ? Storage::disk('public')->url($this->receipt_photo_path) 
            : null;
    }

    // Previous log for distance calculation
    public function previousLog()
    {
        return self::where('vehicle_id', $this->vehicle_id)
                   ->where('id', '<', $this->id)
                   ->orderByDesc('filled_at')
                   ->first();
    }

    public function getDistanceSinceLastAttribute(): ?int
    {
        $prev = $this->previousLog();
        return $prev ? ($this->odometer_reading - $prev->odometer_reading) : null;
    }

    /**
     * Litres per Kilometer
     */
    public function getLitresPerKmAttribute(): ?float
    {
        $distance = $this->distance_since_last;

        if (!$distance || $distance <= 0) {
            return null;
        }

        return round($this->litres_dispensed / $distance, 4);
    }

    public function getLitresPerMetreAttribute(): ?float
    {
        $distance = $this->distance_since_last;

        if (!$distance || $distance <= 0) {
            return null;
        }

        return round($this->litres_dispensed / ($distance * 1000), 6);
    }

    public function getKmPerLitreAttribute(): ?float
    {
        $distance = $this->distance_since_last;

        if (!$distance || $distance <= 0) {
            return null;
        }

        return round($distance / $this->litres_dispensed, 2);
    }

    public function getPreviousLitresPerMetre(int $limit = 5)
    {
        return self::where('vehicle_id', $this->vehicle_id)
            ->where('id', '<', $this->id)
            ->orderByDesc('filled_at')
            ->take($limit)
            ->get()
            ->map->litres_per_metre
            ->filter(fn ($value) => $value !== null);
    }

    public function getAveragePreviousLitresPerMetreAttribute(): ?float
    {
        $values = $this->getPreviousLitresPerMetre(5);

        if ($values->isEmpty()) {
            return null;
        }

        return round($values->average(), 7);
    }

    public function getBaselineLitresPerMetre(): ?float
    {
        return $this->getAveragePreviousLitresPerMetreAttribute();
    }

    public function getConsumptionDeltaPercentAttribute(): ?float
    {
        $baseline = $this->getBaselineLitresPerMetre();
        $current = $this->litres_per_metre;

        if (!$baseline || !$current) {
            return null;
        }

        return round((($current / $baseline) - 1) * 100, 2);
    }

    public function isSuspicious(float $thresholdMultiplier = 1.25): bool
    {
        $baseline = $this->getBaselineLitresPerMetre();
        $current = $this->litres_per_metre;

        if (!$baseline || !$current) {
            return false;
        }

        return $current > ($baseline * $thresholdMultiplier);
    }
}
