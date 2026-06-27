<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Deliverable;
use Illuminate\Http\Request;

class DeliverableController extends Controller
{
    public function index()
    {
        $clientId = auth()->user()->client_id;

        $deliverables = Deliverable::with(['project'])
            ->where('client_id', $clientId)
            ->whereIn('status', ['in_review', 'approved', 'rejected', 'delivered'])
            ->latest()
            ->get();

        $grouped = $deliverables->groupBy('status');

        $stats = [
            'awaiting'  => $deliverables->where('status', 'in_review')->count(),
            'approved'  => $deliverables->where('status', 'approved')->count(),
            'changes'   => $deliverables->where('status', 'rejected')->count(),
            'delivered' => $deliverables->where('status', 'delivered')->count(),
        ];

        return view('client.deliverables.index', compact('deliverables', 'grouped', 'stats'));
    }

    public function show(Deliverable $deliverable)
    {
        $this->authorizeClientAccess($deliverable);
        $deliverable->load(['project', 'reviewer']);
        return view('client.deliverables.show', compact('deliverable'));
    }

    public function approve(Request $request, Deliverable $deliverable)
    {
        $this->authorizeClientAccess($deliverable);
        abort_unless($deliverable->isInReview(), 403, 'This deliverable is not awaiting approval.');

        $deliverable->update([
            'status'          => 'approved',
            'approved_at'     => now(),
            'client_feedback' => $request->input('feedback'),
        ]);

        return back()->with('success', 'Deliverable approved. Thank you!');
    }

    public function reject(Request $request, Deliverable $deliverable)
    {
        $this->authorizeClientAccess($deliverable);
        abort_unless($deliverable->isInReview(), 403, 'This deliverable is not awaiting approval.');

        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:2000',
        ]);

        $deliverable->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'client_feedback'  => $request->rejection_reason,
        ]);

        return back()->with('success', 'Feedback submitted. The team will make revisions.');
    }

    private function authorizeClientAccess(Deliverable $deliverable): void
    {
        abort_unless(
            $deliverable->client_id === auth()->user()->client_id,
            403,
            'Access denied.'
        );
    }
}
