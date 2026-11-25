<?php


namespace Database\Seeders;

use App\Models\Customization;
use App\Models\Property;
use Illuminate\Database\Seeder;

/**
 * PAIR 3 demo data (interface I9): one saved set of customization preferences
 * for a house that is Reserved, so both the client form and the management
 * read-only page have something to show during the demo.
 *
 * Safe to run more than once: preferences are matched per client and property.
 */
class CustomizationSeeder  extends Seeder
{
    public function run(): void
    {
        // The first reserved house belongs to a client thanks to PropertySeeder.
        $property = Property::query()
            ->where('status', Property::RESERVED)
            ->whereNotNull('buyer_id')
            ->orderBy('id')
            ->first();

        if (! $property) {
            $this->command?->warn('CustomizationSeeder skipped: run PropertySeeder first (needs a reserved house with a buyer).');

            return;
        }
/**Done by Jewel */
        Customization::updateOrCreate(
            [
                'property_id' => $property->id,
                'client_id' => $property->buyer_id,
            ],
            [
                'furniture_arrangement' => 'Open plan living room with a corner sofa',
                'colour_scheme' => 'Warm neutrals with sage accents',
                'furnishing_status' => Customization::SEMI_FURNISHED,
                'notes' => 'Please leave the balcony empty for a small reading corner.',
            ],
        );
    }
}
