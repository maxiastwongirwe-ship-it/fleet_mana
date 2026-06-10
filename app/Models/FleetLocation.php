<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FleetLocation extends Model
{
     protected $fillable = [
        'driver_id',
        'vehicle_id',
        'latitude',
        'longitude',
        'accuracy',
        'speed',
    ];

    public function driver()
    {
        return $this->belongsTo(
            User::class,
            'driver_id'
        );
    }

    public function vehicle()
    {
        return $this->belongsTo(
            Vehicle::class
        );
    }
    
}
