
@extends('layouts.app')

@section('title', 'Edit Property | Mireli')

@section('content')
<div class="properties-page">

    <div class="page-title">
        <h1>Edit Property</h1>
        <p class="page-lead">Update property information and availability.</p>
    </div>

    @if($errors->any())
        <div class="placeholder-box">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">

            <form method="POST"
                action="{{ route('management.properties.update', $property->id) }}">

                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Property Title</label>
                    <input type="text" name="title" class="form-control"
                        value="{{ old('title', $property->title) }}" required>
                </div>

                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" class="form-control"
                        value="{{ old('location', $property->location) }}" required>
                </div>

                <div class="form-group">
                    <label>Bedrooms</label>
                    <input type="number" name="bedrooms" class="form-control"
                        value="{{ old('bedrooms', $property->bedrooms) }}" min="0" required>
                </div>

                <div class="form-group">
                    <label>Size</label>
                    <input type="text" name="size" class="form-control"
                        value="{{ old('size', $property->size) }}" required>
                </div>

                <div class="form-group">
                    <label>Price (KES)</label>
                    <input type="number" step="0.01" name="price" class="form-control"
                        value="{{ old('price', $property->price) }}" min="0" required>
                </div>

                <div class="form-group">
                    <label>Deposit Amount (KES)</label>
                    <input type="number" step="0.01" name="deposit_amount" class="form-control"
                        value="{{ old('deposit_amount', $property->deposit_amount) }}" min="0" required>
                </div>

                <div class="form-group">
                    <label>Property Status</label>
                    <select name="status" class="form-control" required>
                        <option value="available" {{ old('status', $property->status) == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="reserved" {{ old('status', $property->status) == 'reserved' ? 'selected' : '' }}>Reserved</option>
                        <option value="sold" {{ old('status', $property->status) == 'sold' ? 'selected' : '' }}>Sold</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Amenities</label>
                    <input type="text" name="amenities" class="form-control"
                        value="{{ old('amenities', $property->amenities) }}">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description', $property->description) }}</textarea>
                </div>

                <p class="text-muted">
                    Property photographs are assigned automatically by Mireli.
                    The existing photograph will remain unchanged.
                </p>

                <button type="submit" class="btn">Update Property</button>

                <a href="{{ route('management.properties.index') }}" class="btn">
                    Cancel
                </a>

            </form>

        </div>
    </div>

</div>
@endsection
