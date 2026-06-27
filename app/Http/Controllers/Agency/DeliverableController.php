<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Deliverable;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DeliverableController extends Controller
{
    public function index(Request $request)
    {
        $query = Deliverable::with(['project', 'client', 'reviewer'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('project')) {
            $query->where('project_id', $request->project);
        }
        if ($request->filled('client')) {
            $query->where('client_id', $request->client);
        }
        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $deliverables = $query->paginate(20)->withQueryString();

        $stats = [
            'pending'   => Deliverable::where('status', 'pending')->count(),
            'in_review' => Deliverable::where('status', 'in_review')->count(),
            'approved'  => Deliverable::where('status', 'approved')->count(),
            'rejected'  => Deliverable::where('status', 'rejected')->count(),
        ];

        $projects = Project::orderBy('name')->get();
        $clients  = Client::orderBy('name')->get();

        return view('agency.deliverables.index', compact('deliverables', 'stats', 'projects', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id'  => 'required|exists:projects,id',
            'client_id'   => 'required|exists:clients,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date',
            'file_url'    => 'nullable|url|max:2048',
            'file'        => 'nullable|file|max:51200', // 50MB
        ]);

        $validated['reviewer_id'] = auth()->id();
        $validated['status']      = 'pending';

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('deliverables', 'public');
            $validated['file_path'] = $path;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_mime'] = $file->getMimeType();
            $validated['file_size'] = $file->getSize();
        }

        $deliverable = Deliverable::create($validated);

        $redirect = $request->input('redirect', route('agency.projects.show', $validated['project_id']));
        return redirect($redirect)->with('success', 'Deliverable added.');
    }

    public function show(Deliverable $deliverable)
    {
        $deliverable->load(['project', 'client', 'reviewer']);
        return view('agency.deliverables.show', compact('deliverable'));
    }

    public function update(Request $request, Deliverable $deliverable)
    {
        $validated = $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date',
            'file_url'    => 'nullable|url|max:2048',
            'file'        => 'nullable|file|max:51200',
        ]);

        if ($request->hasFile('file')) {
            // Remove old file if stored locally
            if ($deliverable->file_path) {
                Storage::disk('public')->delete($deliverable->file_path);
            }
            $file = $request->file('file');
            $path = $file->store('deliverables', 'public');
            $validated['file_path'] = $path;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_mime'] = $file->getMimeType();
            $validated['file_size'] = $file->getSize();
            $validated['version']   = $deliverable->version + 1;
            // Reset to pending after new file upload
            $validated['status']    = 'pending';
            $validated['client_feedback']   = null;
            $validated['rejection_reason']  = null;
        }

        $deliverable->update($validated);

        return back()->with('success', 'Deliverable updated.');
    }

    public function destroy(Deliverable $deliverable)
    {
        $projectId = $deliverable->project_id;

        if ($deliverable->file_path) {
            Storage::disk('public')->delete($deliverable->file_path);
        }

        $deliverable->delete();

        return redirect()->route('agency.projects.show', $projectId)
            ->with('success', 'Deliverable deleted.');
    }

    // Agency submits deliverable to client for review
    public function submit(Deliverable $deliverable)
    {
        $deliverable->update([
            'status'       => 'in_review',
            'submitted_at' => now(),
            'client_feedback'  => null,
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Deliverable sent to client for review.');
    }

    // Agency marks as delivered (after approval)
    public function deliver(Deliverable $deliverable)
    {
        abort_unless($deliverable->status === 'approved', 403, 'Must be approved before marking delivered.');

        $deliverable->update(['status' => 'delivered']);

        return back()->with('success', 'Deliverable marked as delivered.');
    }

    // Agency manually approves (bypass client — e.g., client confirmed verbally)
    public function approve(Deliverable $deliverable)
    {
        $deliverable->update([
            'status'      => 'approved',
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Deliverable approved.');
    }
}
