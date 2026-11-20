<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Viewing;
use App\Services\ViewingService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * PAIR 3: the client side of viewings. Management's side lives in
 * App\Http\Controllers\Management\ViewingController.
 */
class ViewingController extends Controller
{
    public function __construct(private readonly ViewingService $viewings) {}

    /** My viewings: everything this client has requested, newest slot first. */
    public function index(Request $request): View
    {
        $viewings = Viewing::query()
            ->with('property')
            ->where('client_id', $request->user()->getKey())
            ->orderByDesc('scheduled_at')
            ->get();

        return view('viewings.index', ['viewings' => $viewings]);
    }

    /** Request form, opened from the property detail page (interface I4). */
    public function create(Property $property): View|RedirectResponse
    {
        if (! $property->isAvailable()) {
            return redirect()->route('properties.index')
                ->with('error', 'That property is not available for viewing.');
        }

        return view('viewings.create', ['property' => $property]);
    }

    public function store(Request $request, Property $property): RedirectResponse
    {
        $data = $request->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
            'client_note' => ['nullable', 'string', 'max:500'],
        ]);

        $viewing = $this->viewings->request(
            $request->user(),
            $property,
            Carbon::parse($data['scheduled_at']),
            $data['client_note'] ?? null,
        );

        return redirect()->route('viewings.index')->with(
            'success',
            'Viewing requested for '.$viewing->scheduled_at->format('d M Y, H:i').'. Management will confirm it.',
        );
    }

    /** Cancel one of my live viewings. Cancelling frees the slot. */
    public function cancel(Request $request, Viewing $viewing): RedirectResponse
    {
        abort_if($viewing->client_id !== $request->user()->getKey(), 403);

        $this->viewings->cancel($viewing, $request->user());

        return back()->with('success', 'Viewing cancelled. The slot is free again.');
    }

    /** Propose a new time for an approved viewing. */
    public function reschedule(Request $request, Viewing $viewing): RedirectResponse
    {
        abort_if($viewing->client_id !== $request->user()->getKey(), 403);

        $data = $request->validate([
            'proposed_at' => ['required', 'date', 'after:now'],
            'reschedule_note' => ['nullable', 'string', 'max:500'],
        ]);

        $this->viewings->requestReschedule(
            $viewing,
            $request->user(),
            Carbon::parse($data['proposed_at']),
            $data['reschedule_note'] ?? null,
        );

        return back()->with('success', 'Your new time was sent to management for approval.');
    }
}
