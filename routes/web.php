<?php

use App\Http\Controllers\Admin\ClaimController as AdminClaimController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ItemController as AdminItemController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ItemTransactionController;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return view('welcome');
});

// Public catalog: anyone can browse/search, logged in or not,
// same as the search bar on the front page.
Route::get('/items', [ItemController::class, 'index'])->name('items.index');
Route::get('/items/{report}', [ItemController::class, 'show'])->name('items.show');

Route::get('/dashboard', function () {
    if (request()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    return view('dashboard', [
        'claims' => request()->user()->claims()->with('report')->latest()->take(5)->get(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated user features
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard buttons: "I lost an item" and "I found an item"
    Route::get('/report/{type}', [ReportController::class, 'create'])
        ->whereIn('type', ['lost', 'found'])
        ->name('reports.create');
    Route::post('/report/{type}', [ReportController::class, 'store'])
        ->whereIn('type', ['lost', 'found'])
        ->name('reports.store');

            // User notifications
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');

    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.readAll');

    // User claims
    Route::get('/claims', [ClaimController::class, 'index'])->name('claims.index');
    Route::get('/items/{report}/claim', [ClaimController::class, 'create'])->name('claims.create');
    Route::post('/items/{report}/claim', [ClaimController::class, 'store'])->name('claims.store');

    // Administrator features
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {

        Route::get('/transactions', [ItemTransactionController::class, 'index'])
    ->name('transactions.index');

Route::post('/transactions/{report}/drop-off', [ItemTransactionController::class, 'recordDropOff'])
    ->name('transactions.dropoff');

Route::post('/transactions/{claim}/pickup', [ItemTransactionController::class, 'confirmPickup'])
    ->name('transactions.pickup');

        Route::get('/', AdminDashboardController::class)->name('dashboard');

        // Admin item management
        Route::get('/items', [AdminItemController::class, 'index'])->name('items.index');
        Route::get('/items/{report}/edit', [AdminItemController::class, 'edit'])->name('items.edit');
        Route::patch('/items/{report}', [AdminItemController::class, 'update'])->name('items.update');

        // Admin claim review and decisions (Increment 12)
        Route::get('/claims', [AdminClaimController::class, 'index'])->name('claims.index');
        Route::get('/claims/{claim}', [AdminClaimController::class, 'show'])->name('claims.show');
        Route::post('/claims/{claim}/approve', [AdminClaimController::class, 'approve'])->name('claims.approve');
        Route::post('/claims/{claim}/reject', [AdminClaimController::class, 'reject'])->name('claims.reject');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';