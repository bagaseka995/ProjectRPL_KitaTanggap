<?php

namespace App\Http\Controllers;

use App\Jobs\NotifyAffectedUsersJob;
use App\Models\Bencana;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BencanaController extends Controller
{
    /* ═══════════════════════════════════════════════════════════
     │  HALAMAN PUBLIK
     ══════════════════════════════════════════════════════════ */

    /**
     * GET /peta — Halaman peta interaktif publik (REQ-08, REQ-10).
     */
    public function peta(): View
    {
        return view('publik.peta');
    }

    /* ═══════════════════════════════════════════════════════════
     │  API PUBLIK
     ══════════════════════════════════════════════════════════ */

    /**
     * GET /api/bencana/peta — JSON untuk Leaflet (REQ-08, REQ-09).
     */
    public function petaApi(): JsonResponse
    {
        $bencana = Bencana::aktif()
            ->orderByRaw("CASE status_siaga WHEN 'awas' THEN 1 WHEN 'siaga' THEN 2 ELSE 3 END")
            ->get(['id', 'nama_bencana', 'jenis_bencana', 'lokasi',
                   'latitude', 'longitude', 'status_siaga',
                   'tanggal_kejadian', 'deskripsi'])
            ->map(fn ($b) => [
                'id'               => $b->id,
                'nama_bencana'     => $b->nama_bencana,
                'jenis_bencana'    => $b->jenis_bencana,
                'lokasi'           => $b->lokasi,
                'latitude'         => (float) $b->latitude,
                'longitude'        => (float) $b->longitude,
                'status_siaga'     => $b->status_siaga,
                'warna_siaga'      => $b->warna_siaga,
                'label_siaga'      => $b->label_siaga,
                'tanggal_kejadian' => $b->tanggal_kejadian?->format('d M Y'),
                'deskripsi'        => $b->deskripsi,
            ]);

        return response()->json($bencana);
    }

    /**
     * GET /api/bencana — Daftar bencana dengan filter (REQ-11).
     */
    public function index(Request $request): JsonResponse
    {
        $bencana = Bencana::aktif()
            ->lokasi($request->lokasi)
            ->jenis($request->jenis)
            ->siaga($request->siaga)
            ->rentangTanggal($request->dari, $request->sampai)
            ->orderByDesc('tanggal_kejadian')
            ->get(['id', 'nama_bencana', 'jenis_bencana', 'lokasi',
                   'latitude', 'longitude', 'status_siaga',
                   'tanggal_kejadian', 'deskripsi', 'status_aktif'])
            ->map(fn ($b) => array_merge($b->toArray(), [
                'warna_siaga'      => $b->warna_siaga,
                'label_siaga'      => $b->label_siaga,
                'tanggal_kejadian' => $b->tanggal_kejadian?->format('d M Y'),
            ]));

        return response()->json([
            'total' => $bencana->count(),
            'data'  => $bencana,
        ]);
    }

    /**
     * GET /api/bencana/{id} — Detail satu bencana (publik).
     */
    public function show(int $id): JsonResponse
    {
        $bencana = Bencana::findOrFail($id);

        return response()->json(array_merge($bencana->toArray(), [
            'warna_siaga'      => $bencana->warna_siaga,
            'label_siaga'      => $bencana->label_siaga,
            'tanggal_kejadian' => $bencana->tanggal_kejadian?->format('d M Y'),
        ]));
    }

    /**
     * POST /api/admin/bencana — Tambah Bencana Baru (Admin via API)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_bencana'     => 'required|string|max:255',
            'jenis_bencana'    => 'required|string|max:100',
            'lokasi'           => 'required|string|max:255',
            'latitude'         => 'required|numeric',
            'longitude'        => 'required|numeric',
            'tanggal_kejadian' => 'required|date',
            'status_siaga'     => 'required|in:waspada,siaga,awas',
            'deskripsi'        => 'required|string',
            'target_dana'      => 'nullable|numeric|min:0',
        ]);

        $bencana = Bencana::create(array_merge($validated, [
            'status_aktif' => true,
            'admin_id'     => auth()->id(),
        ]));

        NotifyAffectedUsersJob::dispatch($bencana);

        return response()->json([
            'message' => 'Bencana berhasil ditambahkan',
            'data'    => $bencana,
        ], 201);
    }

    /* ═══════════════════════════════════════════════════════════
     │  ADMIN WEB CRUD — Halaman manajemen bencana (REQ-07~11)
     ══════════════════════════════════════════════════════════ */

    /**
     * GET /admin/bencana — Daftar semua bencana (admin)
     */
    public function adminIndex(Request $request): View
    {
        $query = Bencana::orderByDesc('created_at');

        if ($request->filled('jenis')) {
            $query->where('jenis_bencana', $request->jenis);
        }
        if ($request->filled('siaga')) {
            $query->where('status_siaga', $request->siaga);
        }
        if ($request->filled('status')) {
            $query->where('status_aktif', $request->status === 'aktif' ? 1 : 0);
        }
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_bencana', 'like', "%{$request->q}%")
                  ->orWhere('lokasi', 'like', "%{$request->q}%");
            });
        }

        $bencanaList = $query->paginate(10)->withQueryString();

        $summary = [
            'total'  => Bencana::count(),
            'aktif'  => Bencana::where('status_aktif', true)->count(),
            'awas'   => Bencana::where('status_siaga', 'awas')->count(),
            'siaga'  => Bencana::where('status_siaga', 'siaga')->count(),
        ];

        return view('admin.bencana.index', compact('bencanaList', 'summary'));
    }

    /**
     * GET /admin/bencana/create — Form tambah bencana baru (admin)
     */
    public function adminCreate(): View
    {
        return view('admin.bencana.create');
    }

    /**
     * POST /admin/bencana — Simpan bencana baru via form web (admin)
     */
    public function adminStore(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_bencana'     => 'required|string|max:255',
            'jenis_bencana'    => 'required|string|max:100',
            'lokasi'           => 'required|string|max:255',
            'latitude'         => 'required|numeric|between:-90,90',
            'longitude'        => 'required|numeric|between:-180,180',
            'tanggal_kejadian' => 'required|date',
            'status_siaga'     => 'required|in:waspada,siaga,awas',
            'deskripsi'        => 'required|string',
            'target_dana'      => 'nullable|numeric|min:0',
        ], [
            'nama_bencana.required'     => 'Nama bencana wajib diisi.',
            'jenis_bencana.required'    => 'Jenis bencana wajib dipilih.',
            'lokasi.required'           => 'Lokasi bencana wajib diisi.',
            'latitude.required'         => 'Koordinat latitude wajib diisi.',
            'latitude.between'          => 'Latitude harus antara -90 dan 90.',
            'longitude.required'        => 'Koordinat longitude wajib diisi.',
            'longitude.between'         => 'Longitude harus antara -180 dan 180.',
            'tanggal_kejadian.required' => 'Tanggal kejadian wajib diisi.',
            'status_siaga.required'     => 'Status siaga wajib dipilih.',
            'deskripsi.required'        => 'Deskripsi bencana wajib diisi.',
        ]);

        $bencana = Bencana::create([
            'nama_bencana'     => $request->nama_bencana,
            'jenis_bencana'    => $request->jenis_bencana,
            'lokasi'           => $request->lokasi,
            'latitude'         => $request->latitude,
            'longitude'        => $request->longitude,
            'tanggal_kejadian' => $request->tanggal_kejadian,
            'status_siaga'     => $request->status_siaga,
            'deskripsi'        => $request->deskripsi,
            'target_dana'      => $request->input('target_dana', 0) ?? 0,
            'status_aktif'     => true,
            'admin_id'         => auth()->id(),
        ]);

        try {
            NotifyAffectedUsersJob::dispatch($bencana);
        } catch (\Exception $e) {
            \Log::warning("Gagal dispatch NotifyAffectedUsersJob untuk bencana #{$bencana->id}: " . $e->getMessage());
        }

        return redirect()->route('admin.bencana.index')
            ->with('success', "Bencana \"{$bencana->nama_bencana}\" berhasil ditambahkan dan notifikasi dikirim.");
    }

    /**
     * GET /admin/bencana/{id}/edit — Form edit bencana (admin)
     */
    public function adminEdit(int $id): View
    {
        $bencana = Bencana::findOrFail($id);
        return view('admin.bencana.edit', compact('bencana'));
    }

    /**
     * PUT /admin/bencana/{id} — Update data bencana (admin)
     */
    public function adminUpdate(Request $request, int $id): RedirectResponse
    {
        $bencana = Bencana::findOrFail($id);

        $request->validate([
            'nama_bencana'     => 'required|string|max:255',
            'jenis_bencana'    => 'required|string|max:100',
            'lokasi'           => 'required|string|max:255',
            'latitude'         => 'required|numeric|between:-90,90',
            'longitude'        => 'required|numeric|between:-180,180',
            'tanggal_kejadian' => 'required|date',
            'status_siaga'     => 'required|in:waspada,siaga,awas',
            'deskripsi'        => 'required|string',
            'target_dana'      => 'nullable|numeric|min:0',
        ], [
            'nama_bencana.required'     => 'Nama bencana wajib diisi.',
            'jenis_bencana.required'    => 'Jenis bencana wajib dipilih.',
            'lokasi.required'           => 'Lokasi bencana wajib diisi.',
            'tanggal_kejadian.required' => 'Tanggal kejadian wajib diisi.',
            'status_siaga.required'     => 'Status siaga wajib dipilih.',
            'deskripsi.required'        => 'Deskripsi bencana wajib diisi.',
        ]);

        $bencana->update([
            'nama_bencana'     => $request->nama_bencana,
            'jenis_bencana'    => $request->jenis_bencana,
            'lokasi'           => $request->lokasi,
            'latitude'         => $request->latitude,
            'longitude'        => $request->longitude,
            'tanggal_kejadian' => $request->tanggal_kejadian,
            'status_siaga'     => $request->status_siaga,
            'deskripsi'        => $request->deskripsi,
            'target_dana'      => $request->input('target_dana', 0) ?? 0,
            'status_aktif'     => $request->boolean('status_aktif'),
        ]);

        return redirect()->route('admin.bencana.index')
            ->with('success', "Data bencana \"{$bencana->nama_bencana}\" berhasil diperbarui.");
    }

    /**
     * DELETE /admin/bencana/{id} — Hapus bencana (admin)
     */
    public function adminDestroy(int $id): RedirectResponse
    {
        $bencana = Bencana::findOrFail($id);
        $nama = $bencana->nama_bencana;
        $bencana->delete();

        return redirect()->route('admin.bencana.index')
            ->with('success', "Bencana \"{$nama}\" berhasil dihapus.");
    }

    /**
     * PATCH /admin/bencana/{id}/toggle — Toggle status aktif/nonaktif bencana (admin, via AJAX)
     */
    public function adminToggle(int $id): JsonResponse
    {
        $bencana = Bencana::findOrFail($id);
        $bencana->update(['status_aktif' => !$bencana->status_aktif]);

        $bencana->refresh();
        $status = $bencana->status_aktif ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'status'       => 'success',
            'message'      => "Bencana \"{$bencana->nama_bencana}\" berhasil {$status}.",
            'status_aktif' => $bencana->status_aktif,
        ]);
    }
}
