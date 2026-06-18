<?php

namespace Tests\Integration\Donasi;

use Tests\Integration\IntegrationTestCase;
use App\Models\Bencana;
use App\Models\LaporanDistribusi;
use App\Models\Donasi;

class LaporanDistribusiTest extends IntegrationTestCase
{
    public function test_publik_dapat_melihat_halaman_transparansi()
    {
        $response = $this->get('/transparansi');
        $response->assertStatus(200);
    }

    public function test_admin_dapat_membuat_laporan_distribusi_dana()
    {
        $admin = $this->createAdmin();
        $bencana = $this->createBencanaAktif();

        // Admin submit laporan
        $response = $this->actingAs($admin)->post('/admin/laporan-distribusi', [
            'bencana_id' => $bencana->id,
            'rincian_penggunaan' => 'Pembagian 100 paket sembako untuk korban gempa.',
            'jumlah_disalurkan' => 5000000,
        ]);

        $response->assertRedirect(route('admin.laporan-distribusi.index'));
        $this->assertDatabaseHas('laporan_distribusi', [
            'bencana_id' => $bencana->id,
            'rincian_penggunaan' => 'Pembagian 100 paket sembako untuk korban gempa.',
            'jumlah_disalurkan' => 5000000,
        ]);
    }

    public function test_laporan_terpublikasi_muncul_di_halaman_transparansi()
    {
        $bencana = $this->createBencanaAktif();
        
        LaporanDistribusi::create([
            'bencana_id' => $bencana->id,
            'rincian_penggunaan' => 'Pembangunan Tenda Darurat',
            'jumlah_disalurkan' => 2000000,
            'tanggal_laporan' => now(),
        ]);

        $response = $this->get('/transparansi');
        $response->assertStatus(200);
        $response->assertSee('Pembangunan Tenda Darurat');
    }
}
