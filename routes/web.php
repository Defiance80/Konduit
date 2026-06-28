<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Agency;
use App\Http\Controllers\Client;
use App\Http\Controllers\Client\DeliverableController as ClientDeliverableController;
use Illuminate\Support\Facades\Route;

// Landing page (guests) or redirect to dashboard (authenticated)
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        return $user->isClientContact()
            ? redirect()->route('client.dashboard')
            : redirect()->route('agency.dashboard');
    }
    return view('landing');
})->name('home');

// Auth
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Agency Portal
Route::middleware(['auth', \App\Http\Middleware\EnsureAgencyUser::class])
    ->prefix('/')
    ->name('agency.')
    ->group(function () {
        Route::get('/dashboard', [Agency\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('clients', Agency\ClientController::class)->names('clients');
        Route::resource('retainers', Agency\RetainerController::class)->names('retainers');
        Route::resource('projects', Agency\ProjectController::class)->names('projects');
        Route::resource('tickets', Agency\TicketController::class)->names('tickets');
        Route::post('/tickets/{ticket}/comment', [Agency\TicketController::class, 'comment'])->name('tickets.comment');

        // Team
        Route::get('/team', [Agency\TeamController::class, 'index'])->name('team.index');
        Route::post('/team/invite', [Agency\TeamController::class, 'invite'])->name('team.invite');
        Route::delete('/team/{user}', [Agency\TeamController::class, 'destroy'])->name('team.destroy');

        // Settings
        Route::get('/settings', [Agency\SettingsController::class, 'index'])->name('settings.index');
        Route::patch('/settings/agency', [Agency\SettingsController::class, 'updateAgency'])->name('settings.agency');
        Route::patch('/settings/profile', [Agency\SettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::patch('/settings/password', [Agency\SettingsController::class, 'updatePassword'])->name('settings.password');
        Route::get('/settings/integrations', [Agency\SettingsController::class, 'integrations'])->name('settings.integrations');
        Route::post('/settings/integrations/{service}', [Agency\SettingsController::class, 'saveIntegration'])->name('settings.integrations.save');
        Route::delete('/settings/integrations/{service}', [Agency\SettingsController::class, 'removeIntegration'])->name('settings.integrations.remove');

        // Deliverables
        Route::get('/deliverables', [Agency\DeliverableController::class, 'index'])->name('deliverables.index');
        Route::post('/deliverables', [Agency\DeliverableController::class, 'store'])->name('deliverables.store');
        Route::get('/deliverables/{deliverable}', [Agency\DeliverableController::class, 'show'])->name('deliverables.show');
        Route::patch('/deliverables/{deliverable}', [Agency\DeliverableController::class, 'update'])->name('deliverables.update');
        Route::delete('/deliverables/{deliverable}', [Agency\DeliverableController::class, 'destroy'])->name('deliverables.destroy');
        Route::patch('/deliverables/{deliverable}/submit', [Agency\DeliverableController::class, 'submit'])->name('deliverables.submit');
        Route::patch('/deliverables/{deliverable}/deliver', [Agency\DeliverableController::class, 'deliver'])->name('deliverables.deliver');
        Route::patch('/deliverables/{deliverable}/approve', [Agency\DeliverableController::class, 'approve'])->name('deliverables.approve');

        // Tasks (static routes before parameterized ones)
        Route::get('/tasks', [Agency\TaskController::class, 'index'])->name('tasks.index');
        Route::post('/tasks', [Agency\TaskController::class, 'store'])->name('tasks.store');
        Route::post('/tasks/reorder', [Agency\TaskController::class, 'reorder'])->name('tasks.reorder');
        Route::get('/tasks/{task}', [Agency\TaskController::class, 'show'])->name('tasks.show');
        Route::patch('/tasks/{task}', [Agency\TaskController::class, 'update'])->name('tasks.update');
        Route::delete('/tasks/{task}', [Agency\TaskController::class, 'destroy'])->name('tasks.destroy');
        Route::patch('/tasks/{task}/status', [Agency\TaskController::class, 'updateStatus'])->name('tasks.status');
        Route::get('/projects/{project}/board', [Agency\TaskController::class, 'board'])->name('projects.board');

        // Service Library
        Route::get('/services', [Agency\ServiceController::class, 'index'])->name('services.index');
        Route::post('/services', [Agency\ServiceController::class, 'store'])->name('services.store');
        Route::patch('/services/{service}', [Agency\ServiceController::class, 'update'])->name('services.update');
        Route::delete('/services/{service}', [Agency\ServiceController::class, 'destroy'])->name('services.destroy');
        Route::post('/service-categories', [Agency\ServiceController::class, 'storeCategory'])->name('service-categories.store');

        // Capacity Engine
        Route::get('/capacity', [Agency\CapacityController::class, 'index'])->name('capacity.index');

        // Invoices (Financial Intelligence)
        Route::get('/invoices', [Agency\InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/create', [Agency\InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices', [Agency\InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/invoices/{invoice}', [Agency\InvoiceController::class, 'show'])->name('invoices.show');
        Route::delete('/invoices/{invoice}', [Agency\InvoiceController::class, 'destroy'])->name('invoices.destroy');
        Route::patch('/invoices/{invoice}/sent', [Agency\InvoiceController::class, 'markSent'])->name('invoices.sent');
        Route::patch('/invoices/{invoice}/paid', [Agency\InvoiceController::class, 'markPaid'])->name('invoices.paid');
        Route::patch('/invoices/{invoice}/void', [Agency\InvoiceController::class, 'void'])->name('invoices.void');

        // Audit Engine
        Route::get('/audits', [Agency\AuditController::class, 'index'])->name('audits.index');
        Route::get('/audits/create', [Agency\AuditController::class, 'create'])->name('audits.create');
        Route::post('/audits', [Agency\AuditController::class, 'store'])->name('audits.store');
        Route::get('/audits/{audit}', [Agency\AuditController::class, 'show'])->name('audits.show');
        Route::patch('/audits/{audit}', [Agency\AuditController::class, 'update'])->name('audits.update');
        Route::delete('/audits/{audit}', [Agency\AuditController::class, 'destroy'])->name('audits.destroy');
        Route::post('/audits/{audit}/findings', [Agency\AuditController::class, 'addFinding'])->name('audits.findings.store');
        Route::post('/audits/{audit}/recommendations', [Agency\AuditController::class, 'addRecommendation'])->name('audits.recommendations.store');
        Route::patch('/audits/{audit}/share', [Agency\AuditController::class, 'share'])->name('audits.share');
        Route::get('/audits/{audit}/scan', [Agency\AuditController::class, 'runScan'])->name('audits.scan');

        // Messages
        Route::get('/messages', [Agency\MessageController::class, 'index'])->name('messages.index');
        Route::post('/messages', [Agency\MessageController::class, 'store'])->name('messages.store');
        Route::get('/messages/{thread}', [Agency\MessageController::class, 'show'])->name('messages.show');
        Route::post('/messages/{thread}/reply', [Agency\MessageController::class, 'reply'])->name('messages.reply');

        // AI Reports
        Route::get('/reports', [Agency\ReportController::class, 'index'])->name('reports.index');

        // AI Summaries
        Route::post('/projects/{project}/ai-summary', [Agency\AiSummaryController::class, 'generateProject'])->name('projects.ai-summary');
        Route::post('/clients/{client}/ai-summary', [Agency\AiSummaryController::class, 'generateClient'])->name('clients.ai-summary');

        // Client documents
        Route::post('/clients/{client}/documents', [Agency\ClientDocumentController::class, 'store'])->name('clients.documents.store');
        Route::get('/clients/{client}/documents/{document}/download', [Agency\ClientDocumentController::class, 'download'])->name('clients.documents.download');
        Route::delete('/clients/{client}/documents/{document}', [Agency\ClientDocumentController::class, 'destroy'])->name('clients.documents.destroy');
        Route::patch('/ai-summaries/{summary}/toggle-visible', [Agency\AiSummaryController::class, 'toggleClientVisible'])->name('ai-summaries.toggle-visible');
        Route::delete('/ai-summaries/{summary}', [Agency\AiSummaryController::class, 'destroy'])->name('ai-summaries.destroy');

        // Service Requests (agency side)
        Route::get('/service-requests', [Agency\ServiceRequestController::class, 'index'])->name('service-requests.index');
        Route::patch('/service-requests/{serviceRequest}', [Agency\ServiceRequestController::class, 'update'])->name('service-requests.update');

        // ── Phase 3: Executive Intelligence ──────────────────────────────────
        Route::get('/executive', [Agency\ExecutiveDashboardController::class, 'index'])->name('executive.index');
        Route::post('/executive/brief', [Agency\ExecutiveDashboardController::class, 'generateBrief'])->name('executive.brief');

        // Project Blueprints / Templates
        Route::get('/project-templates', [Agency\ProjectTemplateController::class, 'index'])->name('project-templates.index');
        Route::get('/project-templates/create', [Agency\ProjectTemplateController::class, 'create'])->name('project-templates.create');
        Route::post('/project-templates', [Agency\ProjectTemplateController::class, 'store'])->name('project-templates.store');
        Route::get('/project-templates/{projectTemplate}', [Agency\ProjectTemplateController::class, 'show'])->name('project-templates.show');
        Route::delete('/project-templates/{projectTemplate}', [Agency\ProjectTemplateController::class, 'destroy'])->name('project-templates.destroy');
        Route::post('/project-templates/{projectTemplate}/apply', [Agency\ProjectTemplateController::class, 'apply'])->name('project-templates.apply');

        // Intake submissions (agency view)
        Route::get('/intake-submissions', [Agency\IntakeSubmissionController::class, 'index'])->name('intake-submissions.index');

        // Relationship Intelligence
        Route::get('/relationship', [Agency\RelationshipController::class, 'index'])->name('relationship.index');
        Route::post('/relationship/{client}/recalculate', [Agency\RelationshipController::class, 'recalculate'])->name('relationship.recalculate');

        // Resource Forecasting + Agency Simulator
        Route::get('/forecast', [Agency\ForecastController::class, 'index'])->name('forecast.index');
        Route::get('/forecast/simulator', [Agency\ForecastController::class, 'simulator'])->name('forecast.simulator');
        Route::post('/forecast/simulator', [Agency\ForecastController::class, 'simulate'])->name('forecast.simulate');

        // SOP Library
        Route::get('/sops', [Agency\SopController::class, 'index'])->name('sops.index');
        Route::get('/sops/create', [Agency\SopController::class, 'create'])->name('sops.create');
        Route::post('/sops', [Agency\SopController::class, 'store'])->name('sops.store');
        Route::get('/sops/{sop}', [Agency\SopController::class, 'show'])->name('sops.show');
        Route::get('/sops/{sop}/edit', [Agency\SopController::class, 'edit'])->name('sops.edit');
        Route::put('/sops/{sop}', [Agency\SopController::class, 'update'])->name('sops.update');
        Route::delete('/sops/{sop}', [Agency\SopController::class, 'destroy'])->name('sops.destroy');
        Route::post('/sop-categories', [Agency\SopController::class, 'storeCategory'])->name('sop-categories.store');

        // Knowledge Base
        Route::get('/knowledge-base', [Agency\KnowledgeBaseController::class, 'index'])->name('knowledge-base.index');
        Route::get('/knowledge-base/create', [Agency\KnowledgeBaseController::class, 'create'])->name('knowledge-base.create');
        Route::post('/knowledge-base', [Agency\KnowledgeBaseController::class, 'store'])->name('knowledge-base.store');
        Route::get('/knowledge-base/{knowledgeBase}', [Agency\KnowledgeBaseController::class, 'show'])->name('knowledge-base.show');
        Route::get('/knowledge-base/{knowledgeBase}/edit', [Agency\KnowledgeBaseController::class, 'edit'])->name('knowledge-base.edit');
        Route::put('/knowledge-base/{knowledgeBase}', [Agency\KnowledgeBaseController::class, 'update'])->name('knowledge-base.update');
        Route::delete('/knowledge-base/{knowledgeBase}', [Agency\KnowledgeBaseController::class, 'destroy'])->name('knowledge-base.destroy');

        // Marketing Intelligence News Feed
        Route::get('/news', [Agency\NewsBriefController::class, 'index'])->name('news.index');
        Route::post('/news/refresh', [Agency\NewsBriefController::class, 'refresh'])->name('news.refresh');

        // Training Academy — static routes BEFORE parameterized ones
        Route::get('/training', [Agency\TrainingController::class, 'index'])->name('training.index');
        Route::get('/training/create-curriculum', [Agency\TrainingController::class, 'createCurriculum'])->name('training.create-curriculum');
        Route::post('/training/curricula', [Agency\TrainingController::class, 'storeCurriculum'])->name('training.curricula.store');
        Route::get('/training/create', [Agency\TrainingController::class, 'createCourse'])->name('training.create');
        Route::post('/training', [Agency\TrainingController::class, 'storeCourse'])->name('training.store');
        Route::get('/training/{trainingCourse}', [Agency\TrainingController::class, 'show'])->name('training.show');
        Route::delete('/training/{trainingCourse}', [Agency\TrainingController::class, 'destroyCourse'])->name('training.destroy');
        Route::post('/training/{trainingCourse}/assign', [Agency\TrainingController::class, 'storeAssignment'])->name('training.assign');
        Route::delete('/training/{trainingCourse}/assign/{user}', [Agency\TrainingController::class, 'removeAssignment'])->name('training.assign.remove');
        Route::get('/training/{trainingCourse}/lessons/create', [Agency\TrainingController::class, 'createLesson'])->name('training.lessons.create');
        Route::post('/training/{trainingCourse}/lessons', [Agency\TrainingController::class, 'storeLesson'])->name('training.lessons.store');
        Route::get('/training/{trainingCourse}/lessons/{trainingLesson}', [Agency\TrainingController::class, 'lesson'])->name('training.lesson');
        Route::post('/training/{trainingCourse}/lessons/{trainingLesson}/complete', [Agency\TrainingController::class, 'complete'])->name('training.complete');
    });

// ── Public Intake Widget (no auth) ───────────────────────────────────────────
Route::get('/intake/{tenant}', [\App\Http\Controllers\IntakeController::class, 'show'])->name('intake.show');
Route::post('/intake/{tenant}', [\App\Http\Controllers\IntakeController::class, 'store'])->name('intake.store');

// Client Portal
Route::middleware(['auth', \App\Http\Middleware\EnsureClientUser::class])
    ->prefix('client')
    ->name('client.')
    ->group(function () {
        Route::get('/dashboard', [Client\DashboardController::class, 'index'])->name('dashboard');

        // Deliverables / Approvals
        Route::get('/approvals', [ClientDeliverableController::class, 'index'])->name('deliverables.index');
        Route::get('/approvals/{deliverable}', [ClientDeliverableController::class, 'show'])->name('deliverables.show');
        Route::patch('/approvals/{deliverable}/approve', [ClientDeliverableController::class, 'approve'])->name('deliverables.approve');
        Route::patch('/approvals/{deliverable}/reject', [ClientDeliverableController::class, 'reject'])->name('deliverables.reject');

        // Projects
        Route::get('/projects', [Client\ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/{project}', [Client\ProjectController::class, 'show'])->name('projects.show');

        // Support Tickets
        Route::get('/support', [Client\TicketController::class, 'index'])->name('tickets.index');
        Route::get('/support/create', [Client\TicketController::class, 'create'])->name('tickets.create');
        Route::post('/support', [Client\TicketController::class, 'store'])->name('tickets.store');
        Route::get('/support/{ticket}', [Client\TicketController::class, 'show'])->name('tickets.show');
        Route::post('/support/{ticket}/comment', [Client\TicketController::class, 'comment'])->name('tickets.comment');

        // Retainer
        Route::get('/retainer', [Client\RetainerController::class, 'index'])->name('retainer.index');

        // Reports (AI Summaries visible to client)
        Route::get('/reports', [Client\ReportController::class, 'index'])->name('reports.index');

        // Marketplace (Service Library for clients)
        Route::get('/marketplace', [Client\MarketplaceController::class, 'index'])->name('marketplace.index');
        Route::post('/marketplace/request', [Client\MarketplaceController::class, 'requestService'])->name('marketplace.request');
    });
