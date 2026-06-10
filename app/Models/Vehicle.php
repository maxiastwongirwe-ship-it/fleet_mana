<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\FuelLog;
use Illuminate\Support\Str;


class Vehicle extends Model
{
          use HasFactory;

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

    // =============================================
    //              MODEL EVENTS
    // =============================================

    

    // =============================================
    //              ACCESSORS
    // =============================================

    public function getVehiclePhotoUrlAttribute(): ?string
    {
        return $this->vehicle_photo_path 
            ? Storage::disk('public')->url($this->vehicle_photo_path) 
            : null;
    }

    public function getTypeDisplayAttribute(): string
    {
        return ucfirst($this->type);
    }

    // =============================================
    //              RELATIONSHIPS
    // =============================================

    public function documents()
    {
        return $this->hasMany(VehicleDocument::class);
    }

    public function fuelRequests()
    {
        return $this->hasMany(FuelRequest::class);
    }


    public function transportTrips()
    {
        return $this->hasMany(TransportTrip::class);
    }


   

    public function assignedDriver()
    {
        return $this->belongsTo(User::class, 'assigned_driver_id');
    }

    // =============================================
    //              HELPERS
    // =============================================

    public function isAvailable(): bool
    {
        return $this->status === 'available' 
            || $this->status === 'active' 
            || $this->status === 'idle';
    }

  
    public function getFromTrackingToken($token)
    {
        return self::where('tracking_token', $token)->first();
    }

    // =============================================
    //     FUEL CONSUMPTION & THEFT DETECTION
    // =============================================

    public function getBaselineLitresPerKm(): ?float
    {
        $logs = FuelLog::where('vehicle_id', $this->id)
            ->whereHas('previousLog')
            ->orderBy('filled_at')
            ->take(5)
            ->get();

        if ($logs->count() < 3) {
            return null;
        }

        $total = $logs->sum('litres_per_km');
        return round($total / $logs->count(), 4);
    }

    public function getConsumptionStats(): array
    {
        $logs = FuelLog::where('vehicle_id', $this->id)
            ->whereHas('previousLog')
            ->orderByDesc('filled_at')
            ->take(10)
            ->get();

        if ($logs->isEmpty()) {
            return [
                'baseline_l_per_km'   => null,
                'recent_avg_l_per_km' => null,
                'suspicious_count'    => 0,
                'total_logs'          => 0,
            ];
        }

        $baseline = $this->getBaselineLitresPerKm();
        $recentAvg = round($logs->avg('litres_per_km'), 4);
        $suspicious = $logs->filter(fn($log) => $log->isSuspicious(1.25))->count();

        return [
            'baseline_l_per_km'     => $baseline,
            'recent_avg_l_per_km'   => $recentAvg,
            'suspicious_count'      => $suspicious,
            'total_logs'            => $logs->count(),
        ];
    }

    // app/Models/Vehicle.php
public function isTrackingTokenValid()
{
    // No more expiry check for persistent tracking
    return !empty($this->tracking_token);
}

// Optional: Add this helper
public static function boot()
{
    parent::boot();
    static::creating(function ($vehicle) {
        if (empty($vehicle->tracking_token)) {
            $vehicle->tracking_token = Str::random(60); // longer token
        }
    });
}

public function latestLocation()
{
    return $this->hasOne(
        FleetLocation::class,
        'vehicle_id'
    );
}

public function locationLogs()
{
    return $this->hasMany(
        LocationLog::class,
        'vehicle_id'
    );
}

// =======================================================
// FUEL THEFT DETECTION ENGINE
// =======================================================

public function fuelLogs()
{
    return $this->hasMany(FuelLog::class);
}

/**
 * Get last N fuel efficiency logs (valid only)
 */
public function recentFuelEfficiencyLogs(int $limit = 5)
{
    return FuelLog::where('vehicle_id', $this->id)
        ->orderByDesc('filled_at')
        ->take($limit)
        ->get()
        ->filter(fn($log) => $log->litres_per_km !== null);
}

/**
 * Baseline consumption (average litres/km)
 */
public function baselineLitresPerKm(): ?float
{
    $logs = $this->recentFuelEfficiencyLogs(5);

    if ($logs->count() < 3) {
        return null;
    }

    return round($logs->avg('litres_per_km'), 4);
}

/**
 * Expected fuel for given distance
 */
public function expectedFuel(float $distanceKm): ?float
{
    $baseline = $this->baselineLitresPerKm();

    if (!$baseline) {
        return null;
    }

    return round($baseline * $distanceKm, 2);
}

/**
 * Main fuel theft detection engine
 */



public function getLastFiveConsumptionAverage(): ?float
{
    $logs = FuelLog::where('vehicle_id', $this->id)
        ->orderByDesc('filled_at')
        ->take(5)
        ->get();

    if ($logs->count() < 2) {
        return null;
    }

    $totalDistance = 0;
    $totalFuel = 0;

    foreach ($logs as $log) {

        $previous = FuelLog::where('vehicle_id', $this->id)
            ->where('id', '<', $log->id)
            ->orderByDesc('filled_at')
            ->first();

        if (!$previous) {
            continue;
        }

        $distance = $log->odometer_reading - $previous->odometer_reading;

        if ($distance <= 0) {
            continue;
        }

        $totalDistance += $distance;
        $totalFuel += $log->litres_dispensed;
    }

    if ($totalDistance <= 0 || $totalFuel <= 0) {
        return null;
    }

    /**
     * litres per kilometer
     */
    return round($totalFuel / $totalDistance, 4);
}

public function predictExpectedFuelUsage(
    int $currentOdometer
): ?array {

    $averageConsumption =
        $this->getLastFiveConsumptionAverage();

    if (!$averageConsumption) {
        return null;
    }

    $lastLog = FuelLog::where(
        'vehicle_id',
        $this->id
    )
    ->latest('filled_at')
    ->first();

    if (!$lastLog) {
        return null;
    }

    $distanceTravelled =
        $currentOdometer -
        $lastLog->odometer_reading;

    if ($distanceTravelled <= 0) {
        return null;
    }

    /**
     * Expected litres
     */
    $expectedFuel =
        $distanceTravelled *
        $averageConsumption;

    return [

        'average_consumption' =>
            round($averageConsumption, 4),

        'distance_travelled' =>
            $distanceTravelled,

        'expected_fuel' =>
            round($expectedFuel, 2),
    ];
}
public function detectFuelTheft(
    float $requestedLitres,
    int $currentOdometer
): array {

    $prediction =
        $this->predictExpectedFuelUsage(
            $currentOdometer
        );

    if (!$prediction) {

        return [

            'status' =>
                'INSUFFICIENT DATA',

            'message' =>
                'Not enough historical fuel data.',

            'difference' => 0,
        ];
    }

    $difference =
        $requestedLitres -
        $prediction['expected_fuel'];

    $suspected = $difference > 10;

    return [

        'status' => $suspected
            ? 'SUSPECTED FUEL THEFT'
            : 'NO SUSPICION FOUND',

        'message' => $suspected
            ? 'Fuel request exceeds expected consumption.'
            : 'Fuel consumption appears normal.',

        'difference' =>
            round($difference, 2),

        'expected_fuel' =>
            $prediction['expected_fuel'],

        'average_consumption' =>
            $prediction['average_consumption'],

        'distance_travelled' =>
            $prediction['distance_travelled'],
    ];
}
}
