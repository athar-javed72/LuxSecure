<?php

namespace App\Http\Controllers;

use App\Models\KycDocument;
use App\Support\StorageDisk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KycDocumentController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', KycDocument::class);

        $documents = KycDocument::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('kyc.index', compact('documents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', KycDocument::class);

        $data = $request->validate([
            'type' => ['required', Rule::in(KycDocument::TYPES)],
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:8192'],
        ]);

        $path = $request->file('file')->store('kyc/'.$request->user()->id, StorageDisk::private());

        KycDocument::create([
            'user_id' => $request->user()->id,
            'type' => $data['type'],
            'file_path' => $path,
            'status' => 'pending',
        ]);

        return back()->with('success', 'KYC document uploaded for review.');
    }
}
