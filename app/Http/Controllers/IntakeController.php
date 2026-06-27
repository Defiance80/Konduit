<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\IntakeSubmission;
use App\Models\Tenant;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\NewTicketNotification;
use App\Notifications\TicketAcknowledgementNotification;
use App\Services\IntakeAiService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IntakeController extends Controller
{
    public function show(Tenant $tenant): \Illuminate\View\View
    {
        return view('intake.form', compact('tenant'));
    }

    public function store(Request $request, Tenant $tenant, IntakeAiService $ai): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'email'       => 'required|email|max:255',
            'company'     => 'nullable|string|max:255',
            'website_url' => 'nullable|url|max:500',
            'issue_type'  => 'required|in:bug,feature,content,question,billing,emergency,general',
            'description' => 'required|string|min:10|max:5000',
        ]);

        // AI classification
        $classification = $ai->classify(
            $data['name'],
            $data['issue_type'],
            $data['description'],
            $data['company'] ?? null,
            $data['website_url'] ?? null,
        );

        // Match existing client by email or email domain
        $client = Client::where('tenant_id', $tenant->id)
            ->where('email', $data['email'])
            ->first();

        if (!$client) {
            $domain = Str::after($data['email'], '@');
            $client = Client::where('tenant_id', $tenant->id)
                ->whereRaw('LOWER(website) LIKE ?', ["%{$domain}%"])
                ->first();
        }

        // No match — create a provisional client record from intake data
        if (!$client) {
            $client = Client::create([
                'tenant_id' => $tenant->id,
                'name'      => $data['company'] ?: $data['name'],
                'email'     => $data['email'],
                'website'   => $data['website_url'],
                'notes'     => 'Created automatically from intake submission.',
            ]);
        }

        // Map issue_type to ticket type enum values
        $typeMap = [
            'bug'       => 'bug',
            'feature'   => 'feature',
            'content'   => 'change_request',
            'question'  => 'question',
            'billing'   => 'task',
            'emergency' => 'bug',
            'general'   => 'task',
        ];
        $ticketType = $typeMap[$classification['issue_type'] ?? $data['issue_type']] ?? 'task';

        // Create ticket
        $ticket = Ticket::create([
            'tenant_id'      => $tenant->id,
            'client_id'      => $client->id,
            'subject'        => Str::limit($data['description'], 80),
            'description'    => $data['description'],
            'type'           => $ticketType,
            'priority'       => $classification['priority'] ?? 'medium',
            'status'         => 'open',
            'internal_notes' => $classification['internal_summary']
                ? [['note' => $classification['internal_summary'], 'source' => 'intake_ai']]
                : null,
            'ai_summary'     => $classification['internal_summary'] ?? null,
        ]);

        // Save intake submission
        $submission = IntakeSubmission::create([
            'tenant_id'         => $tenant->id,
            'client_id'         => $client?->id,
            'ticket_id'         => $ticket->id,
            'name'              => $data['name'],
            'email'             => $data['email'],
            'company'           => $data['company'] ?? null,
            'website_url'       => $data['website_url'] ?? null,
            'issue_type'        => $data['issue_type'],
            'description'       => $data['description'],
            'priority'          => $classification['priority'] ?? 'medium',
            'ai_classification' => $classification,
            'ai_summary'        => $classification['internal_summary'] ?? null,
            'ai_client_message' => $classification['client_message'] ?? null,
            'status'            => 'complete',
        ]);

        // Update ticket with internal AI notes
        if (!empty($classification['internal_summary'])) {
            $ticket->update(['internal_notes' => $classification['internal_summary']]);
        }

        // Notify agency team (agency admins)
        $agencyAdmins = User::where('tenant_id', $tenant->id)
            ->whereIn('type', ['agency_admin', 'agency_user'])
            ->get();

        foreach ($agencyAdmins as $admin) {
            try {
                $admin->notify(new NewTicketNotification($ticket));
            } catch (\Throwable) {}
        }

        // Notify submitter (acknowledgement)
        $submitter = (object) ['name' => $data['name'], 'email' => $data['email']];
        try {
            \Illuminate\Support\Facades\Notification::route('mail', $data['email'])
                ->notify(new TicketAcknowledgementNotification($ticket, $classification['client_message'] ?? ''));
        } catch (\Throwable) {}

        return view('intake.confirmation', compact('tenant', 'ticket', 'submission', 'classification'));
    }
}
