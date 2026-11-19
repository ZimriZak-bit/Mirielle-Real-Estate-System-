<?php

namespace Tests\Feature;

use App\Models\Customization;
use App\Models\Property;
use App\Models\User;
use App\Models\Viewing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * PAIR 3 testing duty: clashing slots are rejected, a cancelled viewing frees
 * its slot, the clash check ignores cancelled viewings, and the completion
 * check is true only after a viewing is marked completed.
 *
 * Run with: php artisan test --filter=ViewingTest
 */
class ViewingTest extends TestCase
{
    use RefreshDatabase;

    private function property(array $overrides = []): Property
    {
        return Property::create(array_merge([
            'title' => 'Kilimani Apartment',
            'location' => 'Kilimani, Nairobi',
            'bedrooms' => 3,
            'size' => '140 sqm',
            'amenities' => 'Parking, borehole',
            'description' => 'A three bedroom apartment close to the shops.',
            'price' => '8500000.00',
            'deposit_amount' => '850000.00',
            'status' => Property::AVAILABLE,
        ], $overrides));
    }

    /** A form-ready datetime, a few days from now. */
    private function slot(int $days = 3, int $hour = 10): string
    {
        return now()->addDays($days)->setTime($hour, 0)->format('Y-m-d\TH:i');
    }

    private function viewing(User $client, Property $property, string $status = Viewing::STATUS_REQUESTED, int $days = 3): Viewing
    {
        return Viewing::create([
            'property_id' => $property->id,
            'client_id' => $client->id,
            'scheduled_at' => now()->addDays($days)->setTime(10, 0),
            'status' => $status,
        ]);
    }

    public function test_guests_are_sent_to_login(): void
    {
        $property = $this->property();

        $this->get('/viewings')->assertRedirect(route('login'));
        $this->get('/viewings/create/'.$property->id)->assertRedirect(route('login'));
        $this->get('/customizations')->assertRedirect(route('login'));
        $this->get('/management/viewings')->assertRedirect(route('login'));
        $this->get('/management/customizations')->assertRedirect(route('login'));
    }

    public function test_a_client_can_request_a_viewing_on_an_available_property(): void
    {
        $client = User::factory()->create();
        $property = $this->property();

        $this->actingAs($client)
            ->post(route('viewings.store', $property), [
                'scheduled_at' => $this->slot(),
                'client_note' => 'After work please.',
            ])
            ->assertRedirect(route('viewings.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('viewings', [
            'property_id' => $property->id,
            'client_id' => $client->id,
            'status' => Viewing::STATUS_REQUESTED,
            'client_note' => 'After work please.',
        ]);
    }

    public function test_a_viewing_cannot_be_requested_for_a_time_in_the_past(): void
    {
        $client = User::factory()->create();
        $property = $this->property();

        $this->actingAs($client)
            ->from(route('viewings.create', $property))
            ->post(route('viewings.store', $property), [
                'scheduled_at' => now()->subDay()->format('Y-m-d\TH:i'),
            ])
            ->assertSessionHasErrors('scheduled_at');

        $this->assertDatabaseCount('viewings', 0);
    }

    public function test_a_viewing_cannot_be_requested_for_a_house_that_is_not_available(): void
    {
        $client = User::factory()->create();
        $buyer = User::factory()->create();
        $property = $this->property([
            'status' => Property::RESERVED,
            'buyer_id' => $buyer->id,
        ]);

        $this->actingAs($client)
            ->post(route('viewings.store', $property), ['scheduled_at' => $this->slot()])
            ->assertSessionHasErrors('property');

        $this->assertDatabaseCount('viewings', 0);
    }

    public function test_a_clashing_slot_is_rejected(): void
    {
        $property = $this->property();
        $firstClient = User::factory()->create();
        $secondClient = User::factory()->create();
        $slot = $this->slot();

        $this->actingAs($firstClient)
            ->post(route('viewings.store', $property), ['scheduled_at' => $slot])
            ->assertSessionHasNoErrors();

        $this->actingAs($secondClient)
            ->from(route('viewings.create', $property))
            ->post(route('viewings.store', $property), ['scheduled_at' => $slot])
            ->assertSessionHasErrors('scheduled_at');

        $this->assertDatabaseCount('viewings', 1);
    }

    public function test_a_cancelled_viewing_frees_its_slot(): void
    {
        $property = $this->property();
        $client = User::factory()->create();
        $otherClient = User::factory()->create();

        $viewing = $this->viewing($client, $property);

        $this->actingAs($client)
            ->post(route('viewings.cancel', $viewing))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertSame(Viewing::STATUS_CANCELLED, $viewing->fresh()->status);

        $this->actingAs($otherClient)
            ->post(route('viewings.store', $property), [
                'scheduled_at' => $viewing->scheduled_at->format('Y-m-d\TH:i'),
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(2, Viewing::count());
    }

    public function test_a_client_cannot_cancel_another_clients_viewing(): void
    {
        $property = $this->property();
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $viewing = $this->viewing($owner, $property);

        $this->actingAs($stranger)
            ->post(route('viewings.cancel', $viewing))
            ->assertForbidden();

        $this->assertSame(Viewing::STATUS_REQUESTED, $viewing->fresh()->status);
    }

    public function test_management_can_approve_a_request(): void
    {
        $client = User::factory()->create();
        $manager = User::factory()->management()->create();
        $viewing = $this->viewing($client, $this->property());

        $this->actingAs($manager)
            ->post(route('management.viewings.approve', $viewing))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $viewing->refresh();
        $this->assertSame(Viewing::STATUS_APPROVED, $viewing->status);
        $this->assertSame($manager->id, $viewing->decided_by);
    }

    public function test_management_must_give_a_reason_when_rejecting(): void
    {
        $client = User::factory()->create();
        $manager = User::factory()->management()->create();
        $viewing = $this->viewing($client, $this->property());

        $this->actingAs($manager)
            ->post(route('management.viewings.reject', $viewing))
            ->assertSessionHasErrors('management_note');

        $this->assertSame(Viewing::STATUS_REQUESTED, $viewing->fresh()->status);

        $this->actingAs($manager)
            ->post(route('management.viewings.reject', $viewing), [
                'management_note' => 'The caretaker is away that day.',
            ])
            ->assertSessionHasNoErrors();

        $viewing->refresh();
        $this->assertSame(Viewing::STATUS_REJECTED, $viewing->status);
        $this->assertSame('The caretaker is away that day.', $viewing->management_note);
    }

    public function test_a_client_can_propose_a_new_time_and_management_can_accept_it(): void
    {
        $client = User::factory()->create();
        $manager = User::factory()->management()->create();
        $viewing = $this->viewing($client, $this->property(), Viewing::STATUS_APPROVED);

        $proposed = $this->slot(5, 14);

        $this->actingAs($client)
            ->post(route('viewings.reschedule', $viewing), [
                'proposed_at' => $proposed,
                'reschedule_note' => 'I am stuck at work that morning.',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $viewing->refresh();
        $this->assertSame(Viewing::STATUS_RESCHEDULE_REQUESTED, $viewing->status);
        $this->assertSame($proposed, $viewing->proposed_at->format('Y-m-d\TH:i'));

        $this->actingAs($manager)
            ->post(route('management.viewings.approve', $viewing), [
                'scheduled_at' => $viewing->proposed_at->format('Y-m-d\TH:i'),
            ])
            ->assertSessionHasNoErrors();

        $viewing->refresh();
        $this->assertSame(Viewing::STATUS_APPROVED, $viewing->status);
        $this->assertSame($proposed, $viewing->scheduled_at->format('Y-m-d\TH:i'));
        $this->assertNull($viewing->proposed_at);
    }

    public function test_only_management_can_mark_a_viewing_completed(): void
    {
        $client = User::factory()->create();
        $manager = User::factory()->management()->create();
        $viewing = $this->viewing($client, $this->property(), Viewing::STATUS_APPROVED);

        // The client is blocked by the role middleware and sent to their own dashboard.
        $this->actingAs($client)
            ->post(route('management.viewings.complete', $viewing))
            ->assertRedirect(route('client.dashboard'));

        $this->assertSame(Viewing::STATUS_APPROVED, $viewing->fresh()->status);

        $this->actingAs($manager)
            ->post(route('management.viewings.complete', $viewing))
            ->assertSessionHasNoErrors();

        $viewing->refresh();
        $this->assertSame(Viewing::STATUS_COMPLETED, $viewing->status);
        $this->assertNotNull($viewing->completed_at);
    }

    public function test_the_completion_check_is_true_only_after_a_viewing_is_completed(): void
    {
        $client = User::factory()->create();
        $manager = User::factory()->management()->create();
        $property = $this->property();
        $viewing = $this->viewing($client, $property, Viewing::STATUS_APPROVED);

        $this->assertFalse(Viewing::isCompletedFor($client, $property));

        $this->actingAs($manager)->post(route('management.viewings.complete', $viewing));

        $this->assertTrue(Viewing::isCompletedFor($client, $property));

        // Another client's completed viewing must not unlock this client's deposit.
        $otherClient = User::factory()->create();
        $this->assertFalse(Viewing::isCompletedFor($otherClient, $property));
    }

    public function test_a_client_can_customize_a_house_they_reserved(): void
    {
        $client = User::factory()->create();
        $property = $this->property([
            'status' => Property::RESERVED,
            'buyer_id' => $client->id,
        ]);

        $this->actingAs($client)
            ->get(route('customizations.index'))
            ->assertOk()
            ->assertSee($property->title);

        $this->actingAs($client)
            ->post(route('customizations.store', $property), [
                'furniture_arrangement' => 'Open plan with a corner sofa',
                'colour_scheme' => 'Warm neutrals',
                'furnishing_status' => Customization::SEMI_FURNISHED,
                'notes' => 'Please leave the balcony empty.',
            ])
            ->assertRedirect(route('customizations.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('customizations', [
            'property_id' => $property->id,
            'client_id' => $client->id,
            'furnishing_status' => Customization::SEMI_FURNISHED,
        ]);

        // Saving again edits the same set of preferences instead of adding a second one.
        $this->actingAs($client)
            ->post(route('customizations.store', $property), [
                'colour_scheme' => 'Sage green',
                'furnishing_status' => Customization::FURNISHED,
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(1, Customization::count());
        $this->assertSame('Sage green', Customization::first()->colour_scheme);
        $this->assertSame(Customization::FURNISHED, Customization::first()->furnishing_status);
    }

    public function test_a_client_cannot_customize_a_house_that_is_not_theirs(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $property = $this->property([
            'status' => Property::RESERVED,
            'buyer_id' => $owner->id,
        ]);

        $this->actingAs($stranger)
            ->post(route('customizations.store', $property), [
                'colour_scheme' => 'Sage green',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('customizations', 0);

        // An available house is not customizable either, even without a buyer.
        $available = $this->property(['title' => 'Available House']);

        $this->actingAs($stranger)
            ->post(route('customizations.store', $available), [])
            ->assertForbidden();
    }

    public function test_management_can_see_the_customizations_clients_saved(): void
    {
        $client = User::factory()->create(['name' => 'Amina Client']);
        $manager = User::factory()->management()->create();
        $property = $this->property([
            'status' => Property::RESERVED,
            'buyer_id' => $client->id,
        ]);

        Customization::create([
            'property_id' => $property->id,
            'client_id' => $client->id,
            'furniture_arrangement' => 'Open plan',
            'colour_scheme' => 'Warm neutrals',
            'furnishing_status' => Customization::SEMI_FURNISHED,
        ]);

        $this->actingAs($manager)
            ->get(route('management.customizations.index'))
            ->assertOk()
            ->assertSee('Amina Client')
            ->assertSee('Warm neutrals');
    }

    public function test_a_client_is_blocked_from_the_management_customizations_page(): void
    {
        $client = User::factory()->create();

        $this->actingAs($client)
            ->get(route('management.customizations.index'))
            ->assertRedirect(route('client.dashboard'));
    }

    public function test_the_property_page_links_clients_to_the_request_viewing_form(): void
    {
        $property = $this->property();
        $client = User::factory()->create();

        // Interface I4: the property detail page carries the entry point.
        $this->actingAs($client)
            ->get(route('properties.show', $property))
            ->assertOk()
            ->assertSee(route('viewings.create', $property));

        $this->actingAs($client)
            ->get(route('viewings.create', $property))
            ->assertOk()
            ->assertSee($property->title);
    }
}
