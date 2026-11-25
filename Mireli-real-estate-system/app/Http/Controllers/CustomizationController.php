<?php


namespace App\Http\Controllers;

use App\Models\Customization;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * PAIR 3: the client's customization preferences (stretch goal).
 *
 * Interface I7: a client may customize a house only when they are its buyer and
 * the house is Reserved or Sold. This controller never changes property status
 * or the buyer; it only reads those two fields.
 */
class CustomizationController extends Controller
{
    public function index(Request $request): View
    {
        $client = $request->user();

        $properties = Property::query()
            ->where( 'buyer_id', $client->getKey() )
            ->whereIn( 'status', [Property::RESERVED, Property::SOLD] )
            ->orderBy('title' )
            ->get();

        $customizations = Customization::query()
            ->where('client_id', $client->getKey())
            ->whereIn('property_id', $properties->pluck('id'))
            ->get()
            ->keyBy('property_id');

        return view('customizations.index', [
            'properties' => $properties,
            'customizations' => $customizations,
        ]);
    }
/**Done by Jewel */
    public function store(Request $request, Property $property): RedirectResponse
    {
        $client = $request->user();

        abort_unless(
            $property->buyer_id === $client->getKey()
                && in_array($property->status, [Property::RESERVED, Property::SOLD], true),
            403,
            'You can only customize a house you have reserved or bought.',
        );

        $data = $request->validate([
            'furniture_arrangement' => ['nullable', 'string', 'max:255'],
            'colour_scheme' => ['nullable', 'string', 'max:255'],
            'furnishing_status' => ['nullable', Rule::in(Customization::FURNISHING_STATUSES)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Customization::updateOrCreate(
            [
                'property_id' => $property->getKey(),
                'client_id' => $client->getKey(),
            ],
            $data,
        );

        return redirect()->route('customizations.index')
            ->with('success', 'Your preferences for '.$property->title.' have been saved. Management can see them.');
    }
}
