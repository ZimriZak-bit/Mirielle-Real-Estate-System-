
@extends('layouts.app')

@section('title', 'Add New Property | Mireli')

@section('content')

<div class="properties-management">

    {{-- Page Heading --}}
    <div class="page-title">
        <h1>Add New Property</h1>
        <p class="page-lead">
            Enter the property details and upload photographs for clients to view.
        </p>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct the following errors:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Property Form --}}
    <div class="property-form-container">

        <form
            action="{{ route('management.properties.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >

            @csrf

            {{-- Basic Information --}}
            <div class="property-form-section">

                <h2>Property Information</h2>
                <p class="text-muted">Provide the basic details of the property.</p>

                <div class="form-group">
                    <label for="title">Property Title *</label>
                    <input
                        type="text"
                        name="title"
                        id="title"
                        class="form-control"
                        placeholder="e.g. Modern 3 Bedroom Apartment"
                        value="{{ old('title') }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="location">Location *</label>
                    <input
                        type="text"
                        name="location"
                        id="location"
                        class="form-control"
                        placeholder="e.g. Kilimani, Nairobi"
                        value="{{ old('location') }}"
                        required
                    >
                </div>

                <div class="property-form-grid">

                    <div class="form-group">
                        <label for="bedrooms">Number of Bedrooms *</label>
                        <input
                            type="number"
                            name="bedrooms"
                            id="bedrooms"
                            class="form-control"
                            min="0"
                            value="{{ old('bedrooms') }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="size">Property Size *</label>
                        <input
                            type="text"
                            name="size"
                            id="size"
                            class="form-control"
                            placeholder="e.g. 1200 sq ft"
                            value="{{ old('size') }}"
                            required
                        >
                    </div>

                </div>

            </div>

            {{-- Financial Information --}}
            <div class="property-form-section">

                <h2>Pricing Information</h2>

                <div class="property-form-grid">

                    <div class="form-group">
                        <label for="price">Property Price (KES) *</label>
                        <input
                            type="number"
                            name="price"
                            id="price"
                            class="form-control"
                            min="0"
                            step="0.01"
                            placeholder="Enter property price"
                            value="{{ old('price') }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="deposit_amount">Deposit Amount (KES) *</label>
                        <input
                            type="number"
                            name="deposit_amount"
                            id="deposit_amount"
                            class="form-control"
                            min="0"
                            step="0.01"
                            placeholder="Enter deposit amount"
                            value="{{ old('deposit_amount') }}"
                            required
                        >
                    </div>

                </div>

            </div>

            {{-- Description and Amenities --}}
            <div class="property-form-section">

                <h2>Property Description</h2>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea
                        name="description"
                        id="description"
                        class="form-control"
                        rows="5"
                        placeholder="Describe the property..."
                    >{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="amenities">Amenities</label>
                    <textarea
                        name="amenities"
                        id="amenities"
                        class="form-control"
                        rows="3"
                        placeholder="e.g. Parking, swimming pool, security, Wi-Fi"
                    >{{ old('amenities') }}</textarea>
                </div>

            </div>

            {{-- Property Image --}}
            <div class="property-form-section">

                <h2>Property Photograph</h2>

                <p class="text-muted">
                    Upload a clear photograph of the property for clients to view.
                </p>

                <div class="form-group">
                    <label for="image">Choose Property Image</label>

                    <input
                        type="file"
                        name="image"
                        id="image"
                        class="form-control"
                        accept="image/jpeg,image/png,image/jpg,image/webp"
                    >

                    <small class="text-muted">
                        Accepted formats: JPG, JPEG, PNG and WEBP. Maximum size: 2MB.
                    </small>
                </div>

                <div id="image-preview-container" style="display:none; margin-top:15px;">
                    <p><strong>Image Preview</strong></p>
                    <img
                        id="image-preview"
                        src=""
                        alt="Property preview"
                        style="width:100%; max-width:400px; height:230px; object-fit:cover; border-radius:6px;"
                    >
                </div>

            </div>

            {{-- Form Buttons --}}
            <div class="property-form-actions">

                <a
                    href="{{ route('management.properties.index') }}"
                    class="btn btn-outline"
                >
                    Cancel
                </a>

                <button type="submit" class="btn">
                    Save Property
                </button>

            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')
<script>
    document.getElementById('image').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('image-preview');
        const container = document.getElementById('image-preview-container');

        if (file && file.type.startsWith('image/')) {
            preview.src = URL.createObjectURL(file);
            container.style.display = 'block';
        } else {
            preview.src = '';
            container.style.display = 'none';
        }
    });
</script>
@endpush
