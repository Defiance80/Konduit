<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientHealthScore;
use App\Services\RelationshipIntelligenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RelationshipController extends Controller
{
    public function index(RelationshipIntelligenceService $service): \Illuminate\View\View
    {
        $tenantId = Auth::user()->tenant_id;
        $service->calculateForTenant($tenantId);

        $scores = ClientHealthScore::where('tenant_id', $tenantId)
            ->with('client')
            ->orderBy('churn_risk_score', 'desc')
            ->get();

        $clients = Client::where('tenant_id', $tenantId)
            ->with(['projects', 'retainers', 'tickets'])
            ->get();

        return view('agency.relationship.index', compact('scores', 'clients'));
    }

    public function recalculate(Client $client, RelationshipIntelligenceService $service): \Illuminate\Http\RedirectResponse
    {
        $service->calculateForClient($client);
        return back()->with('success', "Health score recalculated for {$client->name}.");
    }
}
