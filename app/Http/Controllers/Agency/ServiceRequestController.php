<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    public function index()
    {
        $requests = ServiceRequest::with(['client', 'service', 'submittedBy'])
            ->latest()
            ->paginate(20);

        $pendingCount = ServiceRequest::where('status', 'pending')->count();

        return view('agency.service-requests.index', compact('requests', 'pendingCount'));
    }

    public function update(Request $request, ServiceRequest $serviceRequest)
    {
        $validated = $request->validate([
            'status'         => 'required|in:pending,reviewing,quoted,accepted,declined',
            'price_quoted'   => 'nullable|numeric|min:0',
            'agency_response'=> 'nullable|string',
        ]);

        $serviceRequest->update($validated);

        return back()->with('success', 'Service request updated.');
    }
}
