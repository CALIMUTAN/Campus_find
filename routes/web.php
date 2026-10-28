
<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Models\Report;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    $items = $user->reports()
        ->latest()
        ->get();

    return view('dashboard', [
        'items' => $items,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// Dashboard buttons: "I lost an item" and "I found an item"
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/report/{type}', [ReportController::class, 'create'])
        ->whereIn('type', ['lost', 'found'])
        ->name('reports.create');

    Route::post('/report/{type}', [ReportController::class, 'store'])
        ->whereIn('type', ['lost', 'found'])
        ->name('reports.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
