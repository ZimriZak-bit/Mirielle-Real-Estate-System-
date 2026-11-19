<?php

namespace Tests\Unit;

use App\Models\Property;
use App\Models\User;
use App\Models\Viewing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * PAIR 3: the rules that live on the Viewing model, tested without HTTP.
 * Run with: php artisan test --filter=ViewingModelTest
 */
class ViewingModelTest extends TestCase
{
    use RefreshDatabase;

    private function property(): Property
    {
        return Property::create([
            'title' => 'Karen Cottage',
            'location' => 'Karen, Nairobi',
            'bedrooms' => 4,
            'size' => '300 sqm',
            'price' => '15000000.00',
            'deposit_amount' => '1500000.00',
            'status' => Property::AVAILABLE,
        ]);
    }

    private function viewing(Property $property, User $client, string $status, string $slot): Viewing
    {
        return Viewing::create([
            'property_id' => $property->id,
            'client_id' => $client->id,
            'scheduled_at' => $slot,
            'status' => $status,
        ]);
    }

    public function test_slot_taken_only_counts_live_viewings(): void
    {
        $property = $this->property();
        $client = User::factory()->create();
        $slot = now()->addDays(2)->setTime(9, 0);

        $cancelled = $this->viewing($property, $client, Viewing::STATUS_CANCELLED, $slot->toDateTimeString());
        $this->assertFalse(Viewing::slotTaken($property->id, $slot));

        $rejected = $this->viewing($property, $client, Viewing::STATUS_REJECTED, $slot->toDateTimeString());
        $this->assertFalse(Viewing::slotTaken($property->id, $slot));

        $requested = $this->viewing($property, $client, Viewing::STATUS_REQUESTED, $slot->toDateTimeString());
        $this->assertTrue(Viewing::slotTaken($property->id, $slot));

        // A different property at the same time is not a clash.
        $otherProperty = $this->property();
        $this->assertFalse(Viewing::slotTaken($otherProperty->id, $slot));

        // A moved viewing must not clash with itself.
        $moved = now()->addDays(3)->setTime(9, 0);
        $this->assertFalse(Viewing::slotTaken($property->id, $requested->scheduled_at, $requested->id));
        $this->assertTrue(Viewing::slotTaken($property->id, $moved->copy()->subDay(), null));
    }

    public function test_is_completed_for_ignores_other_statuses_and_other_clients(): void
    {
        $property = $this->property();
        $client = User::factory()->create();
        $otherClient = User::factory()->create();
        $slot = now()->addDay()->setTime(11, 0);

        $this->viewing($property, $client, Viewing::STATUS_APPROVED, $slot->toDateTimeString());
        $this->assertFalse(Viewing::isCompletedFor($client, $property));

        $this->viewing($property, $otherClient, Viewing::STATUS_COMPLETED, $slot->toDateTimeString());
        $this->assertTrue(Viewing::isCompletedFor($otherClient, $property));
        $this->assertFalse(Viewing::isCompletedFor($client, $property));

        $this->viewing($property, $client, Viewing::STATUS_COMPLETED, $slot->copy()->addHours(2)->toDateTimeString());
        $this->assertTrue(Viewing::isCompletedFor($client, $property));
    }

    public function test_property_status_helpers_move_status_and_buyer_together(): void
    {
        $property = $this->property();
        $buyer = User::factory()->create();

        $this->assertTrue($property->isAvailable());

        $property->markReserved($buyer);
        $property->refresh();
        $this->assertSame(Property::RESERVED, $property->status);
        $this->assertSame($buyer->id, $property->buyer_id);

        $property->markSold();
        $property->refresh();
        $this->assertSame(Property::SOLD, $property->status);

        $property->markAvailable();
        $property->refresh();
        $this->assertSame(Property::AVAILABLE, $property->status);
        $this->assertNull($property->buyer_id);
    }
}
