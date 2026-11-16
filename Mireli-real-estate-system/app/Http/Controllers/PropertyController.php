<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    // --- CLIENT METHODS ---
    public function index(Request $request)
    {
        $query = Property::query();

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
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
        ]);

        $validated['status'] = Property::AVAILABLE;
        Property::create($validated);

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
        ]);

        $property->update($validated);

        return redirect()->route('management.properties.index')->with('success', 'Property updated successfully!');
    }

    public function destroy(Property $property)
    {
        $property->delete();
        return redirect()->route('management.properties.index')->with('success', 'Property deleted successfully!');
    }
}