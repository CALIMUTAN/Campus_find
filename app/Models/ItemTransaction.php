<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemTransaction extends Model
{
    public const TYPE_DROP_OFF = 'drop_off';
    public const TYPE_PICKUP = 'pickup';

    protected $fillable = [
        'report_id',
        'claim_id',
        'recorded_by',
        'type',
        'identity_verified',
        'notes',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'identity_verified' => 'boolean',
            'recorded_at' => 'datetime',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
