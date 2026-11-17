
@extends('layouts.app')

@section('title', 'Mireli Real Estate')

@section('hero')
    <section class="hero">
        <div class="container">
            <h1>Find a home worth keeping</h1>

            <p>
                Discover available homes, explore their features and find
                the perfect place to call home.
            </p>

            <div class="hero-actions">
                @guest
                    <a href="{{ route('register') }}" class="btn btn-light">
                        Create an account
                    </a>

                    <a href="{{ route('login') }}" class="btn btn-outline-light">
                        Log in
                    </a>
                @endguest

                @auth
                    <a href="{{ route('properties.index') }}" class="btn btn-light">
                        Browse Properties
                    </a>
                @endauth
            </div>
        </div>
    </section>
@endsection

@section('content')

    {{-- HOW IT WORKS --}}
    <h2 class="section-title">How it works</h2>

    <div class="grid grid-4">

        <div class="card">
            <div class="card-body">
                <div class="step-number">1</div>
                <h3>Browse</h3>
                <p class="text-muted">
                    Explore available houses by location and price.
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="step-number">2</div>
                <h3>View</h3>
                <p class="text-muted">
                    Request a viewing and explore your preferred property.
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="step-number">3</div>
                <h3>Apply</h3>
                <p class="text-muted">
                    Submit your application for your preferred property.
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="step-number">4</div>
                <h3>Move In</h3>
                <p class="text-muted">
                    Complete the process and prepare to move into your home.
                </p>
            </div>
        </div>

    </div>


    {{-- DYNAMIC PROPERTY LISTINGS --}}
    <div class="properties-heading" style="margin-top: 3rem;">
        <div>
            <h2 class="section-title">Recently Listed Properties</h2>
            <p class="text-muted">
                Explore some of the latest available homes on Mireli.
            </p>
        </div>

        <a href="{{ route('properties.index') }}" class="btn">
            View All Properties
        </a>
    </div>

    @if(isset($featuredProperties) && $featuredProperties->count() > 0)

        <div class="property-card-grid">

            @foreach($featuredProperties as $property)

                <article class="property-management-card">

                    {{-- PROPERTY PHOTO --}}
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
                                    loading="lazy"
                                >

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

                    {{-- PROPERTY INFORMATION --}}
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

    @else

        <div class="placeholder-box">
            <h3>No Properties Available</h3>
            <p>New property listings will appear here once they are added.</p>
        </div>

    @endif

@endsection
