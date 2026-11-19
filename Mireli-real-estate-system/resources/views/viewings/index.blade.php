@extends('layouts.app')

@section('title', 'My viewings | Mireli')

@section('content')
    <h1 class="page-title">My viewings</h1>
    <p class="page-lead">
        Track every request, cancel a visit, or propose another time once management has approved one.
    </p>

    @if ($errors->any())
        <div class="alert alert-error" role="alert">
            <strong>That did not work:</strong>
            <ul>
                @foreach ($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($viewings->isEmpty())
        <div class="placeholder-box mt-3">
            <span class="badge">No viewings yet</span>
            <h3>You have not requested a viewing</h3>
            <p class="text-muted">Open a property and choose &ldquo;Request viewing&rdquo; to pick a date and time.</p>
            <a class="btn" href="{{ route('properties.index') }}">Browse properties</a>
        </div>
    @else
        <div class="table-wrap mt-3">
            <table class="table">
                <thead>
                    <tr>
                        <th>Property</th>
                        <th>Slot</th>
                        <th>Status</th>
                        <th>Messages</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($viewings as $viewing)
                        <tr>
                            <td>
                                @if ($viewing->property->isAvailable())
                                    <a href="{{ route('properties.show', $viewing->property) }}">{{ $viewing->property->title }}</a>
                                @else
                                    {{ $viewing->property->title }}
                                @endif
                                <div class="text-muted">{{ $viewing->property->location }}</div>
                            </td>
                            <td>
                                {{ $viewing->scheduled_at->format('d M Y, H:i') }}
                                @if ($viewing->proposed_at)
                                    <div class="text-muted">Proposed: {{ $viewing->proposed_at->format('d M Y, H:i') }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $viewing->status }}">{{ $viewing->statusLabel() }}</span>
                            </td>
                            <td>
                                @if ($viewing->client_note)
                                    <div>You: {{ $viewing->client_note }}</div>
                                @endif
                                @if ($viewing->reschedule_note)
                                    <div>New time: {{ $viewing->reschedule_note }}</div>
                                @endif
                                @if ($viewing->management_note)
                                    <div class="text-muted">Management: {{ $viewing->management_note }}</div>
                                @endif
                                @if (! $viewing->client_note && ! $viewing->reschedule_note && ! $viewing->management_note)
                                    <span class="text-muted">&mdash;</span>
                                @endif
                            </td>
                            <td>
                                @if ($viewing->canBeCancelled())
                                    <form method="POST" action="{{ route('viewings.cancel', $viewing) }}"
                                          onsubmit="return confirm('Cancel this viewing? The slot becomes free for other clients.');">
                                        @csrf
                                        <button type="submit" class="btn btn-outline">Cancel</button>
                                    </form>
                                @endif

                                @if ($viewing->isApproved())
                                    <form method="POST" action="{{ route('viewings.reschedule', $viewing) }}" class="mt-2">
                                        @csrf
                                        <div class="form-group">
                                            <label for="proposed-{{ $viewing->id }}">Propose another time</label>
                                            <input id="proposed-{{ $viewing->id }}" name="proposed_at" type="datetime-local" class="form-control"
                                                   value="{{ old('proposed_at') }}" min="{{ now()->addMinute()->format('Y-m-d\TH:i') }}" required>
                                        </div>
                                        <input name="reschedule_note" type="text" class="form-control" maxlength="500"
                                               value="{{ old('reschedule_note') }}" placeholder="Reason (optional)">
                                        <button type="submit" class="btn btn-outline mt-2">Send new time</button>
                                    </form>
                                @elseif ($viewing->isRescheduleRequested())
                                    <p class="text-muted">Waiting for management to confirm the new time.</p>
                                @elseif (! $viewing->canBeCancelled())
                                    <span class="text-muted">No action needed</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
