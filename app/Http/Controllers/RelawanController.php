<?php

namespace App\Http\Controllers;

use App\Http\Requests\RelawanProfilRequest;
use App\Mail\PenolakanRelawanMail;
use App\Models\Relawan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class RelawanController extends Controller
{
    /* ═══════════════════════════════════════════════════════════
     │  PROFIL RELAWAN (REQ-12)
     ══════════════════════════════════════════════════════════ */

    /** GET /relawan/profil */
    public function profil(): View
    {
        $relawan = auth()->user()->relawan;
        return view('relawan.profil', compact('relawan'));
    }

    /** POST /relawan/profil — buat profil baru */
    public function store(RelawanProfilRequest $request): RedirectResponse
    {
        // Satu user hanya boleh punya satu profil
        if (auth()->user()->relawan) {
            return back()->withErrors(['umum' => 'Profil relawan Anda sudah ada. Gunakan form edit.']);
        }

        Relawan::create([
            'user_id'           => auth()->id(),
            'keahlian'          => $request->keahlian,
            'pengalaman'        => $request->pengalaman,
            'lokasi_domisili'   => $request->lokasi_domisili,
            'ketersediaan'      => $request->boolean('ketersediaan'),
            'status_verifikasi' => 'terverifikasi', // langsung terverifikasi untuk kemudahan demo
        ]);

        return back()->with('success', 'Profil relawan berhasil disimpan!');
    }

    /** PUT /relawan/profil — edit profil */
    public function update(RelawanProfilRequest $request): RedirectResponse
    {
        $relawan = auth()->user()->relawan;

        if (!$relawan) {
            return back()->withErrors(['umum' => 'Profil relawan tidak ditemukan.']);
        }

        $relawan->update([
            'keahlian'          => $request->keahlian,
            'pengalaman'        => $request->pengalaman,
            'lokasi_domisili'   => $request->lokasi_domisili,
            'ketersediaan'      => $request->boolean('ketersediaan'),
            'status_verifikasi' => 'terverifikasi', // langsung terverifikasi untuk kemudahan demo
        ]);

        return back()->with('success', 'Profil relawan berhasil diperbarui.');
    }

    /* ═══════════════════════════════════════════════════════════
     │  ADMIN: DAFTAR & VERIFIKASI RELAWAN (REQ-13, REQ-14)
     ══════════════════════════════════════════════════════════ */

    /** GET /api/relawan — daftar relawan dengan filter (admin) */
    public function index(Request $request): JsonResponse
    {
        $relawan = Relawan::with('user:id,nama_lengkap,email,no_telepon')
            ->status($request->status)
            ->keahlian($request->keahlian)
            ->lokasi($request->lokasi)
            ->orderByDesc('created_at')
            ->paginate(10);

        // Tambah accessor ke setiap item
        $relawan->getCollection()->transform(fn ($r) => array_merge($r->toArray(), [
            'warna_status' => $r->warna_status,
            'keahlian_array' => $r->keahlian_array,
        ]));

        // Hitung ringkasan
        $summary = [
            'total'          => Relawan::count(),
            'pending'        => Relawan::where('status_verifikasi', 'pending')->count(),
            'terverifikasi'  => Relawan::where('status_verifikasi', 'terverifikasi')->count(),
            'ditolak'        => Relawan::where('status_verifikasi', 'ditolak')->count(),
        ];

        return response()->json([
            'summary' => $summary,
            'data'    => $relawan,
        ]);
    }

    /** PATCH /api/relawan/{id}/verifikasi — verifikasi/tolak relawan (admin) */
    public function verifikasi(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'aksi' => ['required', 'in:terverifikasi,ditolak'],
        ], [
            'aksi.required' => 'Aksi verifikasi wajib diisi.',
            'aksi.in'       => 'Aksi tidak valid. Pilih: terverifikasi atau ditolak.',
        ]);

        $relawan = Relawan::with('user')->findOrFail($id);
        $relawan->update(['status_verifikasi' => $request->aksi]);

        // Kirim email penolakan (REQ-14)
        if ($request->aksi === 'ditolak') {
            try {
                Mail::to($relawan->user->email)->send(new PenolakanRelawanMail($relawan));
            } catch (\Exception $e) {
                // Log error tapi jangan gagalkan response
                \Log::warning("Gagal kirim email penolakan relawan #{$id}: " . $e->getMessage());
            }
        }

        $pesan = $request->aksi === 'terverifikasi'
            ? "Relawan {$relawan->user->nama_lengkap} berhasil diverifikasi."
            : "Pendaftaran {$relawan->user->nama_lengkap} ditolak. Email pemberitahuan telah dikirim.";

        return response()->json([
            'status'  => 'success',
            'message' => $pesan,
            'data'    => array_merge($relawan->fresh()->toArray(), [
                'warna_status' => $relawan->fresh()->warna_status,
            ]),
        ]);
    }

    /** GET /admin/relawan — halaman dashboard admin */
    public function adminIndex(): View
    {
        $summary = [
            'total'         => Relawan::count(),
            'pending'       => Relawan::where('status_verifikasi', 'pending')->count(),
            'terverifikasi' => Relawan::where('status_verifikasi', 'terverifikasi')->count(),
            'ditolak'       => Relawan::where('status_verifikasi', 'ditolak')->count(),
        ];

        return view('admin.relawan.index', compact('summary'));
    }
}
