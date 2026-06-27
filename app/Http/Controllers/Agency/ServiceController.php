<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $categories = ServiceCategory::with(['services' => fn ($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        $uncategorised = Service::whereNull('category_id')->orderBy('sort_order')->get();

        $stats = [
            'total'    => Service::count(),
            'active'   => Service::where('status', 'active')->count(),
            'draft'    => Service::where('status', 'draft')->count(),
        ];

        return view('agency.services.index', compact('categories', 'uncategorised', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string',
            'what_you_get'    => 'nullable|string',
            'category_id'     => 'nullable|exists:service_categories,id',
            'price'           => 'nullable|numeric|min:0',
            'price_type'      => 'required|in:fixed,hourly,monthly,custom',
            'estimated_hours' => 'nullable|numeric|min:0',
            'status'          => 'required|in:active,draft,archived',
            'features'        => 'nullable|string',
        ]);

        if (!empty($validated['features'])) {
            $validated['features'] = array_filter(array_map('trim', explode("\n", $validated['features'])));
        }

        Service::create($validated);

        return back()->with('success', 'Service added to library.');
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string',
            'what_you_get'    => 'nullable|string',
            'category_id'     => 'nullable|exists:service_categories,id',
            'price'           => 'nullable|numeric|min:0',
            'price_type'      => 'required|in:fixed,hourly,monthly,custom',
            'estimated_hours' => 'nullable|numeric|min:0',
            'status'          => 'required|in:active,draft,archived',
            'features'        => 'nullable|string',
        ]);

        if (isset($validated['features'])) {
            $validated['features'] = array_filter(array_map('trim', explode("\n", $validated['features'])));
        }

        $service->update($validated);

        return back()->with('success', 'Service updated.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return back()->with('success', 'Service removed.');
    }

    public function storeCategory(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100', 'color' => 'nullable|string']);
        ServiceCategory::create(['name' => $request->name, 'color' => $request->color ?? '#6366f1']);
        return back()->with('success', 'Category created.');
    }
}
