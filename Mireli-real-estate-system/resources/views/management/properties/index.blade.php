
@extends('layouts.app')

@section('title', 'Manage Properties | Mireli')

@section('content')

<div class="properties-page">

    <div class="page-title">
        <h1>Manage Properties</h1>
        <p class="page-lead">
            View, edit, update and manage all property listings.
        </p>

        <a href="{{ route('management.properties.create') }}" class="btn">
            + Add New Property
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="placeholder-box">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="properties-heading">
        <div>
            <h2>All Properties</h2>
            <p class="text-muted">
                {{ $properties->total() }} properties in the system
            </p>
        </div>
    </div>

    @if($properties->count() > 0)

        <div class="property-card-grid">

            @foreach($properties as $property)

                <article class="property-management-card">

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
                            >

                        @else

                            <div class="property-no-image">
                                No Image Available
                            </div>

                        @endif

                        @if($property->status === 'available')
                            <span class="badge badge-available">Available</span>
                        @elseif($property->status === 'reserved')
                            <span class="badge badge-reserved">Reserved</span>
                        @elseif($property->status === 'sold')
                            <span class="badge badge-sold">Sold</span>
                        @else
                            <span class="badge">Unknown Status</span>
                        @endif

                    </div>

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

                        <div class="management-actions">

                            <a
                                href="{{ route('management.properties.edit', $property->id) }}"
                                class="btn"
                            >
                                Edit Property
                            </a>

                            <form
                                action="{{ route('management.properties.destroy', $property->id) }}"
                                method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this property?')"
                            >
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger">
                                    Delete
                                </button>
                            </form>

                        </div>

                    </div>

                </article>

            @endforeach

        </div>

        @if($properties->hasPages())
            <div class="mt-3">
                {{ $properties->links() }}
            </div>
        @endif

    @else

        <div class="placeholder-box">
            <h3>No Properties Yet</h3>
            <p>Add your first property to get started.</p>

            <a href="{{ route('management.properties.create') }}" class="btn">
                Add Property
            </a>
        </div>

    @endif

</div>

@endsection
