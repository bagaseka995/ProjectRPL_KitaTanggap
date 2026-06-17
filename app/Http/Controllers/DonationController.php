<?php

namespace App\Http\Controllers;

use App\Jobs\SendDonationReceiptJob;
use App\Models\Bencana;
use App\Models\Donasi;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DonationController extends Controller
{
    /* ═══════════════════════════════════════════════════════════
     │  HALAMAN PUBLIK: DONASI (REQ-19)
     ══════════════════════════════════════════════════════════ */

    /**
     * GET /donasi/{bencana_id}
     * Halaman publik: detail bencana + form donasi + progress bar.
     */
    public function show(int $bencanaId): View
    {
        $bencana = Bencana::findOrFail($bencanaId);

        // Hitung total donasi sukses untuk bencana ini
        $totalTerkumpul = Donasi::where('bencana_id', $bencanaId)
            ->where('status_bayar', 'sukses')
            ->sum('nominal');

        // Hitung persentase, clamp ke 100%
        $targetDana = (float) $bencana->target_dana;
        $persentase = $targetDana > 0
            ? min(100, round(($totalTerkumpul / $targetDana) * 100, 1))
            : 0;

        // Hitung jumlah donatur unik
        $jumlahDonatur = Donasi::where('bencana_id', $bencanaId)
            ->where('status_bayar', 'sukses')
            ->distinct('email_donatur')
            ->count('email_donatur');

        // Ambil 5 donasi terbaru yang sukses
        $donasiTerbaru = Donasi::where('bencana_id', $bencanaId)
            ->where('status_bayar', 'sukses')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Midtrans client key untuk Snap.js di frontend
        $midtransClientKey = config('midtrans.client_key');
        $midtransSnapUrl   = config('midtrans.snap_url');

        return view('publik.donasi', compact(
            'bencana',
            'totalTerkumpul',
            'targetDana',
            'persentase',
            'jumlahDonatur',
            'donasiTerbaru',
            'midtransClientKey',
            'midtransSnapUrl'
        ));
    }

    /* ═══════════════════════════════════════════════════════════
     │  API: BUAT ORDER MIDTRANS SNAP (REQ-20)
     ══════════════════════════════════════════════════════════ */

    /**
     * POST /api/donasi/create-order
     * Membuat record donasi (pending) → generate Snap Token dari Midtrans.
     * Frontend akan menerima snap_token untuk menampilkan popup Midtrans.
     */
    public function createOrder(Request $request, MidtransService $midtrans): JsonResponse
    {
        $request->validate([
            'bencana_id'        => ['required', 'exists:bencana,id'],
            'nominal'           => ['required', 'numeric', 'min:10000', 'max:999999999999'],
            'nama_donatur'      => ['required', 'string', 'max:100'],
            'email_donatur'     => ['required', 'email', 'max:100'],
            'metode_pembayaran' => ['required', 'in:transfer_bank,e_wallet,kartu_kredit'],
            'pesan'             => ['nullable', 'string', 'max:500'],
        ], [
            'bencana_id.required'        => 'Bencana wajib dipilih.',
            'bencana_id.exists'          => 'Data bencana tidak ditemukan.',
            'nominal.required'           => 'Nominal donasi wajib diisi.',
            'nominal.numeric'            => 'Nominal harus berupa angka.',
            'nominal.min'                => 'Nominal donasi minimal Rp 10.000.',
            'nominal.max'                => 'Nominal donasi terlalu besar.',
            'nama_donatur.required'      => 'Nama donatur wajib diisi.',
            'email_donatur.required'     => 'Email donatur wajib diisi.',
            'email_donatur.email'        => 'Format email tidak valid.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'metode_pembayaran.in'       => 'Metode pembayaran tidak valid.',
            'pesan.max'                  => 'Pesan maksimal 500 karakter.',
        ]);

        $bencana = Bencana::findOrFail($request->bencana_id);

        // Validasi: bencana harus masih aktif
        if (!$bencana->status_aktif) {
            return response()->json([
                'message' => 'Donasi untuk bencana ini telah ditutup.',
            ], 422);
        }

        // Generate kode transaksi unik (UUID)
        $kodeTransaksi = 'KT-' . strtoupper(Str::uuid()->toString());

        // Simpan record donasi ke DB dengan status pending
        $donasi = Donasi::create([
            'bencana_id'         => $request->bencana_id,
            'user_id'            => auth()->id(), // null jika anonim
            'kode_transaksi'     => $kodeTransaksi,
            'nama_donatur'       => $request->nama_donatur,
            'email_donatur'      => $request->email_donatur,
            'nominal'            => $request->nominal,
            'pesan'              => $request->pesan,
            'metode_pembayaran'  => $request->metode_pembayaran,
            'status_bayar'       => 'pending',
        ]);

        // Load relasi bencana untuk payload Midtrans
        $donasi->load('bencana');

        // Request Snap Token ke Midtrans API
        try {
            $snapToken = $midtrans->createSnapToken($donasi);

            // Simpan snap_token ke database
            $donasi->update(['snap_token' => $snapToken]);

            return response()->json([
                'status'          => 'success',
                'snap_token'      => $snapToken,
                'kode_transaksi'  => $kodeTransaksi,
                'donasi_id'       => $donasi->id,
            ]);

        } catch (\Exception $e) {
            Log::error("Gagal create Midtrans order: {$e->getMessage()}", [
                'donasi_id'       => $donasi->id,
                'kode_transaksi'  => $kodeTransaksi,
            ]);

            // Tandai donasi sebagai gagal
            $donasi->update(['status_bayar' => 'gagal']);

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memproses pembayaran. Silakan coba lagi.',
            ], 500);
        }
    }

    /* ═══════════════════════════════════════════════════════════
     │  WEBHOOK: MIDTRANS NOTIFICATION HANDLER (REQ-20)
     ══════════════════════════════════════════════════════════ */

    /**
     * POST /api/donasi/notification
     * Endpoint webhook yang dipanggil oleh server Midtrans
     * saat status transaksi berubah (settlement, pending, expire, dll).
     *
     * URL ini harus di-set di Midtrans Dashboard → Settings → Payment Notification URL.
     */
    public function handleNotification(Request $request, MidtransService $midtrans): JsonResponse
    {
        $notification = $request->all();

        // Verifikasi signature key
        if (!$midtrans->verifySignature($notification)) {
            Log::warning('Midtrans notification: signature tidak valid', $notification);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $orderId           = $notification['order_id'] ?? '';
        $transactionStatus = $notification['transaction_status'] ?? '';
        $fraudStatus       = $notification['fraud_status'] ?? 'accept';
        $transactionId     = $notification['transaction_id'] ?? '';

        // Cari donasi berdasarkan kode_transaksi
        $donasi = Donasi::where('kode_transaksi', $orderId)->first();

        if (!$donasi) {
            Log::warning("Midtrans notification: donasi tidak ditemukan", ['order_id' => $orderId]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Mapping status Midtrans → status internal
        $newStatus = $midtrans->mapTransactionStatus($transactionStatus, $fraudStatus);

        // Update status donasi
        $donasi->update([
            'status_bayar'            => $newStatus,
            'midtrans_transaction_id' => $transactionId,
        ]);

        // Jika sukses → kirim email bukti donasi (REQ-21)
        if ($newStatus === 'sukses') {
            $donasi->load('bencana');
            SendDonationReceiptJob::dispatch($donasi);
        }

        Log::info("Midtrans notification processed", [
            'order_id'     => $orderId,
            'old_status'   => $donasi->getOriginal('status_bayar'),
            'new_status'   => $newStatus,
            'transaction'  => $transactionStatus,
            'fraud'        => $fraudStatus,
        ]);

        return response()->json(['message' => 'OK']);
    }

    /* ═══════════════════════════════════════════════════════════
     │  API: RINGKASAN DONASI
     ══════════════════════════════════════════════════════════ */

    /**
     * GET /api/donasi/{bencana_id}/summary
     * API: ringkasan donasi untuk bencana tertentu.
     */
    public function summary(int $bencanaId): JsonResponse
    {
        $bencana = Bencana::findOrFail($bencanaId);

        $totalTerkumpul = Donasi::where('bencana_id', $bencanaId)
            ->sukses()
            ->sum('nominal');

        $targetDana = (float) $bencana->target_dana;
        $persentase = $targetDana > 0
            ? min(100, round(($totalTerkumpul / $targetDana) * 100, 1))
            : 0;

        $jumlahDonatur = Donasi::where('bencana_id', $bencanaId)
            ->sukses()
            ->distinct('email_donatur')
            ->count('email_donatur');

        return response()->json([
            'target_dana'     => $targetDana,
            'total_terkumpul' => $totalTerkumpul,
            'persentase'      => $persentase,
            'jumlah_donatur'  => $jumlahDonatur,
        ]);
    }

    /**
     * GET /api/donasi/check-status/{kode_transaksi}
     * Cek status donasi ke Midtrans API (atau langsung database jika sudah terupdate)
     */
    public function checkStatus(string $kodeTransaksi, MidtransService $midtrans): JsonResponse
    {
        $donasi = Donasi::where('kode_transaksi', $kodeTransaksi)->first();

        if (!$donasi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan.'], 404);
        }

        // Jika sudah sukses atau gagal di DB, langsung kembalikan
        if ($donasi->status_bayar !== 'pending') {
            return response()->json([
                'status_bayar' => $donasi->status_bayar,
                'summary'      => $this->getSummaryData($donasi->bencana_id),
            ]);
        }

        // Jika masih pending, coba cek langsung ke Midtrans API
        try {
            $statusResponse = $midtrans->getTransactionStatus($kodeTransaksi);
            $transactionStatus = $statusResponse['transaction_status'] ?? '';
            $fraudStatus = $statusResponse['fraud_status'] ?? 'accept';
            $transactionId = $statusResponse['transaction_id'] ?? '';

            $newStatus = $midtrans->mapTransactionStatus($transactionStatus, $fraudStatus);

            if ($newStatus !== $donasi->status_bayar) {
                $donasi->update([
                    'status_bayar'            => $newStatus,
                    'midtrans_transaction_id' => $transactionId,
                ]);

                if ($newStatus === 'sukses') {
                    $donasi->load('bencana');
                    SendDonationReceiptJob::dispatch($donasi);
                }
            }

            return response()->json([
                'status_bayar' => $donasi->status_bayar,
                'summary'      => $this->getSummaryData($donasi->bencana_id),
            ]);

        } catch (\Exception $e) {
            Log::error("Gagal cek status Midtrans untuk {$kodeTransaksi}: " . $e->getMessage());
            // Tetap kembalikan status DB yang ada
            return response()->json([
                'status_bayar' => $donasi->status_bayar,
                'summary'      => $this->getSummaryData($donasi->bencana_id),
            ]);
        }
    }

    /**
     * POST /api/donasi/simulate-success/{kode_transaksi}
     * Simulasikan pembayaran selesai secara manual (untuk testing).
     */
    public function simulateSuccess(string $kodeTransaksi): JsonResponse
    {
        $donasi = Donasi::where('kode_transaksi', $kodeTransaksi)->first();

        if (!$donasi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan.'], 404);
        }

        if ($donasi->status_bayar !== 'sukses') {
            $donasi->update([
                'status_bayar' => 'sukses',
            ]);

            // Kirim email bukti donasi
            try {
                $donasi->load('bencana');
                SendDonationReceiptJob::dispatch($donasi);
            } catch (\Exception $e) {
                Log::error("Gagal kirim email simulasi sukses: " . $e->getMessage());
            }
        }

        return response()->json([
            'status'  => 'success',
            'summary' => $this->getSummaryData($donasi->bencana_id),
        ]);
    }

    /**
     * Helper untuk mendapatkan ringkasan statistik bencana.
     */
    private function getSummaryData(int $bencanaId): array
    {
        $bencana = Bencana::findOrFail($bencanaId);
        $totalTerkumpul = Donasi::where('bencana_id', $bencanaId)
            ->where('status_bayar', 'sukses')
            ->sum('nominal');

        $targetDana = (float) $bencana->target_dana;
        $persentase = $targetDana > 0
            ? min(100, round(($totalTerkumpul / $targetDana) * 100, 1))
            : 0;

        $jumlahDonatur = Donasi::where('bencana_id', $bencanaId)
            ->where('status_bayar', 'sukses')
            ->distinct('email_donatur')
            ->count('email_donatur');

        return [
            'total_terkumpul' => (float) $totalTerkumpul,
            'persentase'      => $persentase,
            'jumlah_donatur'  => $jumlahDonatur,
        ];
    }
}
