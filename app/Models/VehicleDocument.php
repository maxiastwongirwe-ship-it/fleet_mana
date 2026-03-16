<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class VehicleDocument extends Model
{
    protected $fillable = [
        'vehicle_id',
        'document_type',
        'document_number',
        'issue_date',
        'expiry_date',
        'file_path',
        'uploaded_by',
        'is_valid',
        'notes',
    ];

    protected $casts = [
        'issue_date'    => 'date',
        'expiry_date'   => 'date',
        'is_valid'      => 'boolean',
    ];

    // Relationships
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Accessor: full URL for document file
    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::disk('public')->url($this->file_path) : null;
    }

    // Helper: days until expiry
    public function getDaysToExpiryAttribute(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }

        return now()->diffInDays($this->expiry_date, false);
    }

    // Helper: expiry status badge class
    public function getExpiryStatusClassAttribute(): string
    {
        if (!$this->expiry_date) {
            return 'bg-gray-100 text-gray-800';
        }

        $days = $this->daysToExpiry;

        if ($days < 0) {
            return 'bg-red-100 text-red-800';
        }

        if ($days <= 30) {
            return 'bg-yellow-100 text-yellow-800';
        }

        return 'bg-green-100 text-green-800';
 
        }

}
