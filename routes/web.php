<?php

use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminResourceController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\EmergencyAnalyticsController;
use App\Http\Controllers\Admin\EmergencyCommandCenterController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BloodDonorPublicController;
use App\Http\Controllers\DoctorPublicController;
use App\Http\Controllers\Emergency\CommunityResponderController;
use App\Http\Controllers\EmergencyAssistantController;
use App\Http\Controllers\EmergencyIncidentPublicController;
use App\Http\Controllers\EmergencyIntakeController;
use App\Http\Controllers\EmergencySearchController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Provider\ProviderAvailabilityController;
use App\Http\Controllers\Provider\ProviderDashboardController;
use App\Http\Controllers\Provider\ProviderDispatchController;
use App\Http\Controllers\Provider\ProviderDoctorController;
use App\Http\Controllers\Provider\ProviderReportController;
use App\Http\Controllers\Provider\ProviderResourceController;
use App\Http\Controllers\ReportPublicController;
use App\Http\Controllers\ResourcePublicController;
use App\Http\Controllers\User\UserDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Emergency SOS, AI Intake & Live Incident Tracking (Bangladesh Life-Safety)
|--------------------------------------------------------------------------
*/
Route::post('/emergency/sos', [EmergencyIncidentPublicController::class, 'storeSos'])->name('emergency.sos');
Route::get('/emergency/report', [EmergencyIntakeController::class, 'create'])->name('emergency.report');
Route::post('/emergency/report', [EmergencyIntakeController::class, 'store'])->name('emergency.report.store');
Route::post('/emergency/analyze-preview', [EmergencyIntakeController::class, 'previewTriage'])->name('emergency.preview-triage');
Route::get('/emergency/{reference}', [EmergencyIncidentPublicController::class, 'show'])->name('emergency.show');
Route::get('/emergency/{reference}/status', [EmergencyIncidentPublicController::class, 'status'])->name('emergency.status');
Route::post('/emergency/{reference}/cancel', [EmergencyIncidentPublicController::class, 'cancel'])->name('emergency.cancel');

/*
|--------------------------------------------------------------------------
| Emergency Assistant (Conversational AI Triage & Guidance)
|--------------------------------------------------------------------------
*/
Route::prefix('emergency-assistant')->name('emergency-assistant.')->group(function () {
    Route::get('/', [EmergencyAssistantController::class, 'index'])->name('index');
    Route::middleware('throttle:60,1')->group(function () {
        Route::post('/conversations', [EmergencyAssistantController::class, 'initConversation'])->name('conversations.init');
        Route::get('/conversations/{conversation}', [EmergencyAssistantController::class, 'getConversation'])->name('conversations.show');
        Route::post('/message', [EmergencyAssistantController::class, 'sendMessage'])->name('message');
        Route::post('/{conversation}/sos', [EmergencyAssistantController::class, 'triggerSos'])->name('sos');
        Route::post('/{conversation}/language', [EmergencyAssistantController::class, 'switchLanguage'])->name('language');
        Route::get('/resources/nearby', [EmergencyAssistantController::class, 'getNearbyResources'])->name('resources.nearby');
    });
});


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [EmergencySearchController::class, 'index'])->name('search');
Route::get('/search/map-data', [EmergencySearchController::class, 'mapData'])->name('search.map-data');
Route::get('/resources/{slug}', [ResourcePublicController::class, 'show'])->name('resources.show');

// Doctor & Specialist Directory & Profile
Route::get('/doctors', [DoctorPublicController::class, 'index'])->name('doctors.index');
Route::get('/doctors/{slug}', [DoctorPublicController::class, 'show'])->name('doctors.show');

// Blood Donors Directory & Request
Route::get('/blood-donors', [BloodDonorPublicController::class, 'index'])->name('blood-donors.index');
Route::get('/blood-donors/register', [BloodDonorPublicController::class, 'create'])->name('blood-donors.create');
Route::post('/blood-donors/register', [BloodDonorPublicController::class, 'store'])->name('blood-donors.store');
Route::post('/blood-donors/request', [BloodDonorPublicController::class, 'requestContact'])->name('blood-donors.request');

// User Reporting
Route::post('/reports', [ReportPublicController::class, 'store'])->name('reports.store');

// Community First Responders (CFR) & Volunteer Mobilization
Route::get('/community-responders/register', [CommunityResponderController::class, 'create'])->name('community.responder.register');
Route::post('/community-responders/register', [CommunityResponderController::class, 'store'])->name('community.responder.store');
Route::get('/community-responders/{id}/registered', [CommunityResponderController::class, 'registered'])->name('community.responder.registered');
Route::post('/community-responders/{responder}/toggle-availability', [CommunityResponderController::class, 'toggleAvailability'])->name('community.responder.toggle-availability');
Route::post('/community-responders/alerts/{alert}/respond', [CommunityResponderController::class, 'respondToAlert'])->name('community.responder.alerts.respond');
Route::post('/community-responders/alerts/{alert}/arrived', [CommunityResponderController::class, 'markArrived'])->name('community.responder.alerts.arrived');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::post('/profile', [UserDashboardController::class, 'updateProfile'])->name('user.profile.update');
    Route::post('/favorites/{resource}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
});

/*
|--------------------------------------------------------------------------
| Provider Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'role:provider,admin'])
    ->prefix('provider')
    ->name('provider.')
    ->group(function () {
        Route::get('/dashboard', [ProviderDashboardController::class, 'index'])->name('dashboard');
        Route::resource('resources', ProviderResourceController::class);
        Route::post('/resources/{resource}/availability', [ProviderAvailabilityController::class, 'toggle'])->name('resources.availability');
        Route::resource('doctors', ProviderDoctorController::class);
        Route::post('/doctors/{doctor}/emergency', [ProviderDoctorController::class, 'toggleEmergency'])->name('doctors.emergency');
        Route::get('/reports', [ProviderReportController::class, 'index'])->name('reports.index');
        Route::get('/dispatches', [ProviderDispatchController::class, 'index'])->name('dispatches.index');
        Route::post('/dispatches/{dispatch}/respond', [ProviderDispatchController::class, 'respond'])->name('dispatches.respond');
        Route::post('/dispatches/{dispatch}/status', [ProviderDispatchController::class, 'updateStatus'])->name('dispatches.status');
    });

/*
|--------------------------------------------------------------------------
| Administrator Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Emergency Operations Command Center (EOC)
        Route::get('/emergency-command', [EmergencyCommandCenterController::class, 'index'])->name('emergency.index');
        Route::get('/emergency-command/feed', [EmergencyCommandCenterController::class, 'feed'])->name('emergency.feed');
        Route::get('/emergency-command/{incident}/recommendations', [EmergencyCommandCenterController::class, 'recommendations'])->name('emergency.recommendations');
        Route::post('/emergency-command/{incident}/dispatch', [EmergencyCommandCenterController::class, 'dispatch'])->name('emergency.dispatch');
        Route::post('/emergency-command/{incident}/status', [EmergencyCommandCenterController::class, 'updateStatus'])->name('emergency.status');
        Route::post('/emergency-command/{incident}/mci', [EmergencyCommandCenterController::class, 'declareMci'])->name('emergency.mci');

        // Emergency Response Analytics, Heatmaps & Audit
        Route::get('/emergency-analytics', [EmergencyAnalyticsController::class, 'index'])->name('emergency.analytics');
        Route::get('/emergency-analytics/heatmap-data', [EmergencyAnalyticsController::class, 'heatmapData'])->name('emergency.analytics.heatmap');

        // Resource Verification & Moderation
        Route::get('/resources/pending', [AdminResourceController::class, 'pending'])->name('resources.pending');
        Route::patch('/resources/{resource}/verify', [AdminResourceController::class, 'verify'])->name('resources.verify');
        Route::patch('/resources/{resource}/reject', [AdminResourceController::class, 'reject'])->name('resources.reject');
        Route::resource('resources', AdminResourceController::class);

        // User & Provider Management
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/status', [AdminUserController::class, 'toggleStatus'])->name('users.status');

        // Reports Moderation
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::patch('/reports/{report}/resolve', [AdminReportController::class, 'resolve'])->name('reports.resolve');

        // Category Management
        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    });
