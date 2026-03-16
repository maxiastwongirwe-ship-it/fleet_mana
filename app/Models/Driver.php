<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Driver extends Model
{   
    protected $fillable = [
        'user_id',
        'license_number',
        'license_category',
        'license_issue_date',
        'license_expiry_date',
        'nin_number',
        'driver_photo_path',
        'status',
        'approved',
        'tracking_token',
        'tracking_token_expires_at',
    ];
protected $casts = [
    'license_issue_date'        => 'date',
    'license_expiry_date'       => 'date',
    'approved'                  => 'boolean',
    'tracking_token_expires_at' => 'datetime',  
];
    // Relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor: full URL for driver photo
    public function getDriverPhotoUrlAttribute(): ?string
    {
        return $this->driver_photo_path
            ? Storage::disk('public')->url($this->driver_photo_path)
            : null;
    }

    // Helper: formatted expiry status
    public function getLicenseStatusAttribute(): string
    {
        if (!$this->license_expiry_date) {
            return 'Not set';
        }

        $daysLeft = now()->diffInDays($this->license_expiry_date, false);

        if ($daysLeft < 0) {
            return 'Expired ' . abs($daysLeft) . ' days ago';
        }

        if ($daysLeft <= 30) {
            return 'Expiring in ' . $daysLeft . ' days';
        }

        return 'Valid';
    }

    public function fuelLogs()
{
    return $this->hasMany(FuelLog::class, 'driver_id');
}

public function transportTripsAsDriver()
{
    return $this->hasMany(TransportTrip::class, 'driver_id');
}



    // Check if current tracking token is still valid
    public function isTrackingTokenValid(): bool
    {
        return $this->tracking_token 
            && $this->tracking_token_expires_at 
            && $this->tracking_token_expires_at->isFuture();
    }
}
