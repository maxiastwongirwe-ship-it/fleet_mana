<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Vehicle extends Model
{
     protected $fillable = [
        'plate_number',
        'make',
        'model',
        'year',
        'type',
        'capacity',
        'fuel_type',
        'fuel_tank_capacity',
        'current_odometer',
        'vehicle_photo_path',
        'assigned_driver_id',
        'status',
         'tracking_token',
    'tracking_token_expires_at',
    ];

    protected $casts = [
    'type'                      => 'string',
    'status'                    => 'string',
    'year'                      => 'integer',
    'capacity'                  => 'integer',
    'fuel_tank_capacity'        => 'decimal:2',
    'current_odometer'          => 'integer',
    'tracking_token_expires_at' => 'datetime', 
];


    // Accessor for photo URL
    public function getVehiclePhotoUrlAttribute(): ?string
    {
        return $this->vehicle_photo_path ? Storage::disk('public')->url($this->vehicle_photo_path) : null;
    }

    // Helper: human-readable type
    public function getTypeDisplayAttribute(): string
    {
        return ucfirst($this->type);
    }
    //vehicle documents
    public function documents()
{
    return $this->hasMany(VehicleDocument::class);
}
    public function fuelRequests()
{
    return $this->hasMany(FuelRequest::class);
}
public function fuelLogs()
{
    return $this->hasMany(FuelLog::class);
}

public function transportTrips()
{
    return $this->hasMany(TransportTrip::class);
}

public function isAvailable(): bool
{
    // Example logic - adjust to your real vehicle status/availability rules
    return $this->status === 'available' 
        || $this->status === 'active' 
        || $this->status === 'idle';

    // Alternative examples you can use:
    // return $this->status !== 'maintenance' && $this->status !== 'out_of_service';
    // return !$this->transportTrips()->whereIn('status', ['scheduled', 'active'])->exists();
}

// Vehicle.php
public function locationLogs()
{
    return $this->hasMany(LocationLog::class);
}

// User.php
public function locationLogsAsDriver()
{
    return $this->hasMany(LocationLog::class, 'driver_id');
}
/**
 * Get the most recent location log for this vehicle
 */
public function latestLocation()
{
    return $this->hasOne(LocationLog::class)->latestOfMany();
}
public function assignedDriver()
{
    return $this->belongsTo(User::class, 'assigned_driver_id');
}
public function isTrackingTokenValid(): bool
{
    return $this->tracking_token 
        && $this->tracking_token_expires_at 
        && $this->tracking_token_expires_at->isFuture();
}

// Add this method if not already there
public function getFromTrackingToken($token)
{
    return self::where('tracking_token', $token)->first();
}

}
