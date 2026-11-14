<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PAIR 3: a viewing request.
 *
 * Status flow (frozen by interface I10):
 *   requested -> approved -> completed
 *   requested -> rejected
 *   approved -> reschedule_requested -> approved
 *   any live viewing -> cancelled
 *
 * Live viewings (requested, approved, reschedule_requested) hold their slot.
 * Cancelled and rejected viewings keep their row for history but free the slot.
 */
#[Fillable([
    'property_id',
    'client_id',
    'scheduled_at',
    'proposed_at',
    'status',
    'client_note',
    'reschedule_note',
    'management_note',
    'completed_at',
    'decided_by',
])]
class Viewing extends Model
{
    public const STATUS_REQUESTED = 'requested';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_RESCHEDULE_REQUESTED = 'reschedule_requested';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    /** The frozen vocabulary from interface I10, in display order. */
    public const STATUSES = [
        self::STATUS_REQUESTED,
        self::STATUS_APPROVED,
        self::STATUS_RESCHEDULE_REQUESTED,
        self::STATUS_COMPLETED,
        self::STATUS_REJECTED,
        self::STATUS_CANCELLED,
    ];

    /** Statuses that still hold a time slot. */
    public const LIVE_STATUSES = [
        self::STATUS_REQUESTED,
        self::STATUS_APPROVED,
        self::STATUS_RESCHEDULE_REQUESTED,
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'proposed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function scopeLive(Builder $query): Builder
    {
        return $query->whereIn('status', self::LIVE_STATUSES);
    }

    /** Requests waiting for management: new requests and proposed new times. */
    public function scopeAwaitingManagement(Builder $query): Builder
    {
        return $query->whereIn('status', [
            self::STATUS_REQUESTED,
            self::STATUS_RESCHEDULE_REQUESTED,
        ]);
    }

    public function isLive(): bool
    {
        return in_array($this->status, self::LIVE_STATUSES, true);
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRequested(): bool
    {
        return $this->status === self::STATUS_REQUESTED;
    }

    public function isRescheduleRequested(): bool
    {
        return $this->status === self::STATUS_RESCHEDULE_REQUESTED;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isAwaitingManagement(): bool
    {
        return in_array($this->status, [
            self::STATUS_REQUESTED,
            self::STATUS_RESCHEDULE_REQUESTED,
        ], true);
    }

    public function canBeCancelled(): bool
    {
        return $this->isLive();
    }

    public function statusLabel(): string
    {
        return ucfirst(str_replace('_', ' ', (string) $this->status));
    }

    /**
     * INTERFACE I5: true only if this client has a completed viewing of this
     * property. Pair 4 gates deposits on this; Pair 2 hides the deposit button.
     */
    public static function isCompletedFor(User $client, Property $property): bool
    {
        return static::query()
            ->where('property_id', $property->getKey())
            ->where('client_id', $client->getKey())
            ->where('status', self::STATUS_COMPLETED)
            ->exists();
    }

    /**
     * PAIR 3 clash check: no two live viewings of the same property at the same
     * time. Cancelled and rejected viewings do not block a slot.
     *
     * @param  CarbonInterface  $slot  the slot being requested, approved or moved to
     * @param  int|null  $exceptViewingId  the viewing being moved, so it does not clash with itself
     */
    public static function slotTaken(int $propertyId, CarbonInterface $slot, ?int $exceptViewingId = null): bool
    {
        return static::query()
            ->where('property_id', $propertyId)
            ->whereIn('status', self::LIVE_STATUSES)
            ->where('scheduled_at', $slot)
            ->when($exceptViewingId, fn (Builder $query) => $query->whereKeyNot($exceptViewingId))
            ->exists();
    }
}
