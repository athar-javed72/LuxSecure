<?php

namespace App\Http\Controllers;

use App\Support\LegalAcceptanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LegalAcceptanceController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $pending = LegalAcceptanceService::pendingFor($request->user());
        if ($pending === []) {
            return redirect()->intended(route('home'));
        }

        $pages = LegalAcceptanceService::currentPublished();

        return view('legal.accept', [
            'terms' => $pages['terms'],
            'privacy' => $pages['privacy'],
            'pending' => $pending,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(LegalAcceptanceService::validationRules());

        LegalAcceptanceService::recordFromRequest(
            $request->user(),
            [
                'accept_terms' => $request->boolean('accept_terms'),
                'accept_privacy' => $request->boolean('accept_privacy'),
            ],
            $request->ip()
        );

        return redirect()->intended(route('home'))->with('success', 'Thank you. Your acceptance has been recorded.');
    }
}
