<?php

namespace App\Http\Controllers;

use App\Models\Sertifikat;
use App\Models\Penugasan;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SertifikatController extends Controller
{
    /**
     * GET /verifikasi/{kode}
     * Halaman publik untuk memverifikasi keaslian sertifikat relawan.
     * Tidak membutuhkan login.
     *
     * @param string $kode
     * @return View
     */
    public function verifikasi(string $kode): View
    {
        $sertifikat = Sertifikat::where('kode_sertifikat', $kode)
            ->with(['penugasan.relawan.user', 'penugasan.bencana'])
            ->first();

        return view('publik.verifikasi', compact('sertifikat', 'kode'));
    }

    /**
     * GET /sertifikat/{kode}/unduh
     * Unduh file PDF sertifikat relawan.
     *
     * @param string $kode
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function unduh(string $kode)
    {
        $sertifikat = Sertifikat::where('kode_sertifikat', $kode)->firstOrFail();
        $fileName = str_replace('storage/', '', $sertifikat->file_path);

        if (!Storage::disk('public')->exists($fileName)) {
            try {
                $penugasan = Penugasan::with(['relawan.user', 'bencana'])->findOrFail($sertifikat->penugasan_id);
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('sertifikat.template', [
                    'penugasan' => $penugasan,
                    'kode' => $sertifikat->kode_sertifikat
                ])->setPaper('a4', 'landscape');
                
                Storage::disk('public')->put($fileName, $pdf->output());
            } catch (\Exception $e) {
                abort(404, 'File sertifikat tidak ditemukan dan gagal dibuat ulang.');
            }
        }

        return Storage::disk('public')->download($fileName, "Sertifikat-{$sertifikat->kode_sertifikat}.pdf");
    }
}
