<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Message;
use App\Models\MessageThread;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $threads = MessageThread::with(['client', 'messages' => fn ($q) => $q->latest()->limit(1)])
            ->whereJsonContains('participant_ids', (string) $userId)
            ->orWhere('type', 'internal')
            ->latest('last_message_at')
            ->get();

        $unreadCount = 0; // Simplified for now

        $clients = Client::orderBy('name')->get();
        $members = User::where('tenant_id', auth()->user()->tenant_id)
            ->where('user_type', 'agency_user')
            ->get();

        return view('agency.messages.index', compact('threads', 'unreadCount', 'clients', 'members'));
    }

    public function show(MessageThread $thread)
    {
        $thread->load(['client', 'messages.user']);

        $messages = $thread->messages()->with('user')->latest()->get()->reverse()->values();

        $members = User::where('tenant_id', auth()->user()->tenant_id)
            ->where('user_type', 'agency_user')
            ->get();

        return view('agency.messages.show', compact('thread', 'messages', 'members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject'         => 'required|string|max:255',
            'type'            => 'required|in:internal,client',
            'client_id'       => 'nullable|exists:clients,id',
            'participant_ids' => 'nullable|array',
            'body'            => 'required|string',
        ]);

        $participants = $validated['participant_ids'] ?? [];
        if (!in_array((string) auth()->id(), array_map('strval', $participants))) {
            $participants[] = (string) auth()->id();
        }

        $thread = MessageThread::create([
            'subject'         => $validated['subject'],
            'type'            => $validated['type'],
            'client_id'       => $validated['client_id'] ?? null,
            'participant_ids' => array_values(array_unique($participants)),
            'last_message_at' => now(),
        ]);

        Message::create([
            'thread_id'   => $thread->id,
            'user_id'     => auth()->id(),
            'body'        => $validated['body'],
            'is_internal' => $validated['type'] === 'internal',
        ]);

        return redirect()->route('agency.messages.show', $thread)->with('success', 'Thread started.');
    }

    public function reply(Request $request, MessageThread $thread)
    {
        $request->validate(['body' => 'required|string']);

        Message::create([
            'thread_id'   => $thread->id,
            'user_id'     => auth()->id(),
            'body'        => $request->body,
            'is_internal' => $thread->type === 'internal',
        ]);

        $thread->update(['last_message_at' => now()]);

        return back()->with('success', 'Reply sent.');
    }
}
