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

use App\Http\Controllers\Api\DataController;
use App\Http\Controllers\Api\OltSshApiController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Estas rutas estarán disponibles bajo el prefijo /api
| Ejemplo: GET /api/users
|--------------------------------------------------------------------------
*/
Route::get('/olt/{id}/ssh/alarms', [OltSshApiController::class, 'alarms']);
Route::get('/olt/{id}/ssh/onus', [OltSshApiController::class, 'onus']);
Route::get('/olt/{id}/ssh/system', [OltSshApiController::class, 'system']);


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

Route::get('/data/olts', [DataController::class, 'getOlts']);
Route::prefix('data')->group(function () {
    Route::get('/olts', [DataController::class, 'getOlts']);
    Route::get('/onus', [DataController::class, 'getOnus']);
    Route::get('/vlans', [DataController::class, 'getVlans']);
    Route::get('/users', [DataController::class, 'getUsers']);
});
use App\Http\Controllers\OltTelnetApiController;

Route::prefix('olt/telnet')->group(function () {
    Route::get('/system/status', [OltTelnetApiController::class, 'systemStatus']);
    Route::get('/onus/list', [OltTelnetApiController::class, 'listOnus']);
    Route::post('/onus/add', [OltTelnetApiController::class, 'addOnu']);
    Route::get('/alarms/active', [OltTelnetApiController::class, 'getAlarms']);
    Route::post('/vlan/create', [OltTelnetApiController::class, 'createVlan']);
    Route::post('/command', [OltTelnetApiController::class, 'runCommand']);
});
use App\Http\Controllers\OltSshVlanController;
use App\Http\Controllers\OltSshServiceProfileController;

Route::prefix('olt/ssh/vlan')->group(function () {
    Route::get('/{oltId}/list', [OltSshVlanController::class, 'listFromOlt']);
    Route::post('/{oltId}/create', [OltSshVlanController::class, 'createOnOlt']);
});

Route::prefix('olt/ssh/service-profile')->group(function () {
    Route::get('/{oltId}/list', [OltSshServiceProfileController::class, 'listFromOlt']);
    Route::post('/{oltId}/create', [OltSshServiceProfileController::class, 'createOnOlt']);
});
