<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function index(Request $request): View
    {
        $query = Property::where('is_active', true)->with('images');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qry) use ($q) {
                $qry->where('title', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%");
            });
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('price')) {
            $range = $request->price;
            if (preg_match('/^(\d+)-(\d+)$/', $range, $m)) {
                $query->whereBetween('price', [(int) $m[1], (int) $m[2]]);
            }
        }

        $properties = match ($request->get('sort')) {
            'price_asc' => $query->orderBy('price')->paginate(12)->withQueryString(),
            'price_desc' => $query->orderByDesc('price')->paginate(12)->withQueryString(),
            default => $query->latest()->paginate(12)->withQueryString(),
        };

        return view('properties', compact('properties'));
    }

    public function show(Property $property): View
    {
        if (!$property->is_active) {
            abort(404);
        }
        $property->load('images');
        return view('properties.show', compact('property'));
    }
}
