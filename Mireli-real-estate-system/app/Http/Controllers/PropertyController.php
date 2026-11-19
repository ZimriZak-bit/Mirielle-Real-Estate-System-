<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CLIENT SIDE
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Property::with('images')
            ->where('status', Property::AVAILABLE);

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $properties = $query->latest()->paginate(6);

        return view('properties.index', compact('properties'));
    }

    public function show(Property $property)
    {
        if (!$property->isAvailable()) {
            abort(404);
        }

        $property->load('images');

        return view('properties.show', compact('property'));
    }

    /*
    |--------------------------------------------------------------------------
    | MANAGEMENT SIDE
    |--------------------------------------------------------------------------
    */

    public function manage()
    {
        $properties = Property::with('images')
            ->latest()
            ->paginate(10);

        return view('management.properties.index', compact('properties'));
    }

    public function create()
    {
        return view('management.properties.create');
    }

    // Automatically assign an unused built-in photograph
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'bedrooms' => 'required|integer|min:0',
            'size' => 'required|string|max:255',
            'amenities' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
        ]);

        $builtInImages = [
            'images/properties/house-1.jpg',
            'images/properties/house-2.jpg',
            'images/properties/house-3.jpg',
            'images/properties/house-4.jpg',
            'images/properties/house-5.jpg',
            'images/properties/house-6.jpg',
            'images/properties/house-7.jpg',
            'images/properties/house-8.jpg',
        ];

        // Find photographs already assigned to existing properties
        $usedImages = Property::with('images')
            ->get()
            ->flatMap(fn ($existingProperty) =>
                $existingProperty->images->pluck('image_url')
            )
            ->all();

        // Find photographs that have not yet been assigned
        $availableImages = array_values(
            array_diff($builtInImages, $usedImages)
        );

        // Do not create a property if no unique photograph is available
        if (empty($availableImages)) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'images' => 'All built-in property photographs are currently assigned. Add more photographs before creating another property.'
                ]);
        }

        $property = Property::create(array_merge($validated, [
            'status' => Property::AVAILABLE,
        ]));

        $property->images()->create([
            'image_url' => $availableImages[0],
            'is_primary' => true,
        ]);

        return redirect()
            ->route('management.properties.index')
            ->with('success', 'Property added successfully with its own photograph.');
    }

    public function edit(Property $property)
    {
        $property->load('images');

        return view('management.properties.edit', compact('property'));
    }

    public function update(Request $request, Property $property)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'bedrooms' => 'required|integer|min:0',
            'size' => 'required|string|max:255',
            'amenities' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'status' => 'required|in:available,reserved,sold',
        ]);

        $property->update($validated);

        return redirect()
            ->route('management.properties.index')
            ->with('success', 'Property updated successfully.');
    }

    public function destroy(Property $property)
    {
        foreach ($property->images as $image) {

            // Keep built-in photographs inside the project
            if (!str_starts_with($image->image_url, 'images/')) {
                Storage::disk('public')->delete($image->image_url);
            }

            $image->delete();
        }

        $property->delete();

        return redirect()
            ->route('management.properties.index')
            ->with('success', 'Property deleted successfully.');
    }
}
