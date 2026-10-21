<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('catalogo', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('catalogo/{product:slug}', [CatalogController::class, 'show'])->name('catalog.show');

Route::post('catalogo/{product:slug}/resenas', [ReviewController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('reviews.store');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard legacy: su vista se conserva en resources/js/pages/legacy/Dashboard.vue.
    // Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::get('dashboard', function (Request $request) {
        return redirect()->route(
            $request->user()?->isAdmin() ? 'admin.products.index' : 'home'
        );
    })->name('dashboard');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/', '/admin/productos')->name('dashboard');

    Route::get('storage-link', function () {
        Artisan::call('storage:link', ['--force' => true]);

        return response()->json([
            'output' => Artisan::output(),
        ]);
    })->name('storage.link');

    Route::get('productos', [ProductController::class, 'index'])->name('products.index');
    Route::get('productos/crear', [ProductController::class, 'create'])->name('products.create');
    Route::post('productos', [ProductController::class, 'store'])->name('products.store');
    Route::get('productos/{product}/editar', [ProductController::class, 'edit'])->name('products.edit');
    Route::patch('productos/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::patch('productos/{product}/disponibilidad', [ProductController::class, 'updateAvailability'])
        ->name('products.availability');
    Route::delete('productos/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    Route::get('marcas', [BrandController::class, 'index'])->name('brands.index');
    Route::post('marcas', [BrandController::class, 'store'])->name('brands.store');
    Route::patch('marcas/{brand}', [BrandController::class, 'update'])->name('brands.update');
    Route::patch('marcas/{brand}/estado', [BrandController::class, 'updateStatus'])->name('brands.status');
    Route::delete('marcas/{brand}', [BrandController::class, 'destroy'])->name('brands.destroy');
});

Route::get('/clear-all', function () {
    Artisan::call('storage:link');
    return 'linked';
});

require __DIR__.'/settings.php';
