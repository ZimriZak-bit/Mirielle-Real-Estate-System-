@extends('layouts.app')

@section('title', 'Manage viewings | Mireli')

@section('content')
    <h1 class="page-title">Viewing requests</h1>
    <p class="page-lead">
        {{ $awaitingCount }} request{{ $awaitingCount === 1 ? '' : 's' }} waiting for a decision.
        Approve, reject with a reason, move a slot, or mark a visit completed after it happens.
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

/**Done by Jewel */

    <div class="mt-2">
        <a  class="btn {{ $status === null ? '' : 'btn-outline' }}" href="{{ route('management.viewings.index') }}">All</a>
        @foreach ($statuses as $option)
            <a class="btn {{ $status === $option ? '' : 'btn-outline' }}"
               href="{{ route('management.viewings.index', ['status' => $option]) }}">{{ ucfirst(str_replace('_', ' ', $option)) }}</a>
        @endforeach
    </div>

    @if ($viewings->isEmpty())
        <div class="placeholder-box mt-3">
            <span class="badge">Nothing here</span>
            <h3>No viewings with this status</h3>
            <p class="text-muted">New client requests appear here as soon as they are made.</p>
        </div>
    @else
        <div class="table-wrap mt-3">
            <table class="table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Property</th>
                        <th>Slot</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($viewings as $viewing)
                        <tr>
                            <td>
                                {{ $viewing->client->name }}
                                <div class="text-muted">{{ $viewing->client->phone ?? $viewing->client->email }}</div>
                            </td>
                            <td>
                                {{ $viewing->property->title }}
                                <div class="text-muted">
                                    {{ $viewing->property->location }}
                                    <span class="badge badge-{{ $viewing->property->status }}">{{ $viewing->property->status }}</span>
                                </div>
                            </td>
                            <td>
                                {{ $viewing->scheduled_at->format('d M Y, H:i') }}
                                @if ($viewing->proposed_at)
                                    <div class="text-muted">Wants: {{ $viewing->proposed_at->format('d M Y, H:i') }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $viewing->status }}">{{ $viewing->statusLabel() }}</span>
                            </td>
                            <td>
                                @if ($viewing->client_note)
                                    <div>Client: {{ $viewing->client_note }}</div>
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
                                @include('management.viewings._actions', ['viewing' => $viewing])
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
