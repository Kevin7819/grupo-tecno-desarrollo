<?php

use App\Http\Controllers\Admin\AuthenticatedSessionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HostController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\StationSettingsController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('/login', [AuthenticatedSessionController::class, 'store'])
            ->middleware('throttle:admin-login')
            ->name('login.store');
    });

    Route::middleware('admin.auth')->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

        Route::get('/emisora', [StationSettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/emisora', [StationSettingsController::class, 'update'])->name('settings.update');

        Route::get('/programacion', [ProgramController::class, 'index'])->name('programs.index');
        Route::get('/programacion/nuevo', [ProgramController::class, 'create'])->name('programs.create');
        Route::post('/programacion', [ProgramController::class, 'store'])->name('programs.store');
        Route::get('/programacion/{program}/editar', [ProgramController::class, 'edit'])->name('programs.edit');
        Route::put('/programacion/{program}', [ProgramController::class, 'update'])->name('programs.update');
        Route::patch('/programacion/{program}/estado', [ProgramController::class, 'updateStatus'])->name('programs.status');
        Route::get('/programacion/{program}/eliminar', [ProgramController::class, 'confirmDelete'])->name('programs.delete');
        Route::delete('/programacion/{program}', [ProgramController::class, 'destroy'])->name('programs.destroy');

        Route::get('/locutores', [HostController::class, 'index'])->name('hosts.index');
        Route::get('/locutores/nuevo', [HostController::class, 'create'])->name('hosts.create');
        Route::post('/locutores', [HostController::class, 'store'])->name('hosts.store');
        Route::get('/locutores/{host}/editar', [HostController::class, 'edit'])->name('hosts.edit');
        Route::put('/locutores/{host}', [HostController::class, 'update'])->name('hosts.update');
        Route::get('/locutores/{host}/eliminar', [HostController::class, 'confirmDelete'])->name('hosts.delete');
        Route::delete('/locutores/{host}', [HostController::class, 'destroy'])->name('hosts.destroy');

    });
});
