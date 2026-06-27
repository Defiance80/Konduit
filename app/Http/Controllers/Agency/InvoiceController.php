<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Project;
use App\Models\Retainer;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['client', 'project'])->latest('issued_date');

        if ($request->filled('status'))  $query->where('status', $request->status);
        if ($request->filled('client'))  $query->where('client_id', $request->client);

        // Auto-mark overdue
        Invoice::whereNotIn('status', ['paid', 'void'])
            ->where('due_date', '<', now()->toDateString())
            ->update(['status' => 'overdue']);

        $invoices = $query->paginate(20)->withQueryString();

        $stats = [
            'draft'   => Invoice::where('status', 'draft')->count(),
            'sent'    => Invoice::whereIn('status', ['sent', 'viewed'])->count(),
            'overdue' => Invoice::where('status', 'overdue')->count(),
            'paid_month' => Invoice::where('status', 'paid')
                ->whereMonth('paid_at', now()->month)->sum('total'),
        ];

        $clients = Client::orderBy('name')->get();

        return view('agency.invoices.index', compact('invoices', 'stats', 'clients'));
    }

    public function create()
    {
        $clients   = Client::orderBy('name')->get();
        $projects  = Project::orderBy('name')->get();
        $retainers = Retainer::where('status', 'active')->get();
        return view('agency.invoices.create', compact('clients', 'projects', 'retainers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'    => 'required|exists:clients,id',
            'project_id'   => 'nullable|exists:projects,id',
            'retainer_id'  => 'nullable|exists:retainers,id',
            'issued_date'  => 'required|date',
            'due_date'     => 'required|date|after_or_equal:issued_date',
            'tax_rate'     => 'nullable|numeric|min:0|max:100',
            'notes'        => 'nullable|string',
            'items'        => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        $invoice = Invoice::create([
            'client_id'   => $validated['client_id'],
            'project_id'  => $validated['project_id'] ?? null,
            'retainer_id' => $validated['retainer_id'] ?? null,
            'issued_date' => $validated['issued_date'],
            'due_date'    => $validated['due_date'],
            'tax_rate'    => $validated['tax_rate'] ?? 0,
            'notes'       => $validated['notes'] ?? null,
            'status'      => 'draft',
            'subtotal'    => 0,
            'tax_amount'  => 0,
            'total'       => 0,
        ]);

        $subtotal = 0;
        foreach ($request->items as $i => $item) {
            $amount = round($item['quantity'] * $item['unit_price'], 2);
            $subtotal += $amount;
            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'description' => $item['description'],
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['unit_price'],
                'amount'      => $amount,
                'sort_order'  => $i,
            ]);
        }

        $taxAmount = round($subtotal * (($invoice->tax_rate ?? 0) / 100), 2);
        $invoice->update(['subtotal' => $subtotal, 'tax_amount' => $taxAmount, 'total' => $subtotal + $taxAmount]);

        return redirect()->route('agency.invoices.show', $invoice)->with('success', 'Invoice created.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['client', 'project', 'retainer', 'items']);
        return view('agency.invoices.show', compact('invoice'));
    }

    public function markSent(Invoice $invoice)
    {
        $invoice->update(['status' => 'sent']);
        return back()->with('success', 'Invoice marked as sent.');
    }

    public function markPaid(Invoice $invoice)
    {
        $invoice->update(['status' => 'paid', 'paid_at' => now()]);
        return back()->with('success', 'Invoice marked as paid.');
    }

    public function void(Invoice $invoice)
    {
        $invoice->update(['status' => 'void']);
        return back()->with('success', 'Invoice voided.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('agency.invoices.index')->with('success', 'Invoice deleted.');
    }
}
