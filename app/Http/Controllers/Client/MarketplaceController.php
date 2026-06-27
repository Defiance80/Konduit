<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function index()
    {
        $categories = ServiceCategory::with(['services' => fn ($q) => $q->where('status', 'active')->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        $myRequests = ServiceRequest::where('client_id', auth()->user()->client_id)
            ->with('service')
            ->latest()
            ->get();

        return view('client.marketplace.index', compact('categories', 'myRequests'));
    }

    public function requestService(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'nullable|exists:services,id',
            'title'      => 'required|string|max:255',
            'message'    => 'nullable|string',
        ]);

        ServiceRequest::create([
            'client_id'    => auth()->user()->client_id,
            'service_id'   => $validated['service_id'] ?? null,
            'submitted_by' => auth()->id(),
            'title'        => $validated['title'],
            'message'      => $validated['message'] ?? null,
            'status'       => 'pending',
        ]);

        return back()->with('success', 'Service request submitted. We\'ll follow up shortly.');
    }
}
