<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    public const CATEGORIES = [
        'Bags',
        'Electronics',
        'IDs and wallets',
        'Clothing',
        'Keys',
        'Books',
        'Other',
    ];

    // Lost items start here and stay here until a matching found item turns up.
    public const STATUS_REPORTED = 'Reported';

    // Found items start here: logged, but still waiting on the physical drop-off.
    public const STATUS_PENDING_DROP_OFF = 'Pending Drop-off';

    // Set by staff once the found item has actually been handed in.
    public const STATUS_IN_CUSTODY = 'In Custody';

    // Set once the owner has verified their identity and taken the item back.
    public const STATUS_CLAIMED = 'Claimed';

    protected $fillable = [
        'user_id',
        'type',
        'item_name',
        'category',
        'place',
        'date',
        'description',
        'photo_path',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Public URL for the uploaded photo, or null when no photo was attached.
     */
    public function photoUrl(): ?string
    {
        return $this->photo_path ? Storage::disk('public')->url($this->photo_path) : null;
    }
        public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }
        public const STATUSES = [
        self::STATUS_REPORTED,
        self::STATUS_PENDING_DROP_OFF,
        self::STATUS_IN_CUSTODY,
        self::STATUS_CLAIMED,
    ];
}