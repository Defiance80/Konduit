<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Agency;
use App\Http\Controllers\Client;
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
    });

// Client Portal
Route::middleware(['auth', \App\Http\Middleware\EnsureClientUser::class])
    ->prefix('client')
    ->name('client.')
    ->group(function () {
        Route::get('/dashboard', [Client\DashboardController::class, 'index'])->name('dashboard');
    });
