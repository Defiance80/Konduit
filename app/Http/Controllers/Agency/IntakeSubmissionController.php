<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\IntakeSubmission;
use Illuminate\Support\Facades\Auth;

class IntakeSubmissionController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $submissions = IntakeSubmission::where('tenant_id', Auth::user()->tenant_id)
            ->with(['client', 'ticket'])
            ->latest()
            ->paginate(25);

        $tenant = Auth::user()->tenant;
        $intakeUrl = route('intake.show', $tenant);

        return view('agency.intake.index', compact('submissions', 'intakeUrl'));
    }
}
