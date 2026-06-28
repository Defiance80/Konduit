<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Models\Client;
use App\Models\Project;
use App\Services\AiWebsiteScanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditController extends Controller
{
    public function index()
    {
        $tenantId = Auth::user()->tenant_id;
        $audits   = Audit::where('tenant_id', $tenantId)->with(['client', 'conductedBy'])->latest()->paginate(20);
        $clients  = Client::where('tenant_id', $tenantId)->orderBy('name')->get();

        $stats = [
            'total'     => Audit::where('tenant_id', $tenantId)->count(),
            'complete'  => Audit::where('tenant_id', $tenantId)->where('status', 'complete')->count(),
            'shared'    => Audit::where('tenant_id', $tenantId)->where('visible_to_client', true)->count(),
            'avg_score' => round(Audit::where('tenant_id', $tenantId)->whereNotNull('score')->avg('score') ?? 0),
        ];

        return view('agency.audits.index', compact('audits', 'clients', 'stats'));
    }

    public function create()
    {
        $clients  = Client::where('tenant_id', Auth::user()->tenant_id)->orderBy('name')->get();
        $projects = Project::where('tenant_id', Auth::user()->tenant_id)->orderBy('name')->get();
        return view('agency.audits.create', compact('clients', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'         => 'required|exists:clients,id',
            'project_id'        => 'nullable|exists:projects,id',
            'title'             => 'required|string|max:255',
            'type'              => 'required|in:seo,website,social,content,technical,performance,general',
            'website_url'       => 'nullable|url|max:500',
            'executive_summary' => 'nullable|string',
            'score'             => 'nullable|integer|min:0|max:100',
            'audited_at'        => 'nullable|date',
        ]);

        $validated['conducted_by'] = Auth::id();
        $validated['status']       = 'draft';
        $validated['tenant_id']    = Auth::user()->tenant_id;

        $audit = Audit::create($validated);

        if (!empty($validated['website_url'])) {
            return redirect()->route('agency.audits.scan', $audit);
        }

        return redirect()->route('agency.audits.show', $audit)->with('success', 'Audit created.');
    }

    public function runScan(Audit $audit, AiWebsiteScanService $scanner)
    {
        if (!$audit->website_url) {
            return back()->with('error', 'No website URL set for this audit.');
        }

        $audit->update(['status' => 'in_progress']);

        $result     = $scanner->scan($audit->website_url);
        $categories = $result['categories'] ?? [];

        $findings = [];
        foreach ($categories as $key => $cat) {
            foreach ($cat['issues'] ?? [] as $issue) {
                $findings[] = ['title' => $issue, 'severity' => 'medium', 'category' => $cat['label'] ?? $key, 'detail' => ''];
            }
        }

        $recs = collect($result['top_recommendations'] ?? [])->map(fn ($r) => [
            'title'    => $r['title'] ?? '',
            'priority' => $r['priority'] ?? 'medium',
            'detail'   => $r['detail'] ?? '',
            'category' => $r['category'] ?? '',
            'impact'   => $r['impact'] ?? 'medium',
        ])->toArray();

        $scores       = collect($categories)->map(fn ($c) => $c['score'] ?? 0);
        $overallScore = $result['overall_score'] ?? ($scores->isEmpty() ? 0 : (int) $scores->avg());

        $audit->update([
            'score'             => $overallScore,
            'category_scores'   => $categories,
            'scan_data'         => $result['scan_data'] ?? [],
            'ai_analysis'       => $result['executive_summary'] ?? '',
            'executive_summary' => $result['executive_summary'] ?? '',
            'findings'          => $findings,
            'recommendations'   => $recs,
            'status'            => isset($result['error']) ? 'draft' : 'complete',
            'audited_at'        => now(),
        ]);

        return redirect()->route('agency.audits.show', $audit)
            ->with('success', isset($result['error']) ? 'Scan finished with errors — check the report.' : 'Website audit complete!');
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
            'website_url'       => 'nullable|url|max:500',
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
