<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ServerController;

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

  Route::get('user/server/list', [ServerController::class, 'index'])->name('admin.servers.list');

/// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('user/register', [UserController::class, 'register'])->name('user.register');
    Route::post('user/login', [UserController::class, 'login'])->name('user.login');

    Route::post('admin/login', [AdminController::class, 'login'])->name('admin.login');
});

// User API Routes
Route::prefix('user')->middleware(['auth:api_users'])->group(function () {
    Route::get('profile/{id}', [UserController::class, 'show'])->name('user.profile');
    Route::post('update/{id}', [UserController::class, 'update'])->name('user.update');
    Route::post('change-password', [UserController::class, 'changePassword'])->name('user.changePassword');
    // Route::get('server/list', [ServerController::class, 'index'])->name('admin.servers.list');
    Route::post('logout', [UserController::class, 'logout'])->name('user.logout');
});

// Admin API Routes
Route::prefix('admin')->middleware(['auth:api_admin'])->group(function () {
    Route::post('change-password', [AdminController::class, 'changePassword'])->name('admin.changePassword');
    Route::post('update/{id}', [AdminController::class, 'update'])->name('admin.update');
    Route::post('user/register', [UserController::class, 'register'])->name('user.register');
    Route::get('user-list', [UserController::class, 'index'])->name('admin.userList');
    Route::get('user-profile/{id}', [UserController::class, 'show'])->name('admin.userProfile');
    Route::get('profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::post('user-update/{id}', [UserController::class, 'update'])->name('admin.userUpdate');
    Route::post('user-delete/{id}', [UserController::class, 'destroy'])->name('admin.userDelete');
    Route::post('logout', [AdminController::class, 'logout'])->name('admin.logout');

    // Server Routes Group
    Route::prefix('servers')->group(function () {
        Route::post('add', [ServerController::class, 'store'])->name('admin.servers.add');
        Route::post('update/{id}', [ServerController::class, 'update'])->name('admin.servers.update');
        Route::post('delete/{id}', [ServerController::class, 'destroy'])->name('admin.servers.delete');
        Route::get('list', [ServerController::class, 'index'])->name('admin.servers.list');
        Route::get('show/{id}', [ServerController::class, 'show'])->name('admin.servers.show');
    });
});
