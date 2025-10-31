<?php

use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\HoldController;
use Illuminate\Support\Facades\Route;

Route::get('slots/availability', [AvailabilityController::class, 'getAvailability']);
Route::post('slots/{id}/hold', [HoldController::class, 'create'])->middleware('idempotent');
Route::post('holds/{id}/confirm', [HoldController::class, 'confirm']);
Route::delete('holds/{id}', [HoldController::class, 'cancel']);
