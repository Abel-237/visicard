<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BusinessCardController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\LanguageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Routes pour le changement de langue
Route::get('/language/{locale}', [LanguageController::class, 'switchLanguage'])->name('language.switch');

// Application du middleware de langue à toutes les routes
Route::middleware(['setlocale'])->group(function () {
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('welcome');
    
    Route::get('/test-images', function() {
        return view('test-images');
    })->name('test.images');

Route::get('/debug-images', function() {
    return view('debug-images');
})->name('debug.images');

Route::get('/test-notification', function() {
    return view('test-notification');
})->name('test.notification')->middleware('auth');

Auth::routes();

Route::get('/home', [App\Http\Controllers\DashboardController::class, 'index'])->name('home');

// Notifications routes
Route::prefix('notifications')->name('notifications.')->middleware('auth')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::get('/unread', [NotificationController::class, 'getUnreadNotifications'])->name('unread');
    Route::get('/count', [NotificationController::class, 'getUnreadCount'])->name('count');
    Route::post('/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('markAsRead');
    Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
    Route::post('/clear-all', [NotificationController::class, 'clearAll'])->name('clearAll');
    Route::post('/preferences', [NotificationController::class, 'updatePreferences'])->name('updatePreferences');
    Route::post('/test', [NotificationController::class, 'sendTestNotification'])->name('test');
    Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
});

// Préférences de notification utilisateur
Route::middleware('auth')->prefix('notifications/preferences')->name('notifications.preferences.')->group(function () {
    Route::get('/', [App\Http\Controllers\NotificationPreferenceController::class, 'edit'])->name('edit');
    Route::put('/', [App\Http\Controllers\NotificationPreferenceController::class, 'update'])->name('update');
});

// Events routes
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/search', [EventController::class, 'search'])->name('events.search');
Route::get('/events/create', [EventController::class, 'create'])->name('events.create')->middleware('auth');
Route::post('/events', [EventController::class, 'store'])->name('events.store')->middleware('auth');
Route::get('/events/{slug}', [EventController::class, 'show'])->name('events.show');
Route::get('/events/{slug}/edit', [EventController::class, 'edit'])->name('events.edit')->middleware('auth');
Route::put('/events/{slug}', [EventController::class, 'update'])->name('events.update')->middleware('auth');
Route::delete('/events/{slug}', [EventController::class, 'destroy'])->name('events.destroy')->middleware('auth');
Route::post('/events/{slug}/comment', [EventController::class, 'addComment'])->name('events.comment')->middleware('auth');
Route::post('/events/{slug}/like', [App\Http\Controllers\EventController::class, 'toggleLike'])->name('events.toggleLike')->middleware('auth');
Route::get('/events/{slug}/pdf', [EventController::class, 'exportPdf'])->name('events.pdf');

// Admin routes
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Users management
    Route::get('/users', [App\Http\Controllers\Admin\DashboardController::class, 'users'])->name('users');
    Route::get('/users/{id}/edit', [App\Http\Controllers\Admin\DashboardController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{id}', [App\Http\Controllers\Admin\DashboardController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [App\Http\Controllers\Admin\DashboardController::class, 'deleteUser'])->name('users.delete');
    
    // Comments moderation
    Route::get('/comments', [App\Http\Controllers\Admin\DashboardController::class, 'comments'])->name('comments');
    Route::post('/comments/{id}/approve', [App\Http\Controllers\Admin\DashboardController::class, 'approveComment'])->name('comments.approve');
    Route::delete('/comments/{id}', [App\Http\Controllers\Admin\DashboardController::class, 'deleteComment'])->name('comments.delete');
    
    // Reports routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [App\Http\Controllers\ReportController::class, 'index'])->name('index');
        Route::get('/events-by-period', [App\Http\Controllers\ReportController::class, 'eventsByPeriod'])->name('events-by-period');
        Route::get('/participation', [App\Http\Controllers\ReportController::class, 'participation'])->name('participation');
        Route::get('/trends', [App\Http\Controllers\ReportController::class, 'trends'])->name('trends');
    });

    // Gestion des événements
    Route::resource('events', App\Http\Controllers\Admin\EventController::class)->except(['show']);
    // Gestion des catégories
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class)->except(['show']);

    // Routes pour l'administration des événements
    Route::get('/events/dashboard', [App\Http\Controllers\Admin\EventManagementController::class, 'dashboard'])->name('admin.events.dashboard');
    Route::post('/events/{eventId}/customize', [App\Http\Controllers\Admin\EventManagementController::class, 'customizeEvent'])->name('admin.events.customize');
    Route::get('/events/{eventId}/qr-codes', [App\Http\Controllers\Admin\EventManagementController::class, 'generateQRCodes'])->name('admin.events.qr-codes');
    Route::post('/events/{eventId}/invitations', [App\Http\Controllers\Admin\EventManagementController::class, 'manageInvitations'])->name('admin.events.invitations');
    Route::get('/events/{eventId}/stats', [App\Http\Controllers\Admin\EventManagementController::class, 'eventStats'])->name('admin.events.stats');
    Route::get('/events/{eventId}/export', [App\Http\Controllers\Admin\EventManagementController::class, 'exportData'])->name('admin.events.export');
    Route::post('/events/{eventId}/moderate', [App\Http\Controllers\Admin\EventManagementController::class, 'moderateProfiles'])->name('admin.events.moderate');
});

// Calendar routes
Route::get('/calendar', [App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');
Route::get('/calendar/download/{id}', [App\Http\Controllers\CalendarController::class, 'downloadIcs'])->name('calendar.download');
Route::get('/calendar/download-all', [App\Http\Controllers\CalendarController::class, 'downloadAllIcs'])->name('calendar.download-all');
Route::post('/calendar/reminder/{id}', [App\Http\Controllers\CalendarController::class, 'setReminder'])->name('calendar.reminder');

// Search routes
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/search/quick', [App\Http\Controllers\SearchController::class, 'quickSearch'])->name('search.quick');
Route::get('/business-card', [BusinessCardController::class, 'create'])->name('business-card');
Route::post('/business-cards', [BusinessCardController::class, 'store'])->name('business-cards.store');
Route::get('/business-cards/total', [BusinessCardController::class, 'getTotalCards'])->name('business-cards.total');

// Angular App Route - cette route servira l'application Angular
Route::get('/app/{any}', function () {
    return view('angular-app');
})->where('any', '.*')->name('angular.app');

// Profile routes
Route::prefix('profile')->name('profile.')->middleware('auth')->group(function () {
    Route::get('/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('edit');
    Route::put('/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('update');
});

// Media management (admin/editor only)
Route::middleware(['auth', 'role:admin,editor'])->prefix('media')->name('media.')->group(function () {
    Route::get('/', [App\Http\Controllers\MediaController::class, 'index'])->name('index');
    Route::get('/upload', [App\Http\Controllers\MediaController::class, 'upload'])->name('upload');
    Route::post('/store', [App\Http\Controllers\MediaController::class, 'store'])->name('store');
    Route::delete('/{id}', [App\Http\Controllers\MediaController::class, 'destroy'])->name('destroy');
});

// Route publique pour afficher une catégorie et ses événements
Route::get('/categories/{slug}', [App\Http\Controllers\CategoryController::class, 'show'])->name('categories.show');

// Routes publiques pour les cartes de visite
Route::get('/business-cards', [BusinessCardController::class, 'index'])->name('business-cards.index');

// Routes protégées pour les cartes de visite
Route::middleware(['auth'])->group(function () {
    Route::get('/business-cards/create', [BusinessCardController::class, 'create'])->name('business-cards.create');
    Route::post('/business-cards', [BusinessCardController::class, 'store'])->name('business-cards.store');
    Route::get('/business-cards/{businessCard}', [BusinessCardController::class, 'show'])->name('business-cards.show');
    Route::get('/business-cards/{businessCard}/edit', [BusinessCardController::class, 'edit'])->name('business-cards.edit');
    Route::put('/business-cards/{businessCard}', [BusinessCardController::class, 'update'])->name('business-cards.update');
    Route::delete('/business-cards/{businessCard}', [BusinessCardController::class, 'destroy'])->name('business-cards.destroy');
    
    // Routes de partage de cartes
    Route::get('/business-cards/{businessCard}/share', [App\Http\Controllers\CardShareController::class, 'showShareInterface'])->name('business-cards.share');
    Route::post('/business-cards/{businessCard}/share/email', [App\Http\Controllers\CardShareController::class, 'shareViaEmail'])->name('business-cards.share.email');
    Route::post('/business-cards/{businessCard}/share/whatsapp', [App\Http\Controllers\CardShareController::class, 'shareViaWhatsApp'])->name('business-cards.share.whatsapp');
    Route::post('/business-cards/{businessCard}/share/qr', [App\Http\Controllers\CardShareController::class, 'generateQrCode'])->name('business-cards.share.qr');
    Route::post('/business-cards/{businessCard}/share/nfc', [App\Http\Controllers\CardShareController::class, 'shareViaNfc'])->name('business-cards.share.nfc');
    Route::post('/business-cards/{businessCard}/share/nfc-export', [App\Http\Controllers\CardShareController::class, 'exportNFC'])->name('business-cards.share.nfc-export');
    Route::get('/business-cards/{businessCard}/share/stats', [App\Http\Controllers\CardShareController::class, 'getSharingStats'])->name('business-cards.share.stats');
    Route::get('/business-cards/{businessCard}/vcard', [App\Http\Controllers\BusinessCardController::class, 'vcard'])->name('business-cards.vcard');
});

// Routes publiques pour afficher les cartes partagées
Route::get('/carte/{token}', [App\Http\Controllers\CardShareController::class, 'showSharedCard'])->name('shared.card');
Route::get('/u/{username}', [App\Http\Controllers\CardShareController::class, 'showSharedCard'])->name('cards.shared.username');

// Routes pour le chat
Route::middleware(['auth', 'has.business.card'])->group(function () {
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{user}', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{user}/updates', [MessageController::class, 'getUpdates'])->name('messages.updates');
    Route::get('/messages/unread/count', [MessageController::class, 'unreadCount'])->name('messages.unread.count');
});
});
