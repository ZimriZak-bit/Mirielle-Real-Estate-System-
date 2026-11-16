<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Properties | Mireli</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

    <nav class="bg-white shadow-md p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold text-blue-600">Mireli Management</h1>
            <a href="/properties" class="text-gray-600 hover:text-blue-600">View Public Site</a>
        </div>
    </nav>

    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold">Manage Properties</h2>
            <a href="{{ route('management.properties.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">+ Add New Property</a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($properties as $property)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $property->title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $property->location }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">KES {{ number_format($property->price, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($property->status === 'available')
                                    <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded">Available</span>
                                @elseif($property->status === 'reserved')
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-1 rounded">Reserved</span>
                                @else
                                    <span class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded">Sold</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('management.properties.edit', $property->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                
                                <form action="{{ route('management.properties.destroy', $property->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this property?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No properties found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $properties->links() }}
        </div>
    </div>
</body>
</html>