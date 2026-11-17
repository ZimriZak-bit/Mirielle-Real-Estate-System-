@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-2xl">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Add New Property</h2>

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('management.properties.store') }}" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md">
        @csrf
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
            <input type="text" name="title" value="{{ old('title') }}" class="w-full rounded-md border-gray-300 shadow-sm" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
            <input type="text" name="location" value="{{ old('location') }}" class="w-full rounded-md border-gray-300 shadow-sm" required>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bedrooms</label>
                <input type="number" name="bedrooms" value="{{ old('bedrooms') }}" class="w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Size (sqft)</label>
                <input type="text" name="size" value="{{ old('size') }}" class="w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Price (KES)</label>
                <input type="number" step="0.01" name="price" value="{{ old('price') }}" class="w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deposit Amount (KES)</label>
                <input type="number" step="0.01" name="deposit_amount" value="{{ old('deposit_amount') }}" class="w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
        </div>

        <!-- 3. Image Upload -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Property Image</label>
            <input type="file" name="image" class="w-full rounded-md border-gray-300 shadow-sm">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Amenities (optional)</label>
            <input type="text" name="amenities" value="{{ old('amenities') }}" class="w-full rounded-md border-gray-300 shadow-sm">
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
            <textarea name="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm">{{ old('description') }}</textarea>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Save Property</button>
    </form>
</div>
@endsection