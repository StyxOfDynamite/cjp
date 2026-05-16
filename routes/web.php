<?php

use App\Http\Controllers\Admin\MotorcycleController as AdminMotorcycleController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicMotorcycleController;
use App\Models\Motorcycle;
use Illuminate\Support\Facades\Route;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

// Public
Route::get('/', [PublicMotorcycleController::class, 'home'])->name('home');
Route::get('/motorcycles', [PublicMotorcycleController::class, 'index'])->name('motorcycles.index');
Route::get('/motorcycles/{motorcycle:slug}', [PublicMotorcycleController::class, 'show'])->name('motorcycles.show');
Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/sitemap.xml', function () {
    $sitemap = Sitemap::create()
        ->add(Url::create('/')->setPriority(1.0)->setChangeFrequency('daily'))
        ->add(Url::create('/motorcycles')->setPriority(0.9)->setChangeFrequency('daily'))
        ->add(Url::create('/about')->setPriority(0.6)->setChangeFrequency('monthly'))
        ->add(Url::create('/contact')->setPriority(0.6)->setChangeFrequency('monthly'));

    Motorcycle::all()->each(function (Motorcycle $m) use ($sitemap) {
        $sitemap->add(
            Url::create(route('motorcycles.show', $m))
                ->setLastModificationDate($m->updated_at)
                ->setPriority(0.8)
                ->setChangeFrequency('weekly')
        );
    });

    return response($sitemap->render(), 200, ['Content-Type' => 'application/xml']);
})->name('sitemap');

// Auth profile routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('admin.motorcycles.index'))->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/motorcycles', [AdminMotorcycleController::class, 'index'])->name('motorcycles.index');
    Route::get('/motorcycles/create', [AdminMotorcycleController::class, 'create'])->name('motorcycles.create');
    Route::post('/motorcycles', [AdminMotorcycleController::class, 'store'])->name('motorcycles.store');
    Route::get('/motorcycles/{motorcycle}/edit', [AdminMotorcycleController::class, 'edit'])->name('motorcycles.edit');
    Route::put('/motorcycles/{motorcycle}', [AdminMotorcycleController::class, 'update'])->name('motorcycles.update');
    Route::delete('/motorcycles/{motorcycle}', [AdminMotorcycleController::class, 'destroy'])->name('motorcycles.destroy');

    Route::delete('/images/{image}', [AdminMotorcycleController::class, 'destroyImage'])->name('images.destroy');
    Route::patch('/images/{image}/primary', [AdminMotorcycleController::class, 'setPrimaryImage'])->name('images.primary');
    Route::delete('/motorcycles/{motorcycle}/video', [AdminMotorcycleController::class, 'destroyVideo'])->name('motorcycles.video.destroy');

    Route::patch('/motorcycles/{motorcycle}/stock', [StockController::class, 'update'])->name('motorcycles.stock');
});

require __DIR__.'/auth.php';
