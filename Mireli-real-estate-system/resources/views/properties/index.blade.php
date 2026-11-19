
@extends('layouts.app')

@section('title', 'Available Properties | Mireli')

@section('content')

<div class="properties-page">

    <div class="page-title">
        <h1>Find Your Next Home</h1>
        <p class="page-lead">
            Explore our available properties and find a place that feels like home.
        </p>
    </div>

    {{-- PROPERTY SEARCH --}}
    <form action="{{ route('properties.index') }}" method="GET" class="property-search-form">

        <div class="form-group">
            <label for="location">Location</label>
            <input
                type="text"
                name="location"
                id="location"
                class="form-control"
                placeholder="Enter location"
                value="{{ request('location') }}"
            >
        </div>

        <div class="form-group">
            <label for="min_price">Minimum Price</label>
            <input
                type="number"
                name="min_price"
                id="min_price"
                class="form-control"
                placeholder="Minimum price"
                value="{{ request('min_price') }}"
            >
        </div>

        <div class="form-group">
            <label for="max_price">Maximum Price</label>
            <input
                type="number"
                name="max_price"
                id="max_price"
                class="form-control"
                placeholder="Maximum price"
                value="{{ request('max_price') }}"
            >
        </div>

        <button type="submit" class="btn">
            Search Properties
        </button>

    </form>

    {{-- AVAILABLE PROPERTIES --}}
    <div class="properties-heading">
        <div>
            <h2>Available Properties</h2>
            <p class="text-muted">
                {{ $properties->total() }} properties available
            </p>
        </div>
    </div>

    @if($properties->count() > 0)

        <div class="property-card-grid">

            @foreach($properties as $property)

                <article class="property-management-card">

                    {{-- PROPERTY IMAGE --}}
                    <a
                        href="{{ route('properties.show', $property->id) }}"
                        style="display: block; text-decoration: none;"
                    >

                        <div class="property-management-image">

                            @if($property->images->first())

                                @php
                                    $imagePath = $property->images->first()->image_url;

                                    $imageUrl = str_starts_with($imagePath, 'images/')
                                        ? asset($imagePath)
                                        : asset('storage/' . $imagePath);
                                @endphp

                                <img
                                    src="{{ $imageUrl }}"
                                    alt="{{ $property->title }}"
                                    loading="eager"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                >

                                <div class="property-no-image" style="display: none;">
                                    Image could not be loaded
                                </div>

                            @else

                                <div class="property-no-image">
                                    No Image Available
                                </div>

                            @endif

                            <span class="badge badge-available">
                                Available
                            </span>

                        </div>

                    </a>

                    {{-- PROPERTY DETAILS --}}
                    <div class="property-management-body">

                        <h3>{{ $property->title }}</h3>

                        <p class="text-muted">
                            {{ $property->location }}
                        </p>

                        <p class="property-management-price">
                            KES {{ number_format($property->price, 2) }}
                        </p>

                        <div class="property-management-details">
                            <span>{{ $property->bedrooms }} Bedrooms</span>
                            <span>{{ $property->size }}</span>
                        </div>

                        <p>
                            {{ \Illuminate\Support\Str::limit($property->description, 100) }}
                        </p>

                        <a
                            href="{{ route('properties.show', $property->id) }}"
                            class="btn"
                        >
                            View Property Details
                        </a>

                    </div>

                </article>

            @endforeach

        </div>

        {{-- PAGINATION --}}
        @if($properties->hasPages())
            <div class="mt-3">
                {{ $properties->withQueryString()->links() }}
            </div>
        @endif

    @else

        <div class="placeholder-box">
            <h3>No Properties Available</h3>

            <p>
                There are currently no properties matching your search.
            </p>

            <a href="{{ route('properties.index') }}" class="btn">
                View All Properties
            </a>
        </div>

    @endif

</div>

@endsection
