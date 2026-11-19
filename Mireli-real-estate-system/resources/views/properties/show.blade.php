
@extends('layouts.app')

@section('title', $property->title . ' | Mireli Real Estate')

@section('content')

<div class="property-details-page">

    <div class="property-back">
        <a href="{{ route('properties.index') }}" class="back-link">
            &larr; Back to Properties
        </a>
    </div>

    <div class="property-details-header">
        <div>
            <p class="property-location">{{ $property->location }}</p>
            <h1>{{ $property->title }}</h1>
        </div>

        <div class="property-details-price">
            <span>Asking Price</span>
            <h2>KES {{ number_format($property->price, 2) }}</h2>
        </div>
    </div>

    {{-- Property Photos --}}
    <div class="property-gallery">

        @forelse($property->images as $image)

            @php
                $imagePath = $image->image_url;

                $imageUrl = str_starts_with($imagePath, 'images/')
                    ? asset($imagePath)
                    : asset('storage/' . $imagePath);
            @endphp

            <div class="property-gallery-image">
                <img
                    src="{{ $imageUrl }}"
                    alt="{{ $property->title }}"
                >
            </div>

        @empty

            <div class="property-gallery-empty">
                <p>No photos are available for this property yet.</p>
            </div>

        @endforelse

    </div>

    {{-- Property Information --}}
    <div class="property-details-content">

        <div class="property-main-information">

            <h2>Property Overview</h2>

            <p>{{ $property->description }}</p>

            <div class="property-information-grid">

                <div class="property-information-item">
                    <span>Bedrooms</span>
                    <strong>{{ $property->bedrooms }}</strong>
                </div>

                <div class="property-information-item">
                    <span>Property Size</span>
                    <strong>{{ $property->size }}</strong>
                </div>

                <div class="property-information-item">
                    <span>Location</span>
                    <strong>{{ $property->location }}</strong>
                </div>

                <div class="property-information-item">
                    <span>Deposit</span>
                    <strong>
                        KES {{ number_format($property->deposit_amount, 2) }}
                    </strong>
                </div>

            </div>

            <div class="property-amenities">
                <h2>Amenities</h2>
                <p>{{ $property->amenities ?: 'No amenities listed.' }}</p>
            </div>

        </div>

        <aside class="property-contact-card">

            <h3>Interested in this Property?</h3>

            <p>
                Contact our management team to arrange a viewing
                or enquire about this property.
            </p>

            <a href="{{ route('properties.index') }}" class="btn">
                Explore More Properties
            </a>

        </aside>

    </div>

</div>

@endsection
