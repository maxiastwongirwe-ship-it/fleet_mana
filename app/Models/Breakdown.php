<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Breakdown extends Model
{
   protected $fillable = [
        'vehicle_id',
        'driver_id',
        'approved_by',
        'location',
        'description',
        'occurred_at',
        'severity',
        'status',
        'estimated_cost',
        'actual_cost',
        'admin_notes',
        'photo_paths',
        'approved',
    ];

    protected $casts = [
        'occurred_at'     => 'datetime',
        'estimated_cost'  => 'decimal:2',
        'actual_cost'     => 'decimal:2',
        'photo_paths'     => 'array',
        'approved'        => 'boolean',
    ];

    // Relationships
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            'reported'     => 'bg-yellow-100 text-yellow-800',
            'acknowledged' => 'bg-blue-100 text-blue-800',
            'in_progress'  => 'bg-purple-100 text-purple-800',
            'resolved'     => 'bg-green-100 text-green-800',
            'rejected'     => 'bg-red-100 text-red-800',
            default        => 'bg-gray-100 text-gray-800',
        };
    }

    // Helper methods
    public function isApproved(): bool
    {
        return (bool) $this->approved;
    }

    public function canBeApprovedBy(User $user): bool
    {
        // Fixed: use direct property access instead of missing method
        return $user->isAdmin() && !$this->approved;
    }
}
