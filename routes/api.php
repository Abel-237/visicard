<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Routes publiques
Route::get('/events', [App\Http\Controllers\Api\EventController::class, 'index']);
Route::get('/events/featured', [App\Http\Controllers\Api\EventController::class, 'featured']);
Route::get('/events/{slug}', [App\Http\Controllers\Api\EventController::class, 'show']);
Route::get('/events/by-category/{category}', [App\Http\Controllers\Api\EventController::class, 'byCategory']);
Route::get('/events/by-tag/{tag}', [App\Http\Controllers\Api\EventController::class, 'byTag']);
Route::get('/categories', [App\Http\Controllers\Api\CategoryController::class, 'index']);
Route::get('/tags', [App\Http\Controllers\Api\TagController::class, 'index']);
Route::get('/search', [App\Http\Controllers\Api\SearchController::class, 'index']);
Route::get('/stats', [App\Http\Controllers\Api\StatsController::class, 'index']);

// Routes authentifiées
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/events', [App\Http\Controllers\Api\EventController::class, 'store']);
    Route::put('/events/{slug}', [App\Http\Controllers\Api\EventController::class, 'update']);
    Route::delete('/events/{slug}', [App\Http\Controllers\Api\EventController::class, 'destroy']);
    Route::post('/events/{slug}/comment', [App\Http\Controllers\Api\EventController::class, 'addComment']);
    Route::post('/events/{slug}/like', [App\Http\Controllers\Api\EventController::class, 'toggleLike']);
    
    // Routes pour les notifications
    Route::get('/notifications', [App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::get('/notifications/unread', [App\Http\Controllers\Api\NotificationController::class, 'unread']);
    Route::post('/notifications/{id}/mark-as-read', [App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-as-read', [App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
    
    // Routes pour le user connecté
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Routes admin
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard/stats', [App\Http\Controllers\Api\Admin\DashboardController::class, 'stats']);
    Route::get('/users', [App\Http\Controllers\Api\Admin\UserController::class, 'index']);
    Route::put('/users/{id}', [App\Http\Controllers\Api\Admin\UserController::class, 'update']);
    Route::delete('/users/{id}', [App\Http\Controllers\Api\Admin\UserController::class, 'destroy']);
    
    // Routes pour les rapports
    Route::get('/reports/events-by-period', [App\Http\Controllers\Api\Admin\ReportController::class, 'eventsByPeriod']);
    Route::get('/reports/participation', [App\Http\Controllers\Api\Admin\ReportController::class, 'participation']);
    Route::get('/reports/trends', [App\Http\Controllers\Api\Admin\ReportController::class, 'trends']);
});
