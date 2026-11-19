@extends('layouts.app')

@section('title', 'Client customizations | Mireli')

@section('content')
    <h1 class="page-title">Client customizations</h1>
    <p class="page-lead">
        The preferences clients have saved for the houses they reserved or bought. This page is read-only.
    </p>

    @if ($customizations->isEmpty())
        <div class="placeholder-box mt-3">
            <span class="badge">Nothing here yet</span>
            <h3>No client has saved preferences</h3>
            <p class="text-muted">A client can customize a house once it is Reserved or Sold for them.</p>
        </div>
    @else
        <div class="table-wrap mt-3">
            <table class="table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Property</th>
                        <th>Furniture arrangement</th>
                        <th>Colour scheme</th>
                        <th>Furnishing</th>
                        <th>Notes</th>
                        <th>Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customizations as $customization)
                        <tr>
                            <td>
                                {{ $customization->client->name }}
                                <div class="text-muted">{{ $customization->client->phone ?? $customization->client->email }}</div>
                            </td>
                            <td>
                                {{ $customization->property->title }}
                                <div class="text-muted">
                                    {{ $customization->property->location }}
                                    <span class="badge badge-{{ $customization->property->status }}">{{ $customization->property->status }}</span>
                                </div>
                            </td>
                            <td>{{ $customization->furniture_arrangement ?: '—' }}</td>
                            <td>{{ $customization->colour_scheme ?: '—' }}</td>
                            <td>{{ $customization->furnishingLabel() }}</td>
                            <td>{{ $customization->notes ?: '—' }}</td>
                            <td>{{ $customization->updated_at->format('d M Y, H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
