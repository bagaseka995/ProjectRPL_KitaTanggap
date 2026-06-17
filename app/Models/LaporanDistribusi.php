<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class LaporanDistribusi extends Model
{
    use HasFactory;

    protected $table = 'laporan_distribusi';

    protected $fillable = [
        'bencana_id',
        'rincian_penggunaan',
        'bukti_distribusi',
        'jumlah_disalurkan',
        'tanggal_laporan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_disalurkan' => 'decimal:2',
            'tanggal_laporan'   => 'datetime',
        ];
    }

    /* ─── Relasi ──────────────────────────────────────────────── */

    public function bencana(): BelongsTo
    {
        return $this->belongsTo(Bencana::class);
    }

    /* ─── Accessor ───────────────────────────────────────────── */

    /** Format jumlah disalurkan ke Rupiah */
    public function getJumlahFormattedAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->jumlah_disalurkan, 0, ',', '.');
    }

    /**
     * URL publik untuk bukti distribusi.
     * Handle dua format path:
     * - Format lama: "storage/distribusi/filename.ext" (disimpan dengan prefix)
     * - Format baru: "distribusi/filename.ext" (path relatif storage public)
     */
    public function getBuktiUrlAttribute(): ?string
    {
        if (!$this->bukti_distribusi) {
            return null;
        }

        $path = $this->bukti_distribusi;

        // Jika path dimulai dengan 'storage/', itu format lama
        // Strip prefix 'storage/' dan gunakan path relatif untuk Storage::url()
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        // Generate URL via Storage facade (benar, tidak perlu symlink dengan route serve)
        return route('storage.serve', ['path' => $path]);
    }
}

