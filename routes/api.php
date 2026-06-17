<?php

use App\Http\Controllers\BencanaController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\FcmController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| KitaTanggap – API Routes
|--------------------------------------------------------------------------
| Semua route di sini mendapat prefix /api/ secara otomatis.
| Endpoint publik tidak memerlukan middleware auth.
*/

// ─── Bencana (publik) ─────────────────────────────────────────────────────
Route::prefix('bencana')->group(function () {
    Route::get('/peta',  [BencanaController::class, 'petaApi']);  // GET /api/bencana/peta
    Route::get('/',      [BencanaController::class, 'index']);     // GET /api/bencana
    Route::get('/{id}',  [BencanaController::class, 'show']);      // GET /api/bencana/{id}
});

// ─── Admin (Protected) ───────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin/bencana')->group(function () {
    Route::post('/', [BencanaController::class, 'store']); // POST /api/admin/bencana
});

// ─── FCM (Protected) ─────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->prefix('fcm')->group(function () {
    Route::post('/register', [FcmController::class, 'register']); // POST /api/fcm/register
});

// ─── Donasi – Midtrans Integration (REQ-20) ──────────────────────────────
Route::prefix('donasi')->group(function () {
    // Buat order Midtrans Snap — publik (donatur bisa anonim)
    Route::post('/create-order', [DonationController::class, 'createOrder']);   // POST /api/donasi/create-order

    // Webhook notifikasi dari Midtrans — dipanggil server Midtrans
    Route::post('/notification', [DonationController::class, 'handleNotification']); // POST /api/donasi/notification
    Route::post('/webhook', [DonationController::class, 'handleNotification']);      // POST /api/donasi/webhook (alias)

    // Ringkasan donasi per bencana
    Route::get('/{bencana_id}/summary', [DonationController::class, 'summary']); // GET /api/donasi/{bencana_id}/summary

    // Cek status donasi & simulasi sukses (untuk development/testing)
    Route::get('/check-status/{kode_transaksi}', [DonationController::class, 'checkStatus']);
    Route::post('/simulate-success/{kode_transaksi}', [DonationController::class, 'simulateSuccess']);
});
