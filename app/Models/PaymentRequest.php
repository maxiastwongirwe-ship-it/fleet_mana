<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    protected $fillable = [
        'fuel_request_id',
        'requested_by',
        'amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function fuelRequest()
    {
        return $this->belongsTo(FuelRequest::class, 'fuel_request_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    // Status helpers
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default    => ucfirst($this->status ?? 'Unknown'),
        };
    }
    public function paymentRequest()
{
    return $this->hasOne(PaymentRequest::class);
}
}
