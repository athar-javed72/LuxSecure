<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactInquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InquiryController extends Controller
{
    public function index(Request $request): View
    {
        $query = ContactInquiry::latest();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($request->filled('read')) {
            if ($request->read === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->read === 'read') {
                $query->where('is_read', true);
            }
        }

        $inquiries = $query->paginate(20)->withQueryString();

        return view('admin.inquiries.index', compact('inquiries'));
    }

    public function markRead(ContactInquiry $inquiry): RedirectResponse
    {
        $inquiry->update(['is_read' => true]);
        return back()->with('success', 'Marked as read.');
    }
}
