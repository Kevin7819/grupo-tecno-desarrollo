<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AuthenticatedSessionController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\HostController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\SiteContentController;
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

        Route::get('/contenido', [SiteContentController::class, 'edit'])->name('content.edit');
        Route::put('/contenido', [SiteContentController::class, 'update'])->name('content.update');

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

        Route::get('/galeria', [GalleryController::class, 'index'])->name('gallery.index');
        Route::get('/galeria/nuevo', [GalleryController::class, 'create'])->name('gallery.create');
        Route::post('/galeria', [GalleryController::class, 'store'])->name('gallery.store');
        Route::get('/galeria/{image}/editar', [GalleryController::class, 'edit'])->name('gallery.edit');
        Route::put('/galeria/{image}', [GalleryController::class, 'update'])->name('gallery.update');
        Route::get('/galeria/{image}/eliminar', [GalleryController::class, 'confirmDelete'])->name('gallery.delete');
        Route::delete('/galeria/{image}', [GalleryController::class, 'destroy'])->name('gallery.destroy');

        Route::get('/contacto', [ContactController::class, 'edit'])->name('contact.edit');
        Route::put('/contacto', [ContactController::class, 'update'])->name('contact.update');
        Route::get('/contacto/redes/nuevo', [ContactController::class, 'createLink'])->name('contact.links.create');
        Route::post('/contacto/redes', [ContactController::class, 'storeLink'])->name('contact.links.store');
        Route::get('/contacto/redes/{link}/editar', [ContactController::class, 'editLink'])->name('contact.links.edit');
        Route::put('/contacto/redes/{link}', [ContactController::class, 'updateLink'])->name('contact.links.update');
        Route::get('/contacto/redes/{link}/eliminar', [ContactController::class, 'confirmDeleteLink'])->name('contact.links.delete');
        Route::delete('/contacto/redes/{link}', [ContactController::class, 'destroyLink'])->name('contact.links.destroy');

        Route::get('/cuentas', [AdminUserController::class, 'index'])->name('accounts.index');
        Route::get('/cuentas/nueva', [AdminUserController::class, 'create'])->name('accounts.create');
        Route::post('/cuentas', [AdminUserController::class, 'store'])->name('accounts.store');
        Route::get('/cuentas/{account}/editar', [AdminUserController::class, 'edit'])->name('accounts.edit');
        Route::put('/cuentas/{account}', [AdminUserController::class, 'update'])->name('accounts.update');
        Route::get('/cuentas/{account}/eliminar', [AdminUserController::class, 'confirmDelete'])->name('accounts.delete');
        Route::delete('/cuentas/{account}', [AdminUserController::class, 'destroy'])->name('accounts.destroy');
    });
});
