<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TeamController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        $members = User::where('tenant_id', $tenantId)
            ->where('user_type', 'agency_user')
            ->with('roles')
            ->orderBy('name')
            ->get();

        $clientContacts = User::where('tenant_id', $tenantId)
            ->where('user_type', 'client_contact')
            ->with(['roles', 'client'])
            ->orderBy('name')
            ->get();

        return view('agency.team.index', compact('members', 'clientContacts'));
    }

    public function invite(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'role'     => 'required|in:agency_admin,agency_member',
            'job_title' => 'nullable|string|max:100',
        ]);

        $user = User::create([
            'tenant_id'  => auth()->user()->tenant_id,
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make('password'),
            'job_title'  => $request->job_title,
            'user_type'  => 'agency_user',
        ]);

        $user->assignRole($request->role);

        return redirect()->route('agency.team.index')
            ->with('success', "{$user->name} has been added to your team.");
    }

    public function destroy(User $user)
    {
        if ($user->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot remove yourself.');
        }

        $user->delete();

        return redirect()->route('agency.team.index')
            ->with('success', 'Team member removed.');
    }
}
