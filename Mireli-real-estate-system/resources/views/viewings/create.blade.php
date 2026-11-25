@extends('layouts.app')

@section('title', 'Request a viewing | Mireli')

@section('content')
    <a href="{{ route('properties.show', $property) }}" class="text-muted">&larr; Back to the property</a>

     <h1 class="page-title">Request a viewing</h1>
    <p class="page-lead">
        Choose a date and time. Management approves every request before it is confirmed,
        and only available houses can be viewed.
    </p>

    @if ($errors->any())
        <div class="alert alert-error" role="alert">
            <strong>Please check the form:</strong>
            <ul>
                @foreach ($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid">
        <div class="card">
            <div class="card-body">
                <p class="card-title">{{ $property->title }}</p>
                <p class="text-muted">
                    {{ $property->location }}
                    &middot; {{ $property->bedrooms }} bedroom{{ $property->bedrooms === 1 ? '' : 's' }}
                    @if ($property->size)
                        &middot; {{ $property->size }}
                    @endif
                </p>
                <p class="text-muted">Price: KES {{ number_format((float) $property->price, 2) }}</p>
                <p class="text-muted">Deposit: KES {{ number_format((float) $property->deposit_amount, 2) }}</p>
                <span  class="badge badge-{{ $property->status }}">{{ $property->status }}</span>
            </div>
        </div>
/**Done by Jewel */
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('viewings.store', $property) }}" novalidate>
                    @csrf

                    <div class="form-group">
                        <label for="scheduled_at">Date and time</label>
                        <input id="scheduled_at" name="scheduled_at" type="datetime-local" class="form-control"
                               value="{{ old('scheduled_at') }}" min="{{ now()->addMinute()->format('Y-m-d\TH:i') }}" required autofocus>
                        @error('scheduled_at') <p class="field-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label for="client_note">Message to management (optional)</label>
                        <textarea id="client_note" name="client_note" class="form-control" rows="3" maxlength="500">{{ old('client_note') }}</textarea>
                        @error('client_note') <p class="field-error">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="btn btn-block">Send request</button>
                </form>

                <p class="form-footer">Management will approve or reject the request from the viewing queue.</p>
            </div>
        </div>
    </div>
@endsection
