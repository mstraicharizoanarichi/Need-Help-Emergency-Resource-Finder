<?php

use App\Http\Controllers\Admin\EmergencyAnalyticsController;
use App\Http\Controllers\Admin\EmergencyCommandCenterController;
use App\Http\Controllers\EmergencyIncidentPublicController;
use App\Http\Controllers\EmergencyIntakeController;
use App\Http\Controllers\Provider\ProviderDispatchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Emergency Routes
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
| Provider Dispatch Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active', 'role:provider,admin'])
    ->prefix('provider')
    ->name('provider.')
    ->group(function () {
        Route::get('/dispatches', [ProviderDispatchController::class, 'index'])->name('dispatches.index');
        Route::post('/dispatches/{dispatch}/respond', [ProviderDispatchController::class, 'respond'])->name('dispatches.respond');
        Route::post('/dispatches/{dispatch}/status', [ProviderDispatchController::class, 'updateStatus'])->name('dispatches.status');
    });

/*
|--------------------------------------------------------------------------
| Admin Emergency Management Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/emergency-command', [EmergencyCommandCenterController::class, 'index'])->name('emergency.index');
        Route::get('/emergency-command/feed', [EmergencyCommandCenterController::class, 'feed'])->name('emergency.feed');
        Route::get('/emergency-command/{incident}/recommendations', [EmergencyCommandCenterController::class, 'recommendations'])->name('emergency.recommendations');
        Route::post('/emergency-command/{incident}/dispatch', [EmergencyCommandCenterController::class, 'dispatch'])->name('emergency.dispatch');
        Route::post('/emergency-command/{incident}/status', [EmergencyCommandCenterController::class, 'updateStatus'])->name('emergency.status');
        Route::post('/emergency-command/{incident}/mci', [EmergencyCommandCenterController::class, 'declareMci'])->name('emergency.mci');

        Route::get('/emergency-analytics', [EmergencyAnalyticsController::class, 'index'])->name('emergency.analytics');
        Route::get('/emergency-analytics/heatmap-data', [EmergencyAnalyticsController::class, 'heatmapData'])->name('emergency.analytics.heatmap');
    });
