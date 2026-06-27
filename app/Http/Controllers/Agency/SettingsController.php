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

        return view('agency.settings.index', compact('tenant', 'user'));
    }

    public function updateAgency(Request $request)
    {
        $tenant = auth()->user()->tenant;

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
}
