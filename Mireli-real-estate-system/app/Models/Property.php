<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * STARTER PACK placeholder, owned by Pair 2 (Claire & Norman).
 *
 * Pair 3 only relies on the frozen parts of interface I3 (isAvailable,
 * markReserved, markSold, markAvailable, balanceAfterDeposit) and on the
 * status/buyer columns used by interface I7. Pair 2 replaces or extends this
 * model; nobody sets `status` directly, always through the helpers below.
 */
#[Fillable([
    'title',
    'location',
    'bedrooms',
    'size',
    'amenities',
    'description',
    'price',
    'deposit_amount',
    'status',
    'buyer_id',
])]
class Property extends Model
{
    public const STATUS_AVAILABLE = 'available';

    public const STATUS_RESERVED = 'reserved';

    public const STATUS_SOLD = 'sold';

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'bedrooms' => 'integer',
        ];
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function viewings(): HasMany
    {
        return $this->hasMany(Viewing::class);
    }

    public function customizations(): HasMany
    {
        return $this->hasMany(Customization::class);
    }

    /** I3: only an available house can be viewed or have a deposit paid. */
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    /** I3: status and buyer move together, in one save. */
    public function markReserved(User $buyer): void
    {
        $this->forceFill([
            'status' => self::STATUS_RESERVED,
            'buyer_id' => $buyer->getKey(),
        ])->save();
    }

    /** I3: a house becomes Sold once the balance is fully paid. */
    public function markSold(): void
    {
        $this->forceFill(['status' => self::STATUS_SOLD])->save();
    }

    /** I3: a cancelled deposit frees the house and clears the buyer. */
    public function markAvailable(): void
    {
        $this->forceFill([
            'status' => self::STATUS_AVAILABLE,
            'buyer_id' => null,
        ])->save();
    }

    /** I3: what the client still owes after paying the deposit. */
    public function balanceAfterDeposit(): string
    {
        if (function_exists('bcsub')) {
            return bcsub((string) $this->price, (string) $this->deposit_amount, 2);
        }

        return number_format((float) $this->price - (float) $this->deposit_amount, 2, '.', '');
    }
}
