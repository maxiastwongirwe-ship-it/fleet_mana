<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FuelRequest extends Model
{
        protected $fillable = [
        'requested_by',
        'vehicle_id',
        'requested_amount',
        'fuel_type',
        'reason',
        'odometer_reading',
        'odometer_photo_path',         // path to odometer photo (requested phase)
        'status',
        'approved_by',
        'approved_at',
        'admin_notes',
        'actual_litres_dispensed',
        'price_per_litre',
        'station_name',
        'total_cost',
        'payment_method',
        'promocode',
        'bank_account',
        'card_details',
        'receipt_photo_path',          // path to receipt photo (completion phase)
        'fillup_notes',

        'theft_prediction',
'theft_prediction_message',
'expected_litres',
'fuel_difference',
    ];

    protected $casts = [
        'requested_at'            => 'datetime',
        'approved_at'             => 'datetime',
        'actual_litres_dispensed' => 'decimal:2',
        'price_per_litre'         => 'decimal:2',
        'total_cost'              => 'decimal:2',
        'odometer_reading'        => 'integer',
    ];

    // Relationships
    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function paymentRequest()
    {
        return $this->hasOne(PaymentRequest::class, 'fuel_request_id');
    }

    // Photo URL accessors (public disk)
    public function getOdometerPhotoUrlAttribute(): ?string
    {
        return $this->odometer_photo_path
            ? Storage::disk('public')->url($this->odometer_photo_path)
            : null;
    }

    public function getReceiptPhotoUrlAttribute(): ?string
    {
        return $this->receipt_photo_path
            ? Storage::disk('public')->url($this->receipt_photo_path)
            : null;
    }

    // Status helper methods (used in views & controllers)
    public function isRequested(): bool
    {
        return $this->status === 'requested';
    }

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

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPaymentPending(): bool
    {
        return $this->status === 'payment_pending';
    }

    public function isPaymentApproved(): bool
    {
        return $this->status === 'payment_approved';
    }

    public function isPaymentRejected(): bool
    {
        return $this->status === 'payment_rejected';
    }

    // Optional: human-friendly status label
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'requested'       => 'Requested',
            'pending'         => 'Pending Approval',
            'approved'        => 'Approved – Ready to Fill',
            'rejected'        => 'Rejected',
            'completed'       => 'Filled & Completed',
            'payment_pending' => 'Payment Requested',
            'payment_approved'=> 'Payment Approved',
            'payment_rejected'=> 'Payment Rejected',
            default           => ucfirst($this->status ?? 'Unknown'),
        };
    }
public function fuelLog()
{
    return $this->hasOne(FuelLog::class);
}

}
