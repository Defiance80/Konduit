<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Services\NewsBriefService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NewsBriefController extends Controller
{
    public function index(NewsBriefService $service): View
    {
        $tenantId = Auth::user()->tenant_id;
        $brief    = $service->getBrief($tenantId);

        return view('agency.news.index', compact('brief'));
    }

    public function refresh(NewsBriefService $service): JsonResponse
    {
        $tenantId = Auth::user()->tenant_id;
        $brief    = $service->refresh($tenantId);

        return response()->json($brief);
    }
}
