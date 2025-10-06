<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
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
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RegisterRequestController;

// =============================================
// RUTAS PÚBLICAS (Acceso sin autenticación)
// =============================================

// Ruta de login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Solicitudes de registro (públicas)
Route::post('/register-request', [RegisterRequestController::class, 'store']);

// =============================================
// RUTAS PROTEGIDAS (Requieren autenticación)
// =============================================
Route::middleware(['auth'])->group(function () {
    
    // Ruta principal (dashboard) - protegida
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Logout (solo para usuarios autenticados)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Rutas resource para todos los recursos (protegidas)
    Route::resource('users', UserController::class);
    Route::resource('olts', OltController::class);
    Route::resource('vlans', VlanController::class);
    Route::resource('dba-profiles', DbaProfileController::class);
    Route::resource('line-profiles', LineProfileController::class);
    Route::resource('service-profiles', ServiceProfileController::class);
    Route::resource('onus', OnuController::class);
    Route::resource('traffic-tables', TrafficTableController::class);
    Route::resource('service-ports', ServicePortController::class);
    Route::resource('telemetry', TelemetryController::class);
    Route::resource('alarms', AlarmController::class);
    Route::resource('change-history', ChangeHistoryController::class);
    Route::resource('device-configs', DeviceConfigController::class);

    // =============================================
    // ADMIN: GESTIÓN DE SOLICITUDES DE REGISTRO
    // =============================================
    Route::prefix('admin')->name('admin.')->group(function () {
        // Gestión de solicitudes de registro
        Route::get('/register-requests', [RegisterRequestController::class, 'index'])
            ->name('register-requests.index');
        Route::post('/register-requests/{id}/approve', [RegisterRequestController::class, 'approve'])
            ->name('register-requests.approve');
        Route::post('/register-requests/{id}/reject', [RegisterRequestController::class, 'reject'])
            ->name('register-requests.reject');
    });

});

// =============================================
// RUTAS DE REDIRECCIÓN POR ROL (Opcional)
// =============================================
Route::middleware(['auth'])->group(function () {
    // Estas rutas las puedes implementar después según necesites
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard', ['user' => auth()->user()]);
    })->name('admin.dashboard');
    
    Route::get('/technician/dashboard', function () {
        return view('technician.dashboard', ['user' => auth()->user()]);
    })->name('technician.dashboard');
    
    // ... más rutas específicas por rol si las necesitas
});