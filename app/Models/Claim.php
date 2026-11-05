<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Claim extends Model
{
    public const STATUS_PENDING = 'Pending';
    public const STATUS_APPROVED = 'Approved';   // approved, awaiting pickup
    public const STATUS_REJECTED = 'Rejected';
    public const STATUS_PICKED_UP = 'Picked Up';

    // A claim is "active" while it can still lead to the item being handed over.
    public const ACTIVE = [self::STATUS_PENDING, self::STATUS_APPROVED];

    protected $fillable = ['user_id', 'report_id', 'proof', 'status'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function report(): BelongsTo { return $this->belongsTo(Report::class); }
}