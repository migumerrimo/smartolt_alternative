<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OltController;
use App\Http\Controllers\VlanController;
use App\Http\Controllers\DbaProfileController;
use App\Http\Controllers\LineProfileController;
use App\Http\Controllers\ServiceProfileController;
use App\Http\Controllers\OnuController;
use App\Http\Controllers\TrafficTableController;
use App\Http\Controllers\ServicePortController;
use App\Http\Controllers\TelemetryController;
use App\Http\Controllers\AlarmController;
use App\Http\Controllers\ChangeHistoryController;
use App\Http\Controllers\DeviceConfigController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Estas rutas estarán disponibles bajo el prefijo /api
| Ejemplo: GET /api/users
|--------------------------------------------------------------------------
*/

// Users
Route::apiResource('users', UserController::class);

// OLTs
Route::apiResource('olts', OltController::class);

// VLANs
Route::apiResource('vlans', VlanController::class);

// DBA Profiles
Route::apiResource('dba-profiles', DbaProfileController::class);

// Line Profiles
Route::apiResource('line-profiles', LineProfileController::class);

// Service Profiles
Route::apiResource('service-profiles', ServiceProfileController::class);

// ONUs
Route::apiResource('onus', OnuController::class);

// Traffic Table
Route::apiResource('traffic-table', TrafficTableController::class);

// Service Ports
Route::apiResource('service-ports', ServicePortController::class);

// Telemetry
Route::apiResource('telemetry', TelemetryController::class);

// Alarms
Route::apiResource('alarms', AlarmController::class);

// Change History
Route::apiResource('change-history', ChangeHistoryController::class);

// Device Configs
Route::apiResource('device-configs', DeviceConfigController::class);
