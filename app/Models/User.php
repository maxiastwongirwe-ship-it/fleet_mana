<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
         'phone',
        'role',                    // Added – critical for role system
        'is_active',
        'approved',                // Added – for driver approval
        'tracking_token',
        'current_team_id',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string|class-string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'         => 'datetime',
            'is_active'                 => 'boolean',
            'approved'                  => 'boolean',       // Added – treat as true/false
            'two_factor_secret'         => 'encrypted',
            'two_factor_recovery_codes' => 'array',
        ];
    }

    // ================================================================
    // RELATIONSHIPS
    // ================================================================

    public function driverProfile()
    {
        return $this->hasOne(Driver::class);
    }

    public function workerProfile()
    {
        return $this->hasOne(Worker::class);
    }

    public function assignedVehicle()
    {
        return $this->hasOne(Vehicle::class, 'assigned_driver_id');
    }

    public function fuelRequests()
    {
        return $this->hasMany(FuelRequest::class, 'driver_id');
    }

    public function fuelLogs()
    {
        return $this->hasMany(FuelLog::class, 'driver_id');
    }

    public function locationLogsAsDriver()
    {
        return $this->hasMany(LocationLog::class, 'driver_id');
    }

    public function transportTripsAsDriver()
    {
        return $this->hasMany(TransportTrip::class, 'driver_id');
    }

    // ================================================================
    // HELPER METHODS
    // ================================================================

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin_level_1', 'admin_level_2']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'admin_level_1';
    }

    public function isDriver(): bool
    {
        return $this->role === 'driver';
    }

    public function isWorker(): bool
    {
        return $this->role === 'worker';
    }

    public function isApproved(): bool
    {
        return (bool) $this->approved;
    }

    public function getRoleDisplayAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->role ?? 'User'));
    }
}
