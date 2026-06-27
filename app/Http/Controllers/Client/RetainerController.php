<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Retainer;
use Illuminate\View\View;

class RetainerController extends Controller
{
    public function index(): View
    {
        $client    = auth()->user()->client;
        $retainers = Retainer::where('client_id', $client->id)
            ->with(['projects'])
            ->orderByRaw("FIELD(status,'active','paused','completed','cancelled')")
            ->get();

        return view('client.retainer.index', compact('retainers'));
    }
}
