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

    // Accessors
    public function getOdometerPhotoUrlAttribute(): ?string
    {
        return $this->odometer_photo_path ? Storage::disk('public')->url($this->odometer_photo_path) : null;
    }

    public function getReceiptPhotoUrlAttribute(): ?string
    {
        return $this->receipt_photo_path ? Storage::disk('public')->url($this->receipt_photo_path) : null;
    }

    // Previous log for this vehicle (to calculate distance)
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
}
