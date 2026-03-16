<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportRequestPassenger extends Model
{
      protected $fillable = [
        'transport_request_id',
        'passenger_name',
        'user_id',
    ];

    public function request()
    {
        return $this->belongsTo(TransportRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
