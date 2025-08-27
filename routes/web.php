<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\{
    ColorController,
    MasterController,
    SizeController,
    CategoryController,
    ProductController,
    StockController,
};

Route::redirect('/', '/login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


Route::get('test', function () {
    dd(array_column(\App\Enums\Gender::cases(), 'value'));
});

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    // Masters list
    Route::get('masters', MasterController::class)->name('masters');

    // Color Master
    Route::resource('colors', ColorController::class);

    // Size Master
    Route::resource('sizes', SizeController::class);

    // Category Master
    Route::resource('categories', CategoryController::class);

    // Product Master
    Route::resource('products', ProductController::class);

    // Stock List
    Route::get('stocks/scan/{type}', [StockController::class, 'scan'])->name('stocks.scan')->whereIn('type', array_column(\App\Enums\TransactionType::cases(), 'value'));
    Route::post('stocks/scan/{type}', [StockController::class, 'storeScannedSku'])->name('stocks.scan.store')->whereIn('type', array_column(\App\Enums\TransactionType::cases(), 'value'));
    Route::resource('stocks', StockController::class)->only(['index', 'create', 'store']);
});
