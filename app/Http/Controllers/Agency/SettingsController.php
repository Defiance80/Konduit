<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;
        $user   = auth()->user();

        if (!$tenant) abort(403, 'No agency tenant associated with this account.');

        return view('agency.settings.index', compact('tenant', 'user'));
    }

    public function updateAgency(Request $request)
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant) abort(403, 'No agency tenant associated with this account.');

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'nullable|email|max:255',
            'phone'    => 'nullable|string|max:50',
            'website'  => 'nullable|url|max:255',
            'timezone' => 'required|string|max:100',
        ]);

        $tenant->update($request->only('name', 'email', 'phone', 'website', 'timezone'));

        return back()->with('success', 'Agency profile updated.');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'job_title' => 'nullable|string|max:100',
            'phone'     => 'nullable|string|max:50',
        ]);

        $user->update($request->only('name', 'email', 'job_title', 'phone'));

        return back()->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'  => 'required',
            'password'          => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        auth()->user()->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password updated.');
    }

    public function integrations()
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant) abort(403, 'No agency tenant associated with this account.');

        $integrations = $tenant->data['integrations'] ?? [];
        return view('agency.settings.integrations', compact('tenant', 'integrations'));
    }

    public function saveIntegration(Request $request, string $service)
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant) abort(403);

        $allowed = ['google_analytics', 'google_search_console', 'asana', 'monday', 'motion', 'harvest', 'slack', 'zapier', 'hubspot', 'mailchimp'];
        if (!in_array($service, $allowed)) abort(404);

        $request->validate([
            'api_key'      => 'nullable|string|max:500',
            'api_token'    => 'nullable|string|max:500',
            'account_id'   => 'nullable|string|max:255',
            'workspace_id' => 'nullable|string|max:255',
            'webhook_url'  => 'nullable|url|max:500',
            'label'        => 'nullable|string|max:100',
        ]);

        $data = $tenant->data ?? [];
        $data['integrations'][$service] = array_filter([
            'api_key'      => $request->api_key,
            'api_token'    => $request->api_token,
            'account_id'   => $request->account_id,
            'workspace_id' => $request->workspace_id,
            'webhook_url'  => $request->webhook_url,
            'label'        => $request->label,
            'connected_at' => now()->toISOString(),
        ]);

        $tenant->update(['data' => $data]);

        return back()->with('success', ucwords(str_replace('_', ' ', $service)) . ' integration saved.');
    }

    public function removeIntegration(string $service)
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant) abort(403);

        $data = $tenant->data ?? [];
        unset($data['integrations'][$service]);
        $tenant->update(['data' => $data]);

        return back()->with('success', ucwords(str_replace('_', ' ', $service)) . ' disconnected.');
    }
}
