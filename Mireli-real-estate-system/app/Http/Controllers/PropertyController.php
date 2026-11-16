<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    // This is the page where clients browse houses
    public function index(Request $request)
    {
        // Start a query to get all properties
        $query = Property::query();

        // Apply filters if the user typed them in the search bar
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

        // Get the results, paginated (6 per page)
        $properties = $query->latest()->paginate(6);

        // Return the view and pass the properties to it
        return view('properties.index', compact('properties'));
    }

    // This is the page that shows a single house
    public function show(Property $property)
    {
        return view('properties.show', compact('property'));
    }
}