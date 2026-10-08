<?php
use App\Http\Controllers\Api\V1\AppointmentController;
use Illuminate\Support\Facades\Route;
Route::prefix('v1')->middleware('auth:sanctum')->group(function(){ Route::apiResource('appointments',AppointmentController::class)->only(['index','store','show']); });
