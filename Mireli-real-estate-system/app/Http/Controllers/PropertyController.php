<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    // --- CLIENT METHODS ---
    public function index(Request $request)
    {
        // 5. ONLY show available houses to clients
        $query = Property::where('status', Property::AVAILABLE);

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
        return view('properties.show', compact('property'));
    }

    // --- MANAGEMENT METHODS ---
    public function manage()
    {
        $properties = Property::latest()->paginate(10);
        return view('management.properties.index', compact('properties'));
    }

    public function create()
    {
        return view('management.properties.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'bedrooms' => 'required|integer|min:1',
            'size' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0|max:' . $request->price,
            'amenities' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' // 3. Image validation
        ]);

        $validated['status'] = Property::AVAILABLE;
        $property = Property::create($validated);

        // 2 & 3. Save the image
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('properties', 'public');
            $property->images()->create(['image_url' => $path, 'is_primary' => true]);
        }

        return redirect()->route('management.properties.index')->with('success', 'Property created successfully!');
    }

    public function edit(Property $property)
    {
        return view('management.properties.edit', compact('property'));
    }

    public function update(Request $request, Property $property)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'bedrooms' => 'required|integer|min:1',
            'size' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0|max:' . $request->price,
            'amenities' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|string|in:available,reserved,sold', // 6. Status update
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $property->update($validated);

        if ($request->hasFile('image')) {
            // Delete old images first (optional)
            foreach($property->images as $img) {
                Storage::disk('public')->delete($img->image_url);
                $img->delete();
            }
            $path = $request->file('image')->store('properties', 'public');
            $property->images()->create(['image_url' => $path, 'is_primary' => true]);
        }

        return redirect()->route('management.properties.index')->with('success', 'Property updated successfully!');
    }

    public function destroy(Property $property)
    {
        $property->delete();
        return redirect()->route('management.properties.index')->with('success', 'Property deleted successfully!');
    }
}