<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Viewing;
use App\Services\ViewingService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * PAIR 3: the management viewing queue.
 *
 * Management approves, rejects with a note, gives a different slot, handles
 * reschedule requests and marks a visit as completed. Only management may mark
 * a viewing completed, because that is what unlocks the deposit (interface I5).
 */
class ViewingController extends Controller
{
    public function __construct(private readonly ViewingService $viewings) {}

    public function index(Request $request): View
    {
        $status = $request->query('status');
        $status = in_array($status, Viewing::STATUSES, true) ? $status : null;

        $viewings = Viewing::query()
            ->with(['property', 'client'])
            ->when($status, fn ($query) => $query->where('status', $status))
            // Requests waiting for a decision come first, then approved visits, then history.
            ->orderByRaw(
                'case status when ? then 0 when ? then 0 when ? then 1 else 2 end',
                [Viewing::STATUS_REQUESTED, Viewing::STATUS_RESCHEDULE_REQUESTED, Viewing::STATUS_APPROVED],
            )
            ->orderBy('scheduled_at')
            ->get();

        return view('management.viewings.index', [
            'viewings' => $viewings,
            'status' => $status,
            'statuses' => Viewing::STATUSES,
            'awaitingCount' => Viewing::query()->awaitingManagement()->count(),
        ]);
    }

    public function approve(Request $request, Viewing $viewing): RedirectResponse
    {
        $data = $request->validate([
            'scheduled_at' => ['nullable', 'date', 'after:now'],
            'management_note' => ['nullable', 'string', 'max:500'],
        ]);

        $this->viewings->approve(
            $viewing,
            $request->user(),
            isset($data['scheduled_at']) ? Carbon::parse($data['scheduled_at']) : null,
            $data['management_note'] ?? null,
        );

        return back()->with('success', 'Viewing approved for '.$viewing->scheduled_at->format('d M Y, H:i').'.');
    }

    public function reject(Request $request, Viewing $viewing): RedirectResponse
    {
        $data = $request->validate([
            'management_note' => ['required', 'string', 'max:500'],
        ]);

        $this->viewings->reject($viewing, $request->user(), $data['management_note']);

        return back()->with('success', 'Viewing rejected. The client can see the reason you gave.');
    }

    public function reschedule(Request $request, Viewing $viewing): RedirectResponse
    {
        $data = $request->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
            'management_note' => ['nullable', 'string', 'max:500'],
        ]);

        $this->viewings->reschedule(
            $viewing,
            $request->user(),
            Carbon::parse($data['scheduled_at']),
            $data['management_note'] ?? null,
        );

        return back()->with('success', 'Viewing moved to '.$viewing->scheduled_at->format('d M Y, H:i').'.');
    }

    public function complete(Request $request, Viewing $viewing): RedirectResponse
    {
        $this->viewings->complete($viewing, $request->user());

        return back()->with('success', 'Viewing marked as completed. The client can now pay the deposit.');
    }
}
