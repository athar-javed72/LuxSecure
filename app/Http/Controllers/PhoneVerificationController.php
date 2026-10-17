<?php

namespace App\Http\Controllers;

use App\Services\PhoneOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PhoneVerificationController extends Controller
{
    public function __construct(private readonly PhoneOtpService $otp) {}

    public function show(): View
    {
        return view('auth.phone-verify', [
            'user' => auth()->user(),
        ]);
    }

    public function send(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:40'],
        ]);

        $this->otp->send($request->user(), $data['phone']);

        return back()->with('success', 'Verification code sent.');
    }

    public function verify(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:40'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        $this->otp->verify($request->user(), $data['phone'], $data['code']);

        return redirect()->route('profile')->with('success', 'Phone number verified.');
    }
}
