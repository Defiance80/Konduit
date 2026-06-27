<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index()
    {
        $audits  = Audit::with(['client', 'conductedBy'])->latest()->paginate(20);
        $clients = Client::orderBy('name')->get();

        $stats = [
            'total'    => Audit::count(),
            'complete' => Audit::where('status', 'complete')->count(),
            'shared'   => Audit::where('visible_to_client', true)->count(),
            'avg_score'=> round(Audit::whereNotNull('score')->avg('score') ?? 0),
        ];

        return view('agency.audits.index', compact('audits', 'clients', 'stats'));
    }

    public function create()
    {
        $clients  = Client::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        return view('agency.audits.create', compact('clients', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'         => 'required|exists:clients,id',
            'project_id'        => 'nullable|exists:projects,id',
            'title'             => 'required|string|max:255',
            'type'              => 'required|in:seo,website,social,content,technical,performance,general',
            'executive_summary' => 'nullable|string',
            'score'             => 'nullable|integer|min:0|max:100',
            'audited_at'        => 'nullable|date',
        ]);

        $validated['conducted_by'] = auth()->id();
        $validated['status']       = 'draft';

        $audit = Audit::create($validated);

        return redirect()->route('agency.audits.show', $audit)->with('success', 'Audit created.');
    }

    public function show(Audit $audit)
    {
        $audit->load(['client', 'project', 'conductedBy']);
        return view('agency.audits.show', compact('audit'));
    }

    public function update(Request $request, Audit $audit)
    {
        $validated = $request->validate([
            'title'             => 'sometimes|required|string|max:255',
            'executive_summary' => 'nullable|string',
            'ai_analysis'       => 'nullable|string',
            'score'             => 'nullable|integer|min:0|max:100',
            'status'            => 'sometimes|in:draft,in_progress,complete,shared',
            'visible_to_client' => 'boolean',
            'audited_at'        => 'nullable|date',
        ]);

        $audit->update($validated);

        return back()->with('success', 'Audit updated.');
    }

    public function addFinding(Request $request, Audit $audit)
    {
        $request->validate([
            'title'    => 'required|string',
            'severity' => 'required|in:critical,high,medium,low,info',
            'detail'   => 'nullable|string',
        ]);

        $findings   = $audit->findings ?? [];
        $findings[] = [
            'id'       => count($findings) + 1,
            'title'    => $request->title,
            'severity' => $request->severity,
            'detail'   => $request->detail,
        ];

        $audit->update(['findings' => $findings]);

        return back()->with('success', 'Finding added.');
    }

    public function addRecommendation(Request $request, Audit $audit)
    {
        $request->validate([
            'title'    => 'required|string',
            'priority' => 'required|in:critical,high,medium,low',
            'detail'   => 'nullable|string',
        ]);

        $recs   = $audit->recommendations ?? [];
        $recs[] = [
            'id'       => count($recs) + 1,
            'title'    => $request->title,
            'priority' => $request->priority,
            'detail'   => $request->detail,
        ];

        $audit->update(['recommendations' => $recs]);

        return back()->with('success', 'Recommendation added.');
    }

    public function share(Audit $audit)
    {
        $audit->update(['visible_to_client' => true, 'status' => 'shared']);
        return back()->with('success', 'Audit shared with client.');
    }

    public function destroy(Audit $audit)
    {
        $audit->delete();
        return redirect()->route('agency.audits.index')->with('success', 'Audit deleted.');
    }
}
