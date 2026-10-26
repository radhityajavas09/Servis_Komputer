<?php

use App\Http\Controllers\Admin\SparepartController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Petugas\PelangganController;
use App\Http\Controllers\Petugas\PendaftaranServisController;
use App\Http\Controllers\Petugas\ServisController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root redirect: Guest -> Login, Auth -> Respective Role Dashboard
Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }

    $role = Auth::user()->role;
    $roleValue = is_object($role) && property_exists($role, 'value') ? $role->value : (string) $role;

    return match ($roleValue) {
        'admin' => redirect()->route('admin.dashboard'),
        'petugas' => redirect()->route('petugas.dashboard'),
        'teknisi' => redirect()->route('teknisi.dashboard'),
        default => redirect()->route('login'),
    };
})->name('home');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Shared Authenticated Routes (Semua Peran)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profil.update');
});

// Administrator Routes (prefix /admin, role: admin)
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::resource('user', UserController::class);
        Route::resource('sparepart', SparepartController::class);
    });

// Petugas Servis Routes (prefix /petugas, role: petugas)
Route::middleware(['auth', 'role:petugas'])
    ->prefix('petugas')
    ->as('petugas.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('petugas.dashboard');
        })->name('dashboard');

        Route::resource('pelanggan', PelangganController::class);

        // Pendaftaran Servis & Tanda Terima
        Route::get('/servis/pendaftaran', [PendaftaranServisController::class, 'create'])->name('servis.tambah');
        Route::post('/servis/pendaftaran', [PendaftaranServisController::class, 'store'])->name('servis.store');
        Route::get('/servis/{servis:no_servis}/tanda-terima', [PendaftaranServisController::class, 'tandaTerima'])->name('servis.tanda-terima');

        // Data Servis, Detail & Penyerahan Perangkat
        Route::get('/servis', [ServisController::class, 'index'])->name('servis.index');
        Route::get('/servis/{servis:no_servis}', [ServisController::class, 'detail'])->name('servis.detail');
        Route::get('/servis/{servis:no_servis}/serahkan', [ServisController::class, 'serahkan'])->name('servis.serahkan');
        Route::post('/servis/{servis:no_servis}/serahkan', [ServisController::class, 'prosesSerahkan'])->name('servis.proses-serahkan');
    });

// Teknisi Routes (prefix /teknisi, role: teknisi)
Route::middleware(['auth', 'role:teknisi'])
    ->prefix('teknisi')
    ->as('teknisi.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('teknisi.dashboard');
        })->name('dashboard');
    });

// Local / Testing UI Component Catalog
if (app()->environment('local', 'testing')) {
    Route::get('/_ui', function () {
        return view('ui-catalog');
    })->name('ui.catalog');
}
