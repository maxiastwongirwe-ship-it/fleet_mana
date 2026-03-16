<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Worker extends Model
{
      use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'work_id',
        'nin',
        'department',
        'position',
        'hire_date',
        'contract_end_date',
        'tin',
        'nssf_number',
        'bank_name',
        'bank_account_number',
        'employment_type',
        'has_uniform',
        'has_id_card',
        'notes',
    ];

    protected $casts = [
        'hire_date'         => 'date:Y-m-d',
        'contract_end_date' => 'date:Y-m-d',
        'has_uniform'       => 'boolean',
        'has_id_card'       => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────────

    public function getFullNameAttribute(): ?string
    {
        return $this->user?->name;
    }

    public function isActive(): bool
    {
        return $this->user?->is_active ?? false;
    }
}
