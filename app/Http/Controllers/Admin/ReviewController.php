<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Review::class);

        $query = Review::query()->with(['listing', 'user', 'agent'])->latest();

        if ($request->filled('status') && in_array($request->status, Review::STATUSES, true)) {
            $query->where('status', $request->status);
        }

        return view('admin.reviews.index', [
            'reviews' => $query->paginate(20)->withQueryString(),
        ]);
    }

    public function moderate(Request $request, Review $review): RedirectResponse
    {
        $this->authorize('moderate', Review::class);

        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
        ]);

        $review->update(['status' => $data['status']]);

        return back()->with('success', 'Review '.$data['status'].'.');
    }
}
