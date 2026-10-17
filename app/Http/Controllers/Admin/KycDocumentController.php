<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgentProfile;
use App\Models\KycDocument;
use App\Notifications\KycResultNotification;
use App\Services\UserPrivilegedAttributes;
use App\Support\StorageDisk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KycDocumentController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', KycDocument::class);

        $query = KycDocument::query()->with(['user', 'reviewer'])->latest();

        if ($request->filled('status') && in_array($request->status, KycDocument::STATUSES, true)) {
            $query->where('status', $request->status);
        }

        return view('admin.kyc.index', [
            'documents' => $query->paginate(20)->withQueryString(),
        ]);
    }

    public function review(Request $request, KycDocument $kyc): RedirectResponse
    {
        $this->authorize('review', $kyc);

        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $kyc->update([
            'status' => $data['status'],
            'reason' => $data['reason'] ?? null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $user = $kyc->user;
        if ($data['status'] === 'approved') {
            app(UserPrivilegedAttributes::class)->setKycVerifiedAt($user, now());
            AgentProfile::query()->where('user_id', $user->id)->update(['is_verified' => true]);
        } elseif ($data['status'] === 'rejected') {
            $stillApproved = KycDocument::query()
                ->where('user_id', $user->id)
                ->where('status', 'approved')
                ->exists();
            if (! $stillApproved) {
                app(UserPrivilegedAttributes::class)->setKycVerifiedAt($user, null);
                AgentProfile::query()->where('user_id', $user->id)->update(['is_verified' => false]);
            }
        }

        $user->notify(new KycResultNotification($kyc->fresh()));

        return back()->with('success', 'KYC document '.$data['status'].'.');
    }

    public function download(KycDocument $kyc): StreamedResponse
    {
        $this->authorize('view', $kyc);

        return StorageDisk::privateDisk()->download($kyc->file_path);
    }
}
