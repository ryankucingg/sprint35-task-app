<?php

use App\Http\Controllers\Auth\LoginController;
use App\Livewire\Admin\Dashboard\DashboardPage;
use App\Livewire\Admin\Profile\ProfilePage;
use App\Livewire\Admin\Setting\SettingPage;
use App\Livewire\Admin\User\UserPage;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->intended(route('admin.dashboard'))
        : redirect()->route('login');
})->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', DashboardPage::class)->name('dashboard');
    Route::get('/profile', ProfilePage::class)->name('profile');

    Route::middleware('super-admin')->group(function () {
        Route::get('/users', UserPage::class)->name('users');
        Route::get('/settings', SettingPage::class)->name('settings');
    });
});
