<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportRequest extends Model
{
     protected $fillable = [
    'request_type',
    'requested_by',
    'pickup_location',
    'dropoff_location',
    'pickup_time',
    'purpose',
    'status',
    'admin_notes',

     'pickup_lat',      // ← new
    'pickup_lng',      // ← new
    'dropoff_lat',     // ← new
    'dropoff_lng',     // ← new
];

    protected $casts = [
        'pickup_time' => 'datetime',
        'status'      => 'string',


         'pickup_time'   => 'datetime',
    'pickup_lat'    => 'decimal:8',
    'pickup_lng'    => 'decimal:8',
    'dropoff_lat'   => 'decimal:8',
    'dropoff_lng'   => 'decimal:8',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function passengers()
    {
        return $this->hasMany(TransportRequestPassenger::class);
    }

    public function trips()
    {
        return $this->belongsToMany(TransportTrip::class, 'trip_transport_requests');
    }

    // Status helpers
    public function isPending()   { return $this->status === 'pending';   }
    public function isApproved()  { return $this->status === 'approved';  }
    public function isRejected()  { return $this->status === 'rejected';  }
    public function isGrouped()   { return $this->status === 'grouped';   }
    public function isAssigned()  { return $this->status === 'assigned';  }
    public function isCompleted() { return $this->status === 'completed'; }
}
