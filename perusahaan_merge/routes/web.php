<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Models\Karyawan;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\LemburController;
use App\Http\Controllers\GajiController;

Route::get('/', function () {
    $latestKaryawan = Karyawan::with(['jabatan', 'rating'])
        ->orderByDesc('created_at')
        ->limit(10)
        ->get();

    return view('welcome', compact('latestKaryawan'));
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';

// Kantor modules (migrated from legacy PHP)
Route::resource('karyawan', KaryawanController::class);
Route::resource('jabatan', JabatanController::class);
Route::resource('rating', RatingController::class);
Route::resource('lembur', LemburController::class);
Route::resource('gaji', GajiController::class);
