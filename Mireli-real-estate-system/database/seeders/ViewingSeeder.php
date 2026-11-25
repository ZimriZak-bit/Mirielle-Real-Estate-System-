<?php


namespace Database\Seeders;

use App\Models\Property;
use App\Models\User;
use App\Models\Viewing;
use Illuminate\Database\Seeder;


class ViewingSeeder extends Seeder
{
    public function run(): void
    {
        $clients = User::query()
            ->where('role', User::ROLE_CLIENT)
            ->orderBy('email')
            ->get()
            ->keyBy('email');

        $amina = $clients->get('client1@mireli.test');
        $brian = $clients->get('client2@mireli.test');
        $carol = $clients->get('client3@mireli.test');

        $managerId = User::query()->where('email', 'manager@mireli.test')->value('id');

        // The three cheapest available houses,  so the demo uses stable, known data.
        $properties = Property::query()
            ->where('status', Property::AVAILABLE)
            ->orderBy('id')
            ->take(3)
            ->get();

        if (! $amina || ! $brian || ! $carol || $managerId === null || $properties->count() < 3) {
            $this->command?->warn('ViewingSeeder skipped: run UserSeeder and PropertySeeder first (needs 3 available properties).');

            return;
        }

        // Completed visit: this is what unlocks the deposit for Amina (I5).
        Viewing::updateOrCreate(
            ['property_id' => $properties[0]->id, 'client_id' => $amina->id],
            [
                'scheduled_at' => now()->subDays(2)->setTime(10, 0),
                'status' => Viewing::STATUS_COMPLETED,
                'completed_at' => now()->subDays(2)->setTime(11, 0),
                'management_note' => 'Visit completed.  The client may now pay the deposit.',
                'decided_by' =>  $managerId,
            ],
        );
/**Done by Jewel */

        Viewing::updateOrCreate(
            ['property_id' =>  $properties[1]->id, 'client_id' => $brian->id],
            [
                'scheduled_at' => now()->addDays(2)->setTime(11, 0),
                'status' => Viewing::STATUS_APPROVED,
                'management_note' => 'Confirmed by management.',
                'decided_by' => $managerId,
            ],
        );

        // A fresh request that management still has to decide on.
        Viewing::updateOrCreate(
            ['property_id' => $properties[2]->id, 'client_id' => $carol->id],
            [
                'scheduled_at' => now()->addDays(3)->setTime(14, 0),
                'status' => Viewing::STATUS_REQUESTED,
                'client_note' => 'I would like to see the house after work.',
            ],
        );
    }
}
