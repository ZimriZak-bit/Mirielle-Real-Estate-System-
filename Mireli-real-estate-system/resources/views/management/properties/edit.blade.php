@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-2xl">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Edit Property: {{ $property->title }}</h2>

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('management.properties.update', $property->id) }}" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md">
        @csrf
        @method('PUT')
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
            <input type="text" name="title" value="{{ old('title', $property->title) }}" class="w-full rounded-md border-gray-300 shadow-sm" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
            <input type="text" name="location" value="{{ old('location', $property->location) }}" class="w-full rounded-md border-gray-300 shadow-sm" required>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bedrooms</label>
                <input type="number" name="bedrooms" value="{{ old('bedrooms', $property->bedrooms) }}" class="w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Size (sqft)</label>
                <input type="text" name="size" value="{{ old('size', $property->size) }}" class="w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Price (KES)</label>
                <input type="number" step="0.01" name="price" value="{{ old('price', $property->price) }}" class="w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deposit Amount (KES)</label>
                <input type="number" step="0.01" name="deposit_amount" value="{{ old('deposit_amount', $property->deposit_amount) }}" class="w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
        </div>

        <!-- 6. Status Dropdown -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Property Status</label>
            <select name="status" class="w-full rounded-md border-gray-300 shadow-sm">
                <option value="available" {{ $property->status == 'available' ? 'selected' : '' }}>Available</option>
                <option value="reserved" {{ $property->status == 'reserved' ? 'selected' : '' }}>Reserved</option>
                <option value="sold" {{ $property->status == 'sold' ? 'selected' : '' }}>Sold</option>
            </select>
        </div>

        <!-- 3. Image Upload -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Change Image (Optional)</label>
            <input type="file" name="image" class="w-full rounded-md border-gray-300 shadow-sm">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Amenities (optional)</label>
            <input type="text" name="amenities" value="{{ old('amenities', $property->amenities) }}" class="w-full rounded-md border-gray-300 shadow-sm">
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
            <textarea name="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm">{{ old('description', $property->description) }}</textarea>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Update Property</button>
    </form>
</div>
@endsection