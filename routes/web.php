<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BencanaController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\DonorDashboardController;
use App\Http\Controllers\AdminLaporanController;
use App\Http\Controllers\RelawanController;
use App\Http\Controllers\PenugasanController;
use App\Http\Controllers\SertifikatController;
use App\Http\Controllers\TransparencyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationPreferenceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| KitaTanggap – Web Routes (Increment 1)
|--------------------------------------------------------------------------
*/

// ─── Halaman Utama & Publik ────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Peta Interaktif Bencana — publik, tanpa login (REQ-08, REQ-10)
Route::get('/peta', [BencanaController::class, 'peta'])->name('peta');

// Verifikasi Sertifikat Publik (REQ-18)
Route::get('/verifikasi/{kode}', [SertifikatController::class, 'verifikasi'])->name('sertifikat.verifikasi');
Route::get('/sertifikat/{kode}/unduh', [SertifikatController::class, 'unduh'])->name('sertifikat.unduh');

// Halaman Donasi Publik (REQ-19)
Route::get('/donasi/{bencana_id}', [DonationController::class, 'show'])->name('donasi.show');

// Transparansi Donasi Publik (REQ-22)
Route::get('/transparansi', [TransparencyController::class, 'index'])->name('transparansi');

// ─── Storage File Serve (bypass symlink) ──────────────────────────────────
// Serve file dari storage/app/public/ tanpa butuh 'php artisan storage:link'
Route::get('/storage-file/{path}', function (string $path) {
    // Decode URL-encoded path (untuk sub-folder)
    $path = urldecode($path);

    // Keamanan: pastikan tidak ada path traversal
    if (str_contains($path, '..') || str_starts_with($path, '/')) {
        abort(403, 'Path tidak valid.');
    }

    $disk = \Illuminate\Support\Facades\Storage::disk('public');

    if (!$disk->exists($path)) {
        abort(404, 'File tidak ditemukan.');
    }

    $mimeType = $disk->mimeType($path);
    $content  = $disk->get($path);

    return response($content, 200, [
        'Content-Type'        => $mimeType,
        'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        'Cache-Control'       => 'public, max-age=86400',
    ]);
})->where('path', '.*')->name('storage.serve');

// ─── Autentikasi (REQ-01 s/d REQ-06) ────────────────────────────────────
Route::middleware('guest')->group(function () {
    // Registrasi (REQ-01, REQ-02, REQ-03)
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Login (REQ-04)
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Reset Password (REQ-05) — menggunakan controller bawaan Laravel
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::post('/forgot-password', function (\Illuminate\Http\Request $request) {
        $request->validate(['email' => 'required|email']);
        $status = \Illuminate\Support\Facades\Password::sendResetLink($request->only('email'));
        return $status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT
            ? back()->with('success', 'Link reset password telah dikirim ke email Anda.')
            : back()->withErrors(['email' => __($status)]);
    })->name('password.email');

    Route::get('/reset-password/{token}', function (string $token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');

    Route::post('/reset-password', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|regex:/^(?=.*[a-zA-Z])(?=.*[0-9]).+$/|confirmed',
        ], [
            'password.regex' => 'Password harus mengandung kombinasi huruf dan angka.',
        ]);
        $status = \Illuminate\Support\Facades\Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => $password])->save();
                $user->tokens()->delete();
            }
        );
        return $status === \Illuminate\Support\Facades\Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Password berhasil diubah. Silakan masuk.')
            : back()->withErrors(['email' => __($status)]);
    })->name('password.update');
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ─── Verifikasi Email (REQ-03) ───────────────────────────────────────────
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('login')->with('success', 'Email berhasil diverifikasi! Silakan masuk.');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('success', 'Link verifikasi telah dikirim ulang ke email Anda.');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// ─── Dashboard (placeholder — akan diisi di sprint berikutnya) ──────────
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pengaturan Notifikasi (REQ-27)
    Route::get('/pengaturan/notifikasi', [NotificationPreferenceController::class, 'show'])->name('pengaturan.notifikasi');
    Route::patch('/pengaturan/notifikasi/update', [NotificationPreferenceController::class, 'update'])->name('pengaturan.notifikasi.update');

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('dashboard.admin');
        })->name('admin.dashboard');
    });

    Route::middleware('role:relawan')->group(function () {
        Route::get('/relawan/dashboard', function () {
            return view('dashboard.relawan');
        })->name('relawan.dashboard');
    });

    Route::middleware('role:donatur')->group(function () {
        Route::get('/donatur/dashboard', [DonorDashboardController::class, 'index'])->name('donatur.dashboard');

        Route::get('/dashboard/donatur/riwayat', [DonorDashboardController::class, 'history'])->name('donatur.riwayat');
    });

    // ─── Profil Relawan (REQ-12) ──────────────────────────────
    Route::middleware('role:relawan')->group(function () {
        Route::get('/relawan/profil', [RelawanController::class, 'profil'])->name('relawan.profil');
        Route::post('/relawan/profil', [RelawanController::class, 'store'])->name('relawan.profil.store');
        Route::put('/relawan/profil', [RelawanController::class, 'update'])->name('relawan.profil.update');
        
        // Riwayat Misi (REQ-17)
        Route::get('/relawan/riwayat', [PenugasanController::class, 'riwayatIndex'])->name('relawan.riwayat.index');
        Route::get('/api/relawan/riwayat', [PenugasanController::class, 'riwayat'])->name('relawan.riwayat.api');
    });

    // ─── Admin: Manajemen Relawan (REQ-13, REQ-14) ───────────
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/relawan', [RelawanController::class, 'adminIndex'])->name('admin.relawan.index');
        Route::get('/api/relawan', [RelawanController::class, 'index'])->name('admin.relawan.api.index');
        Route::patch('/api/relawan/{id}/verifikasi', [RelawanController::class, 'verifikasi'])->name('admin.relawan.api.verifikasi');

        // Penugasan Relawan (REQ-15, REQ-16)
        Route::get('/admin/penugasan', [PenugasanController::class, 'adminIndex'])->name('admin.penugasan.index');
        Route::get('/api/penugasan', [PenugasanController::class, 'index'])->name('admin.penugasan.api.index');
        Route::post('/api/penugasan', [PenugasanController::class, 'store'])->name('admin.penugasan.api.store');
        Route::patch('/api/penugasan/{id}/status', [PenugasanController::class, 'updateStatus'])->name('admin.penugasan.api.status');
        Route::patch('/api/penugasan/{id}/selesai', [PenugasanController::class, 'selesai'])->name('admin.penugasan.api.selesai');

        // ─── Manajemen Bencana Admin (REQ-07 s/d REQ-11) ─────────
        Route::get('/admin/bencana', [BencanaController::class, 'adminIndex'])->name('admin.bencana.index');
        Route::get('/admin/bencana/create', [BencanaController::class, 'adminCreate'])->name('admin.bencana.create');
        Route::post('/admin/bencana', [BencanaController::class, 'adminStore'])->name('admin.bencana.store');
        Route::get('/admin/bencana/{id}/edit', [BencanaController::class, 'adminEdit'])->name('admin.bencana.edit');
        Route::put('/admin/bencana/{id}', [BencanaController::class, 'adminUpdate'])->name('admin.bencana.update');
        Route::delete('/admin/bencana/{id}', [BencanaController::class, 'adminDestroy'])->name('admin.bencana.destroy');
        Route::patch('/admin/bencana/{id}/toggle', [BencanaController::class, 'adminToggle'])->name('admin.bencana.toggle');

        // Laporan Penggunaan Dana & Distribusi Bantuan (REQ-24)
        Route::resource('/admin/laporan-distribusi', AdminLaporanController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
            ->names('admin.laporan-distribusi');
    });
});

