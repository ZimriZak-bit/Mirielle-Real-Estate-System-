<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Customization;
use Illuminate\View\View;

/**
 * PAIR 3: management's read-only view of the customization preferences clients
 * have saved (interface I7). Management reads the form; it does not edit it.
 */
class CustomizationController extends Controller
{
    public function index(): View
    {
        $customizations = Customization::query()
            ->with(['property', 'client'])
            ->latest('updated_at')
            ->get();

        return view('management.customizations.index', ['customizations' => $customizations]);
    }
}
