<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportTrip extends Model
{
   protected $fillable = [
        'vehicle_id',
        'driver_id',
        'departure_time',
        'estimated_arrival_time',
        'actual_arrival_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'departure_time'        => 'datetime',
        'estimated_arrival_time'=> 'datetime',
        'actual_arrival_time'   => 'datetime',
        'status'                => 'string',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function requests()
    {
        return $this->belongsToMany(TransportRequest::class, 'trip_transport_requests');
    }

    // Status helpers
    public function isScheduled() { return $this->status === 'scheduled'; }
    public function isActive()    { return $this->status === 'active';    }
    public function isCompleted() { return $this->status === 'completed'; }
    public function isCancelled() { return $this->status === 'cancelled'; }

    // New helper methods
    public function canStartTrip(): bool
    {
        return $this->isScheduled();
    }

    public function canFinishTrip(): bool
    {
        return $this->isActive();
    }
}
