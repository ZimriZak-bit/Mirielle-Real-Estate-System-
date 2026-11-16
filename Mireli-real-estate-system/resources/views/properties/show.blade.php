<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $property->title }} | Mireli</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

    <!-- Navigation Bar -->
    <nav class="bg-white shadow-md p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold text-blue-600">Mireli Real Estate</h1>
            <div class="space-x-4">
                <a href="/properties" class="text-blue-600 font-semibold">&larr; Back to Properties</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-6 max-w-4xl">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header Image Placeholder -->
            <div class="h-64 bg-gray-300 flex items-center justify-center text-gray-500">
                Property Image Placeholder
            </div>
            
            <div class="p-8">
                <div class="flex justify-between items-start mb-4">
                    <h2 class="text-3xl font-bold">{{ $property->title }}</h2>
                    @if($property->status === 'available')
                        <span class="bg-green-100 text-green-800 font-semibold px-3 py-1 rounded">Available</span>
                    @elseif($property->status === 'reserved')
                        <span class="bg-yellow-100 text-yellow-800 font-semibold px-3 py-1 rounded">Reserved</span>
                    @else
                        <span class="bg-red-100 text-red-800 font-semibold px-3 py-1 rounded">Sold</span>
                    @endif
                </div>

                <p class="text-gray-600 text-lg mb-6">{{ $property->location }}</p>

                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-sm text-gray-500">Price</p>
                        <p class="text-xl font-bold text-blue-600">KES {{ number_format($property->price, 2) }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-sm text-gray-500">Deposit Required</p>
                        <p class="text-xl font-bold text-gray-800">KES {{ number_format($property->deposit_amount, 2) }}</p>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold mb-2">Details</h3>
                    <p class="text-gray-700 mb-2"><strong>Bedrooms:</strong> {{ $property->bedrooms }}</p>
                    <p class="text-gray-700 mb-2"><strong>Size:</strong> {{ $property->size }}</p>
                    <p class="text-gray-700"><strong>Amenities:</strong> {{ $property->amenities ?? 'N/A' }}</p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold mb-2">Description</h3>
                    <p class="text-gray-700">{{ $property->description ?? 'No description provided.' }}</p>
                </div>

                <!-- Buttons for Pair 3 and Pair 4 -->
                <div class="border-t pt-6 flex gap-4">
                    <!-- Request Viewing goes to Pair 3 -->
                    <a href="#" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 w-full text-center">
                        Request Viewing
                    </a>
                    
                    <!-- Pay Deposit goes to Pair 4 -->
                    @if($property->isAvailable())
                        <a href="#" class="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 w-full text-center">
                            Pay Deposit
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

</body>
</html>