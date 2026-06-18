<?php

namespace Tests\Integration\Donasi;

use Tests\Integration\IntegrationTestCase;
use App\Models\Donasi;
use App\Models\Bencana;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\DonationReceipt;

class DonasiTest extends IntegrationTestCase
{
    public function test_alur_donasi_sukses_end_to_end()
    {
        Mail::fake();
        Http::fake([
            config('midtrans.api_url') => Http::response(['token' => 'mocked-snap-token-123'], 200),
        ]);

        $donatur = $this->createDonatur();
        $bencana = $this->createBencanaAktif();

        // STEP A — Submit donasi
        $this->actingAs($donatur);
        $response = $this->postJson('/api/donasi/create-order', [
            'bencana_id' => $bencana->id,
            'nominal' => 100000,
            'nama_donatur' => $donatur->nama_lengkap,
            'email_donatur' => $donatur->email,
            'metode_pembayaran' => 'transfer_bank'
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['snap_token' => 'mocked-snap-token-123']);
        $kodeTransaksi = $response->json('kode_transaksi');

        $this->assertDatabaseHas('donasi', [
            'kode_transaksi' => $kodeTransaksi,
            'status_bayar' => 'pending'
        ]);

        // STEP B — Simulasi webhook Midtrans sukses
        $orderId = $kodeTransaksi;
        $statusCode = '200';
        $grossAmount = '100000.00';
        $serverKey = config('midtrans.server_key');
        
        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        $response = $this->postJson('/api/donasi/webhook', [
            'order_id' => $orderId,
            'transaction_status' => 'settlement',
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_id' => 'midtrans-trx-123',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('donasi', [
            'kode_transaksi' => $orderId,
            'status_bayar' => 'sukses'
        ]);

        Mail::assertSent(DonationReceipt::class, function ($mail) use ($donatur) {
            return $mail->hasTo($donatur->email) &&
                   str_contains($mail->envelope()->subject, 'Bukti Donasi');
        });

        // STEP C — Cek data tidak bisa dihapus:
        // Admin coba akses endpoint hapus donasi (yang tidak eksis di sistem)
        $admin = $this->createAdmin();
        $donasiId = Donasi::where('kode_transaksi', $orderId)->first()->id;
        
        $response = $this->actingAs($admin)->deleteJson("/api/donasi/{$donasiId}");
        
        // Assert 404 Not Found atau 405 Method Not Allowed karena tidak ada routing delete donasi
        $this->assertTrue(in_array($response->status(), [404, 405]));
        
        $this->assertDatabaseHas('donasi', [
            'id' => $donasiId
        ]);
    }

    public function test_webhook_dengan_signature_tidak_valid_ditolak()
    {
        Mail::fake();

        $donasi = Donasi::factory()->create([
            'status_bayar' => 'pending'
        ]);

        $response = $this->postJson('/api/donasi/webhook', [
            'order_id' => $donasi->kode_transaksi,
            'transaction_status' => 'settlement',
            'status_code' => '200',
            'gross_amount' => '100000.00',
            'signature_key' => 'invalid-signature-123',
        ]);

        $response->assertStatus(403);
        
        $this->assertDatabaseHas('donasi', [
            'id' => $donasi->id,
            'status_bayar' => 'pending' // Tetap pending
        ]);
        
        Mail::assertNotSent(DonationReceipt::class);
    }

    public function test_webhook_duplikat_idempotent()
    {
        Mail::fake();

        $donasi = Donasi::factory()->create([
            'status_bayar' => 'pending',
            'nominal' => 100000
        ]);

        $orderId = $donasi->kode_transaksi;
        $statusCode = '200';
        $grossAmount = '100000.00';
        $serverKey = config('midtrans.server_key');
        
        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        $payload = [
            'order_id' => $orderId,
            'transaction_status' => 'settlement',
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_id' => 'midtrans-trx-123',
        ];

        // Pertama
        $response = $this->postJson('/api/donasi/webhook', $payload);
        $response->assertStatus(200);
        $this->assertEquals('sukses', $donasi->fresh()->status_bayar);

        // Kedua (duplikat persis)
        $response2 = $this->postJson('/api/donasi/webhook', $payload);
        $response2->assertStatus(200); // Harus tetap OK tanpa error

        // Assert tidak ada donasi ganda yang terbuat
        $this->assertEquals(1, Donasi::where('kode_transaksi', $orderId)->count());

        // Assert email receipt HANYA terkirim satu kali
        Mail::assertSent(DonationReceipt::class, 1);
    }

    public function test_donasi_ke_bencana_nonaktif_ditolak()
    {
        $bencana = Bencana::factory()->create([
            'status_aktif' => false
        ]);

        $response = $this->postJson('/api/donasi/create-order', [
            'bencana_id' => $bencana->id,
            'nominal' => 100000,
            'nama_donatur' => 'Anonim',
            'email_donatur' => 'anonim@mail.com',
            'metode_pembayaran' => 'transfer_bank'
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Donasi untuk bencana ini telah ditutup.']);
        
        $this->assertDatabaseMissing('donasi', [
            'bencana_id' => $bencana->id,
        ]);
    }

    public function test_progress_bar_donasi_akurat()
    {
        $bencana = $this->createBencanaAktif();
        
        // Buat 3 donasi sukses (total 225.000)
        Donasi::factory()->create(['bencana_id' => $bencana->id, 'nominal' => 50000, 'status_bayar' => 'sukses']);
        Donasi::factory()->create(['bencana_id' => $bencana->id, 'nominal' => 75000, 'status_bayar' => 'sukses']);
        Donasi::factory()->create(['bencana_id' => $bencana->id, 'nominal' => 100000, 'status_bayar' => 'sukses']);
        
        // Buat donasi gagal (tidak boleh dihitung)
        Donasi::factory()->create(['bencana_id' => $bencana->id, 'nominal' => 200000, 'status_bayar' => 'gagal']);
        
        // Buat donasi pending (tidak boleh dihitung)
        Donasi::factory()->create(['bencana_id' => $bencana->id, 'nominal' => 150000, 'status_bayar' => 'pending']);

        // Check via public API summary endpoint
        $response = $this->getJson("/api/donasi/{$bencana->id}/summary");
        
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'total_terkumpul' => 225000, // Harus presisi hanya donasi sukses
        ]);
    }

    public function test_riwayat_donasi_hanya_milik_sendiri()
    {
        $donaturA = $this->createDonatur();
        $donaturB = $this->createDonatur();
        $bencana = $this->createBencanaAktif();

        // 3 Donasi Donatur A
        Donasi::factory()->count(3)->create(['user_id' => $donaturA->id, 'bencana_id' => $bencana->id]);
        
        // 2 Donasi Donatur B
        Donasi::factory()->count(2)->create(['user_id' => $donaturB->id, 'bencana_id' => $bencana->id]);

        // Akses dashboard donatur riwayat (Web route returns View)
        $response = $this->actingAs($donaturA)->get('/dashboard/donatur/riwayat');
        $response->assertStatus(200);

        // Retrieve data variable passed to view
        $donations = $response->viewData('donations');
        $this->assertCount(3, $donations);
        
        foreach ($donations as $donasi) {
            $this->assertEquals($donaturA->id, $donasi->user_id);
            $this->assertNotEquals($donaturB->id, $donasi->user_id);
        }
    }

    public function test_donatur_dapat_memilih_berbagai_metode_pembayaran()
    {
        Http::fake([
            config('midtrans.api_url') => Http::response(['token' => 'mock-token'], 200),
        ]);

        $donatur = $this->createDonatur();
        $bencana = $this->createBencanaAktif();

        // Tes metode bayar e-wallet
        $responseEwallet = $this->actingAs($donatur)->postJson('/api/donasi/create-order', [
            'bencana_id' => $bencana->id,
            'nominal' => 50000,
            'nama_donatur' => 'Anonim',
            'email_donatur' => 'anonim@mail.com',
            'metode_pembayaran' => 'e_wallet'
        ]);
        $responseEwallet->assertStatus(200);
        $this->assertDatabaseHas('donasi', [
            'kode_transaksi' => $responseEwallet->json('kode_transaksi'),
            'metode_pembayaran' => 'e_wallet'
        ]);

        // Tes metode bayar kartu kredit
        $responseCC = $this->actingAs($donatur)->postJson('/api/donasi/create-order', [
            'bencana_id' => $bencana->id,
            'nominal' => 50000,
            'nama_donatur' => 'Anonim',
            'email_donatur' => 'anonim@mail.com',
            'metode_pembayaran' => 'kartu_kredit'
        ]);
        $responseCC->assertStatus(200);
        $this->assertDatabaseHas('donasi', [
            'kode_transaksi' => $responseCC->json('kode_transaksi'),
            'metode_pembayaran' => 'kartu_kredit'
        ]);
    }
}
