<?php

namespace App\Services;

use App\Models\Property;
use App\Models\User;
use App\Models\Viewing;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * PAIR 3: all viewing status changes go through this service, so the rules live
 * in one place:
 *
 *  - only an available property can be viewed (interface I3),
 *  - a viewing can never be requested for a time in the past,
 *  - no two live viewings of the same property may share a slot,
 *  - only management approves, rejects, moves or completes a viewing.
 *
 * Rule breaks are thrown as ValidationException so the normal Laravel form
 * handling shows the message next to the form instead of a 500 page.
 */
class ViewingService
{
    /** Client requests a viewing of an available house. */
    public function request(User $client, Property $property, CarbonInterface $scheduledAt, ?string $note = null): Viewing
    {
        $this->ensurePropertyIsAvailable($property);
        $this->ensureNotInThePast($scheduledAt, 'scheduled_at');

        return DB::transaction(function () use ($client, $property, $scheduledAt, $note): Viewing {
            $this->ensureSlotIsFree($property->getKey(), $scheduledAt);

            return Viewing::create([
                'property_id' => $property->getKey(),
                'client_id' => $client->getKey(),
                'scheduled_at' => $scheduledAt,
                'status' => Viewing::STATUS_REQUESTED,
                'client_note' => $note,
            ]);
        });
    }

    /**
     * Management approves a request. Passing a time accepts that time instead
     * (used to accept a reschedule proposal or to give a different slot).
     */
    public function approve(Viewing $viewing, User $manager, ?CarbonInterface $scheduledAt = null, ?string $note = null): Viewing
    {
        $this->ensureStatus(
            $viewing,
            [Viewing::STATUS_REQUESTED, Viewing::STATUS_RESCHEDULE_REQUESTED],
            'Only a  request that is waiting for management can be approved.',
        );

        $slot = $scheduledAt ?? $viewing->scheduled_at;

        $this->ensureNotInThePast($slot, 'scheduled_at');
        $this->ensureSlotIsFree($viewing->property_id, $slot, $viewing->getKey());

        $viewing->update([
            'scheduled_at' => $slot,
            'status' => Viewing::STATUS_APPROVED,
            'proposed_at' => null,
            'management_note' => $note,
            'decided_by' => $manager->getKey(),
        ]);

        return $viewing;
    }

    /** Management rejects a request with a reason the client can read. */
    public function reject(Viewing $viewing,  User $manager, string $reason): Viewing
    {
        $this->ensureStatus(
            $viewing,
            [Viewing::STATUS_REQUESTED, Viewing::STATUS_RESCHEDULE_REQUESTED],
            'Only a request that is waiting for management can be rejected.',
        );

        $viewing->update([
            'status' => Viewing::STATUS_REJECTED,
            'proposed_at' => null,
            'management_note' => $reason,
            'decided_by' => $manager->getKey(),
        ]);

        return $viewing;
    }

    /** The client proposes another time for an approved viewing. */
    public function  requestReschedule(Viewing $viewing, User $client, CarbonInterface $proposedAt, ?string $reason = null): Viewing
    {
        if ($viewing->client_id !== $client->getKey()) {
            throw ValidationException::withMessages([
                'proposed_at' => 'You can only reschedule your own viewing.',
            ]);
        }

        $this->ensureStatus($viewing, [Viewing::STATUS_APPROVED], 'Only an approved viewing can be rescheduled.');

        $this->ensureNotInThePast($proposedAt, 'proposed_at');
        $this->ensureSlotIsFree( $viewing->property_id, $proposedAt, $viewing->getKey());

        $viewing->update([
            'status' => Viewing::STATUS_RESCHEDULE_REQUESTED,
            'proposed_at' => $proposedAt,
            'reschedule_note' => $reason,
        ]);

        return $viewing;
    }

    /** Management accepts the proposed time or assigns a different one. */
    public function reschedule(Viewing $viewing, User $manager, CarbonInterface $scheduledAt, ?string $note = null): Viewing
    {
        $this->ensureStatus(
            $viewing,
            [Viewing::STATUS_APPROVED, Viewing::STATUS_RESCHEDULE_REQUESTED],
            'Only an approved viewing can be moved to another time.',
        );

        $this->ensureNotInThePast($scheduledAt, 'scheduled_at');
        $this->ensureSlotIsFree($viewing->property_id, $scheduledAt, $viewing->getKey());

        $viewing->update([
            'scheduled_at' => $scheduledAt,
            'status' => Viewing::STATUS_APPROVED,
            'proposed_at' => null,
            'management_note' => $note,
            'decided_by' => $manager->getKey(),
        ]);

        return $viewing;
    }

    /** A client (or management) cancels a live viewing, which frees its slot. */
    public function cancel(Viewing $viewing, User $actor, ?string $reason = null): Viewing
    {
        $this->ensureStatus(
            $viewing,
            Viewing::LIVE_STATUSES,
            'Only a requested, approved or reschedule-requested viewing can be cancelled.',
        );

        $viewing->update([
            'status' => Viewing::STATUS_CANCELLED,
            'proposed_at' => null,
            'management_note' => $reason ?? $viewing->management_note,
            'decided_by' => $actor->getKey(),
        ]);

        return $viewing;
    }

    /** Management marks a visit as completed, which unlocks the deposit (I5). */
    public function complete(Viewing $viewing, User $manager): Viewing
    {
        $this->ensureStatus($viewing, [Viewing::STATUS_APPROVED], 'Only an approved viewing can be marked as completed.');

        $viewing->update([
            'status' => Viewing::STATUS_COMPLETED,
            'completed_at' => now(),
            'proposed_at' => null,
            'decided_by' => $manager->getKey(),
        ]);

        return $viewing;
    }

    private function ensurePropertyIsAvailable(Property $property): void
    {
        if (! $property->isAvailable()) {
            throw ValidationException::withMessages([
                'property' => 'Only available properties can be viewed.',
            ]);
        }
    }

    private function ensureNotInThePast(CarbonInterface $slot, string $field): void
    {
        if ($slot->isPast()) {
            throw ValidationException::withMessages([
                $field => 'Choose a date and time in the future.',
            ]);
        }
    }

    private function ensureSlotIsFree(int $propertyId, CarbonInterface $slot, ?int $exceptViewingId = null): void
    {
        if (Viewing::slotTaken($propertyId, $slot, $exceptViewingId)) {
            throw ValidationException::withMessages([
                'scheduled_at' => 'Another viewing already holds that slot for this property. Choose a different time.',
            ]);
        }
    }

    /** @param  array<int, string>  $allowed */
    private function ensureStatus(Viewing $viewing, array $allowed, string $message): void
    {
        if (! in_array($viewing->status, $allowed, true)) {
            throw ValidationException::withMessages(['status' => $message]);
        }
    }
}
