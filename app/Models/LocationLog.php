<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationLog extends Model
{
      protected $fillable = [
        'vehicle_id',
        'driver_id',
        'latitude',
        'longitude',
        'accuracy',
        'speed',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
