<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Sop;
use App\Models\SopCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SopController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $categories = SopCategory::where('tenant_id', Auth::user()->tenant_id)
            ->with(['sops' => fn ($q) => $q->orderBy('title')])
            ->get();

        $uncategorized = Sop::where('tenant_id', Auth::user()->tenant_id)
            ->whereNull('sop_category_id')
            ->orderBy('title')
            ->get();

        return view('agency.sops.index', compact('categories', 'uncategorized'));
    }

    public function create(): \Illuminate\View\View
    {
        $categories = SopCategory::where('tenant_id', Auth::user()->tenant_id)->get();
        return view('agency.sops.create', compact('categories'));
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'content'         => 'required|string',
            'sop_category_id' => 'nullable|exists:sop_categories,id',
            'status'          => 'required|in:draft,published',
            'version'         => 'nullable|string|max:20',
        ]);

        Sop::create(array_merge($data, [
            'tenant_id'  => Auth::user()->tenant_id,
            'created_by' => Auth::id(),
        ]));

        return redirect()->route('agency.sops.index')->with('success', 'SOP created.');
    }

    public function show(Sop $sop): \Illuminate\View\View
    {
        return view('agency.sops.show', compact('sop'));
    }

    public function edit(Sop $sop): \Illuminate\View\View
    {
        $categories = SopCategory::where('tenant_id', Auth::user()->tenant_id)->get();
        return view('agency.sops.edit', compact('sop', 'categories'));
    }

    public function update(Request $request, Sop $sop): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'content'         => 'required|string',
            'sop_category_id' => 'nullable|exists:sop_categories,id',
            'status'          => 'required|in:draft,published,archived',
            'version'         => 'nullable|string|max:20',
        ]);

        $sop->update($data);
        return redirect()->route('agency.sops.show', $sop)->with('success', 'SOP updated.');
    }

    public function destroy(Sop $sop): \Illuminate\Http\RedirectResponse
    {
        $sop->delete();
        return redirect()->route('agency.sops.index')->with('success', 'SOP deleted.');
    }

    public function storeCategory(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate(['name' => 'required|string|max:100', 'color' => 'nullable|string|max:7']);
        SopCategory::create([
            'tenant_id' => Auth::user()->tenant_id,
            'name'      => $request->name,
            'color'     => $request->color ?? '#6366f1',
        ]);
        return back()->with('success', 'Category created.');
    }
}
