<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Tampilkan halaman dashboard manajemen pengguna/verifikasi akun.
     */
    public function index(): View
    {
        $summary = [
            'total'    => User::where('peran', '!=', 'admin')->count(),
            'pending'  => User::where('peran', '!=', 'admin')->where('status_akun', 'pending')->count(),
            'aktif'    => User::where('peran', '!=', 'admin')->where('status_akun', 'aktif')->count(),
            'nonaktif' => User::where('peran', '!=', 'admin')->where('status_akun', 'nonaktif')->count(),
        ];

        return view('admin.users.index', compact('summary'));
    }

    /**
     * API untuk mengambil daftar pendaftar dengan filter status, peran, pencarian nama/email, serta paginasi.
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $query = User::where('peran', '!=', 'admin');

        // Filter status_akun
        if ($request->filled('status')) {
            $query->where('status_akun', $request->status);
        }

        // Filter peran (role)
        if ($request->filled('peran')) {
            $query->where('peran', $request->peran);
        }

        // Pencarian Nama / Email
        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('nama_lengkap', 'like', "%{$cari}%")
                  ->orWhere('email', 'like', "%{$cari}%");
            });
        }

        $users = $query->orderByDesc('created_at')->paginate(10);

        // Tambahkan atribut warna_status helper ke response JSON
        $users->getCollection()->transform(function ($user) {
            $warna = match ($user->status_akun) {
                'aktif'    => '#22c55e', // green-500
                'nonaktif' => '#ef4444', // red-500
                'pending'  => '#eab308', // yellow-500
                default    => '#9ca3af',
            };
            return array_merge($user->toArray(), [
                'warna_status' => $warna,
            ]);
        });

        // Hitung ringkasan terbaru
        $summary = [
            'total'    => User::where('peran', '!=', 'admin')->count(),
            'pending'  => User::where('peran', '!=', 'admin')->where('status_akun', 'pending')->count(),
            'aktif'    => User::where('peran', '!=', 'admin')->where('status_akun', 'aktif')->count(),
            'nonaktif' => User::where('peran', '!=', 'admin')->where('status_akun', 'nonaktif')->count(),
        ];

        return response()->json([
            'summary' => $summary,
            'data'    => $users,
        ]);
    }

    /**
     * Memproses verifikasi/persetujuan akun oleh administrator.
     */
    public function verifikasi(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'aksi' => ['required', 'in:aktif,nonaktif'],
        ], [
            'aksi.required' => 'Aksi wajib diisi.',
            'aksi.in'       => 'Aksi tidak valid. Pilih: aktif atau nonaktif.',
        ]);

        $user = User::where('peran', '!=', 'admin')->findOrFail($id);

        if ($request->aksi === 'aktif') {
            $user->update([
                'status_akun'       => 'aktif',
                'email_verified_at' => now(), // otomatis tandai email terverifikasi
            ]);
            $pesan = "Akun {$user->nama_lengkap} berhasil disetujui (ACC) dan sekarang sudah aktif.";
        } else {
            $user->update([
                'status_akun' => 'nonaktif',
            ]);
            $pesan = "Akun {$user->nama_lengkap} telah dinonaktifkan/ditolak.";
        }

        $warna = match ($user->status_akun) {
            'aktif'    => '#22c55e',
            'nonaktif' => '#ef4444',
            'pending'  => '#eab308',
            default    => '#9ca3af',
        };

        return response()->json([
            'status'  => 'success',
            'message' => $pesan,
            'data'    => array_merge($user->toArray(), [
                'warna_status' => $warna,
            ]),
        ]);
    }
}
