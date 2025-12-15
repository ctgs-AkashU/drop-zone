<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\Admin\BackgroundImageController;
use App\Http\Controllers\UserUploadController;

// User Routes
Route::get('/', [UserUploadController::class, 'index'])->name('home');
Route::post('upload', [UserUploadController::class, 'store'])->name('upload.store');
Route::post('/upload/complete', [UserUploadController::class, 'complete'])->name('upload.complete');
Route::get('/upload/{session}/success', [UserUploadController::class, 'success'])->name('upload.success');

// Admin Routes
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified']);

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('uploads', UploadController::class)->only(['index', 'show', 'destroy']);
    Route::get('files/{file}/download', [UploadController::class, 'downloadFile'])->name('files.download');
    Route::delete('files/{file}', [UploadController::class, 'destroyFile'])->name('files.destroy');
    Route::get('uploads/{upload}/download', [UploadController::class, 'download'])->name('uploads.download');
    
    Route::resource('backgrounds', BackgroundImageController::class)->only(['index', 'store', 'destroy']);
});

require __DIR__.'/auth.php';
