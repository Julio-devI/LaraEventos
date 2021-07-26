<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [EventController::class, 'index'])->name('welcome');
Route::get('/events/create', [EventController::class, 'create'])->middleware('auth')->name('event-create');
Route::get('/events/{id}', [EventController::class, 'show'])->name('event-show');
Route::post('/events', [EventController::class, 'store'])->name('event-store');
Route::get('/dashboard', [EventController::class, 'dashboard'])->middleware('auth')->name('dashboard');
Route::delete('/events/{id}', [EventController::class, 'destroy'])->middleware('auth')->name('event-destroy');
Route::get('/events/edit/{id}', [EventController::class, 'edit'])->middleware('auth')->name('event-edit');
Route::put('/events/update/{id}', [EventController::class, 'update'])->middleware('auth')->name('event-update');
Route::post('/events/join/{id}', [EventController::class, 'joinEvent'])->middleware('auth')->name('event-join');