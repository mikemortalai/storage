<?php

use App\Http\Controllers\Admin\AiController;
use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\WaitlistController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicSiteController;
use App\Http\Controllers\Tenant\PortalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Platform marketing (StorageSoftAI SaaS)
|--------------------------------------------------------------------------
*/
Route::prefix('app')->name('platform.')->group(function () {
    Route::get('/', [PlatformController::class, 'marketing'])->name('home');
    Route::get('/pricing', [PlatformController::class, 'pricing'])->name('pricing');
    Route::get('/templates', [PlatformController::class, 'templates'])->name('templates');
    Route::get('/signup', [PlatformController::class, 'signupForm'])->name('signup');
    Route::post('/signup', [PlatformController::class, 'signup'])->name('signup.store');
});

/*
|--------------------------------------------------------------------------
| Facility public website (282 Storage reference tenant)
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicSiteController::class, 'home'])->name('home');
Route::get('/pages/{slug}', [PublicSiteController::class, 'page'])->name('pages.show');
Route::get('/blog', [PublicSiteController::class, 'blog'])->name('blog');
Route::get('/contact', [PublicSiteController::class, 'contact'])->name('contact');
Route::post('/contact', [PublicSiteController::class, 'submitContact'])->name('contact.submit');
Route::get('/map', [PublicSiteController::class, 'map'])->name('map');
Route::get('/rent/{unitType}', [PublicSiteController::class, 'rentForm'])->name('rent.show');
Route::post('/rent/{unitType}', [PublicSiteController::class, 'startRent'])->name('rent.start');
Route::get('/waiting-list/{unitType}', [PublicSiteController::class, 'waitlistForm'])->name('waitlist.show');
Route::post('/waiting-list/{unitType}', [PublicSiteController::class, 'submitWaitlist'])->name('waitlist.submit');

/*
|--------------------------------------------------------------------------
| Auth redirects
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->isTenant()) {
        return redirect()->route('tenant.dashboard');
    }
    if ($user->isStaff()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('home');
})->middleware(['auth'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Admin / facility management
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'staff'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/units', [UnitController::class, 'index'])->name('units.index');
    Route::get('/units/grid', [UnitController::class, 'grid'])->name('units.grid');
    Route::post('/units', [UnitController::class, 'store'])->name('units.store');
    Route::patch('/units/{unit}', [UnitController::class, 'update'])->name('units.update');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::post('/customers/{customer}/rent', [CustomerController::class, 'rentUnit'])->name('customers.rent');
    Route::post('/customers/{customer}/pay', [CustomerController::class, 'collectPayment'])->name('customers.pay');

    Route::get('/waitlist', [WaitlistController::class, 'index'])->name('waitlist.index');
    Route::patch('/waitlist/{entry}', [WaitlistController::class, 'update'])->name('waitlist.update');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/occupancy', [ReportController::class, 'occupancy'])->name('reports.occupancy');
    Route::get('/reports/collections', [ReportController::class, 'collections'])->name('reports.collections');
    Route::get('/reports/rent-roll', [ReportController::class, 'rentRoll'])->name('reports.rent-roll');
    Route::get('/reports/unit-status', [ReportController::class, 'unitStatus'])->name('reports.unit-status');
    Route::get('/reports/deposits', [ReportController::class, 'deposits'])->name('reports.deposits');
    Route::get('/reports/customers.csv', [ReportController::class, 'exportCustomers'])->name('reports.customers-csv');

    Route::get('/cms', [CmsController::class, 'index'])->name('cms.index');
    Route::get('/cms/{page}/edit', [CmsController::class, 'edit'])->name('cms.edit');
    Route::patch('/cms/{page}', [CmsController::class, 'update'])->name('cms.update');

    Route::get('/ai', [AiController::class, 'index'])->name('ai.index');
    Route::post('/ai/ask', [AiController::class, 'ask'])->name('ai.ask');
    Route::post('/ai/briefing', [AiController::class, 'briefing'])->name('ai.briefing');
    Route::post('/ai/pricing', [AiController::class, 'pricing'])->name('ai.pricing');
    Route::post('/ai/delinquency', [AiController::class, 'runDelinquency'])->name('ai.delinquency');
    Route::patch('/ai/suggestions/{suggestion}', [AiController::class, 'updateSuggestion'])->name('ai.suggestion');
});

/*
|--------------------------------------------------------------------------
| Tenant portal
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'tenant'])->prefix('portal')->name('tenant.')->group(function () {
    Route::get('/', [PortalController::class, 'dashboard'])->name('dashboard');
    Route::post('/pay', [PortalController::class, 'pay'])->name('pay');
    Route::get('/profile', [PortalController::class, 'profile'])->name('profile');
    Route::patch('/profile', [PortalController::class, 'updateProfile'])->name('profile.update');
    Route::post('/move-out', [PortalController::class, 'scheduleMoveOut'])->name('move-out');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
