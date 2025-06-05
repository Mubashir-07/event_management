<?php

use App\Http\Controllers\Event\EventController;
use App\Http\Controllers\Event\EventTypeController;
use App\Http\Controllers\Event\GalleryController;
use App\Http\Controllers\Event\InvitationController;
use App\Http\Controllers\Event\RequisitionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');


Route::prefix('event')
    ->name('event.')
    ->controller(EventController::class)
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{event}', 'show')->name('show');
        Route::get('/{event}/edit', 'edit')->name('edit');
        Route::put('/{event}', 'update')->name('update');
        Route::delete('/{event}', 'destroy')->name('destroy');
    });

Route::prefix('event-type')
    ->name('event-type.')
    ->controller(EventTypeController::class)
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{eventType}', 'show')->name('show');
        Route::get('/{eventType}/edit', 'edit')->name('edit');
        Route::put('/{eventType}', 'update')->name('update');
        Route::delete('/{eventType}', 'destroy')->name('destroy');
    });


Route::prefix('invitation')
    ->name('invitation.')
    ->controller(InvitationController::class)
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/', 'index')->name('index');
    });

Route::prefix('requisition')
    ->name('requisition.')
    ->controller(RequisitionController::class)
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
    });

Route::prefix('gallery')
    ->name('gallery.')
    ->controller(GalleryController::class)
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/{event}', 'index')->name('index');
        Route::get('/{event}/create', 'create')->name('create');
    });


require __DIR__.'/auth.php';
