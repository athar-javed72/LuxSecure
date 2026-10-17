<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgentProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AgentProfileController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', AgentProfile::class);

        $profiles = AgentProfile::with('user')->orderBy('sort_order')->paginate(20);

        return view('admin.agents.index', compact('profiles'));
    }

    public function create(): View
    {
        $this->authorize('create', AgentProfile::class);

        $users = User::role('agent')
            ->whereDoesntHave('agentProfile')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.agents.create', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', AgentProfile::class);

        $data = $this->validated($request);
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('agents', 'public');
        }

        AgentProfile::create($data);

        return redirect()->route('admin.agents.index')->with('success', 'Agent profile created.');
    }

    public function edit(AgentProfile $agent_profile): View
    {
        $this->authorize('update', $agent_profile);
        $agent_profile->load('user');

        return view('admin.agents.edit', ['profile' => $agent_profile]);
    }

    public function update(Request $request, AgentProfile $agent_profile): RedirectResponse
    {
        $this->authorize('update', $agent_profile);

        $data = $this->validated($request, updating: true);
        if ($request->hasFile('photo')) {
            if ($agent_profile->photo_path && ! str_starts_with($agent_profile->photo_path, 'http')) {
                Storage::disk('public')->delete($agent_profile->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('agents', 'public');
        }

        unset($data['user_id']);
        $agent_profile->update($data);

        return back()->with('success', 'Agent profile updated.');
    }

    public function destroy(AgentProfile $agent_profile): RedirectResponse
    {
        $this->authorize('delete', $agent_profile);
        $agent_profile->delete();

        return redirect()->route('admin.agents.index')->with('success', 'Agent profile deleted.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, bool $updating = false): array
    {
        $rules = [
            'phone' => ['nullable', 'string', 'max:40'],
            'whatsapp' => ['nullable', 'string', 'max:40'],
            'license_number' => ['nullable', 'string', 'max:80'],
            'agency' => ['nullable', 'string', 'max:160'],
            'bio' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'is_verified' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];

        if (! $updating) {
            $rules['user_id'] = ['required', 'exists:users,id', 'unique:agent_profiles,user_id'];
        }

        $data = $request->validate($rules);
        $data['is_verified'] = $request->boolean('is_verified');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
