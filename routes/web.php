<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BlogController::class, 'index'])->name('blog.index');
Route::get('/post/{post}', [BlogController::class, 'show'])->name('blog.show');

/* -------------------- Admin panel -------------------- */
Route::prefix('admin')->group(function () {
    Route::get('login', [AdminController::class, 'showLogin'])->name('admin.login');
    Route::post('login', [AdminController::class, 'login'])->name('admin.login.post');

    Route::middleware('admin')->group(function () {
        Route::post('logout', [AdminController::class, 'logout'])->name('admin.logout');
        Route::get('/', [AdminController::class, 'index'])->name('admin.posts');
        Route::post('generate', [AdminController::class, 'generate'])->name('admin.generate');
        Route::get('posts/{post}', [AdminController::class, 'show'])->name('admin.posts.show');
        Route::patch('posts/{post}/status', [AdminController::class, 'updateStatus'])->name('admin.posts.status');
        Route::patch('posts/{post}/featured', [AdminController::class, 'toggleFeatured'])->name('admin.posts.featured');
        Route::delete('posts/{post}', [AdminController::class, 'destroy'])->name('admin.posts.destroy');
    });
});
