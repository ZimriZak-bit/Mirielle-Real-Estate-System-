<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mireli | Properties</title>
    <!-- We will use Tailwind CSS via CDN for quick styling -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

    <!-- Navigation Bar -->
    <nav class="bg-white shadow-md p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold text-blue-600">Mireli Real Estate</h1>
            <div class="space-x-4">
                <a href="/" class="text-gray-600 hover:text-blue-600">Home</a>
                <a href="/properties" class="text-blue-600 font-semibold">Properties</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto p-6">
        <h2 class="text-3xl font-bold mb-6">Browse Properties</h2>

        <!-- Search and Filter Form -->
        <form method="GET" action="{{ route('properties.index') }}" class="bg-white p-4 rounded-lg shadow-md mb-8 flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700">Location</label>
                <input type="text" name="location" value="{{ request('location') }}" placeholder="e.g. Nairobi" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Min Price</label>
                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Max Price</label>
                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="10000000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>Reserved</option>
                    <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Search</button>
        </form>

        <!-- Property Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($properties as $property)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold">{{ $property->title }}</h3>
                            <!-- Status Badge -->
                            @if($property->status === 'available')
                                <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded">Available</span>
                            @elseif($property->status === 'reserved')
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-1 rounded">Reserved</span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded">Sold</span>
                            @endif
                        </div>
                        <p class="text-gray-600 text-sm mb-2">{{ $property->location }}</p>
                        <p class="text-2xl font-bold text-blue-600 mb-4">KES {{ number_format($property->price, 2) }}</p>
                        
                        <div class="flex justify-between text-sm text-gray-500 mb-4">
                            <span>{{ $property->bedrooms }} Beds</span>
                            <span>{{ $property->size }}</span>
                        </div>
                        
                        <a href="#" class="block w-full text-center bg-gray-100 text-gray-800 py-2 rounded-md hover:bg-gray-200">View Details</a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center text-gray-500 py-10">
                    <p>No properties found matching your search.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $properties->links() }}
        </div>
    </div>

</body>
</html>