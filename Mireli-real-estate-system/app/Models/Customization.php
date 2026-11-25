<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PAIR 3: a client's furniture, colour and furnishing preferences for one
 * property. There is exactly one set per client per property, and the client
 * may edit it while they own the house (interface I7).
 */
class  Customization extends Model
{
    public  const FURNISHED = 'furnished';

    public const SEMI_FURNISHED = 'semi_furnished';

    public const UNFURNISHED = 'unfurnished';

    /** Frozen furnishing values for the dropdown. */

    /**Done by Jewel */
    
    public const FURNISHING_STATUSES = [
        self::FURNISHED,
        self::SEMI_FURNISHED,
        self::UNFURNISHED,
    ];

    protected $fillable = [
        'property_id',
        'client_id',
        'furniture_arrangement',
        'colour_scheme',
        'furnishing_status',
        'notes',
    ];

    public  function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /** @return array<string, string> value => label, for the form dropdown */
    public static function furnishingOptions(): array
    {
        return [
            self::FURNISHED => 'Furnished',
            self::SEMI_FURNISHED => 'Semi-furnished',
            self::UNFURNISHED => 'Unfurnished',
        ];
    }

    public function furnishingLabel(): string
    {
        return self::furnishingOptions()[$this->furnishing_status] ?? 'No preference';
    }
}
