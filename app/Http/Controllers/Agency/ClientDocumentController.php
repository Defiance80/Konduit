<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientDocumentController extends Controller
{
    public function store(Request $request, Client $client): RedirectResponse
    {
        $request->validate([
            'file'          => 'required|file|max:20480',
            'document_type' => 'required|in:contract,legal,policy,proposal,other',
            'notes'         => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        $path = $file->store("client-docs/{$client->id}");

        ClientDocument::create([
            'tenant_id'     => Auth::user()->tenant_id,
            'client_id'     => $client->id,
            'uploaded_by'   => Auth::id(),
            'name'          => $file->getClientOriginalName(),
            'file_path'     => $path,
            'document_type' => $request->document_type,
            'notes'         => $request->notes,
            'file_size'     => $file->getSize(),
            'mime_type'     => $file->getMimeType(),
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function download(Client $client, ClientDocument $document): StreamedResponse
    {
        abort_if($document->client_id !== $client->id, 404);
        return Storage::download($document->file_path, $document->name);
    }

    public function destroy(Client $client, ClientDocument $document): RedirectResponse
    {
        abort_if($document->client_id !== $client->id, 404);
        Storage::delete($document->file_path);
        $document->delete();
        return back()->with('success', 'Document deleted.');
    }
}
