@extends('layouts.app')

 @section('title', 'Customization | Mireli')

@section('content')
    <h1 class="page-title">Customization</h1>
    <p class="page-lead">
        Tell us how your house should be finished. You can change these preferences at any time,
        and management sees the same form.
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
/**Done by Jewel */
    @if ($properties->isEmpty())
        <div class="placeholder-box mt-3">
            <span  class="badge">Nothing to customize yet</span>
            <h3>Reserve a house first</h3>
            <p class="text-muted">
                Customization opens once a deposit has been confirmed and the house is Reserved for you.
                Browse the catalogue to find a house you like.
            </p>
            <a class="btn" href="{{ route('properties.index') }}">Browse properties</a>
        </div>
    @else
        <div class="grid mt-3">
            @foreach ($properties as $property)
                @php($customization = $customizations->get($property->id))

                <div class="card">
                    <div class="card-body">
                        <p class="card-title">{{ $property->title }}</p>
                        <p class="text-muted">
                            {{ $property->location }}
                            <span class="badge badge-{{ $property->status }}">{{ $property->status }}</span>
                        </p>

                        <form method="POST" action="{{ route('customizations.store', $property) }}" novalidate>
                            @csrf

                            <div class="form-group">
                                <label for="furniture-{{ $property->id }}">Furniture arrangement</label>
                                <input id="furniture-{{ $property->id }}" name="furniture_arrangement" type="text" class="form-control"
                                       maxlength="255" value="{{ old('furniture_arrangement', $customization?->furniture_arrangement) }}"
                                       placeholder="e.g. Open plan with a corner sofa">
                            </div>

                            
                            <div class="form-group">
                                <label for="colour-{{ $property->id }}">Colour scheme</label>
                                <input id="colour-{{ $property->id }}" name="colour_scheme" type="text" class="form-control"
                                       maxlength="255" value="{{ old('colour_scheme', $customization?->colour_scheme) }}"
                                       placeholder="e.g. Warm neutrals">
                            </div>

                            <div class="form-group">
                                <label for="furnishing-{{ $property->id }}">Furnishing status</label>
                                <select id="furnishing-{{ $property->id }}" name="furnishing_status" class="form-control">
                                    <option value="">No preference</option>
                                    @foreach (\App\Models\Customization::furnishingOptions() as $value => $label)
                                        <option value="{{ $value }}" @selected(old('furnishing_status', $customization?->furnishing_status) === $value)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="notes-{{ $property->id }}">Anything else?</label>
                                <textarea id="notes-{{ $property->id }}" name="notes" class="form-control" rows="3"
                                          maxlength="1000">{{ old('notes', $customization?->notes) }}</textarea>
                            </div>

                            <button type="submit"  class="btn btn-block">
                                {{ $customization ? 'Update preferences' : 'Save preferences' }}
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
