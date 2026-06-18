<?php

namespace Tests\Integration\Relawan;

use Tests\Integration\IntegrationTestCase;
use App\Models\User;
use App\Models\Bencana;
use App\Models\Relawan;
use App\Models\Penugasan;
use App\Models\Sertifikat;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\PenugasanRelawanMail;
use App\Mail\PenolakanRelawanMail;
use App\Mail\SertifikatRelawanMail;

class ManajemenRelawanTest extends IntegrationTestCase
{
    public function test_alur_lengkap_relawan_dari_profil_hingga_sertifikat()
    {
        Mail::fake();
        Storage::fake('public');

        // SETUP: siapkan admin, relawan, bencana aktif
        $admin = $this->createAdmin();
        $userRelawan = $this->createRelawan();
        $bencana = $this->createBencanaAktif();

        // STEP A — Isi profil
        $this->actingAs($userRelawan);
        $response = $this->post('/relawan/profil', [
            'keahlian' => 'Medis, Logistik',
            'pengalaman' => 'Pernah jadi relawan gempa bumi.',
            'lokasi_domisili' => 'Bandung',
            'ketersediaan' => true,
        ]);
        
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('relawan', [
            'user_id' => $userRelawan->id,
            'status_verifikasi' => 'pending',
        ]);
        
        $relawan = Relawan::where('user_id', $userRelawan->id)->first();

        // STEP B — Admin verifikasi
        $response = $this->actingAs($admin)->patch("/api/relawan/{$relawan->id}/verifikasi", [
            'aksi' => 'terverifikasi'
        ]);
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('relawan', [
            'id' => $relawan->id,
            'status_verifikasi' => 'terverifikasi'
        ]);
        Mail::assertNotSent(PenolakanRelawanMail::class);

        // STEP C — Admin tugaskan
        $response = $this->actingAs($admin)->post('/api/penugasan', [
            'relawan_id' => $relawan->id,
            'bencana_id' => $bencana->id,
            'tanggal_tugas' => now()->addDays(2)->toDateString(),
        ]);
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('penugasan', [
            'relawan_id' => $relawan->id,
            'bencana_id' => $bencana->id,
            'status_tugas' => 'ditugaskan',
        ]);
        
        $penugasan = Penugasan::where('relawan_id', $relawan->id)->first();
        
        Mail::assertSent(PenugasanRelawanMail::class, function ($mail) use ($userRelawan) {
            return $mail->hasTo($userRelawan->email);
        });

        // STEP D — Admin selesaikan misi
        $response = $this->actingAs($admin)->patch("/api/penugasan/{$penugasan->id}/selesai");
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('penugasan', [
            'id' => $penugasan->id,
            'status_tugas' => 'selesai'
        ]);
        
        $sertifikat = Sertifikat::where('penugasan_id', $penugasan->id)->first();
        $this->assertNotNull($sertifikat);
        $this->assertMatchesRegularExpression('/^SERT-\d{4}-[A-Z0-9]{6}$/', $sertifikat->kode_sertifikat);
        
        $fileName = str_replace('storage/', '', $sertifikat->file_path);
        Storage::disk('public')->assertExists($fileName);

        Mail::assertSent(SertifikatRelawanMail::class, function ($mail) use ($userRelawan) {
            return $mail->hasTo($userRelawan->email);
        });

        // STEP E — Verifikasi keaslian sertifikat (halaman publik tanpa login)
        $this->post('/logout'); // pastikan kita mengakses sebagai publik
        
        $response = $this->get("/verifikasi/{$sertifikat->kode_sertifikat}");
        $response->assertStatus(200);
        $response->assertSee($userRelawan->nama_lengkap);
        $response->assertSee($bencana->nama_bencana);
    }

    public function test_admin_tolak_relawan_email_terkirim_relawan_tidak_bisa_ditugaskan()
    {
        Mail::fake();

        $admin = $this->createAdmin();
        $userRelawan = $this->createRelawan();
        $bencana = $this->createBencanaAktif();

        // Buat profil relawan (status pending)
        $relawan = Relawan::factory()->create([
            'user_id' => $userRelawan->id,
            'status_verifikasi' => 'pending'
        ]);

        // Admin PATCH verifikasi dengan aksi='ditolak'
        $response = $this->actingAs($admin)->patch("/api/relawan/{$relawan->id}/verifikasi", [
            'aksi' => 'ditolak'
        ]);
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('relawan', [
            'id' => $relawan->id,
            'status_verifikasi' => 'ditolak'
        ]);
        
        Mail::assertSent(PenolakanRelawanMail::class, function ($mail) use ($userRelawan) {
            return $mail->hasTo($userRelawan->email);
        });

        // Admin coba POST /api/penugasan dengan relawan yang ditolak
        $response = $this->actingAs($admin)->post('/api/penugasan', [
            'relawan_id' => $relawan->id,
            'bencana_id' => $bencana->id,
            'tanggal_tugas' => now()->addDay()->toDateString(),
        ]);
        
        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Relawan belum terverifikasi']);
        
        $this->assertDatabaseMissing('penugasan', [
            'relawan_id' => $relawan->id,
            'bencana_id' => $bencana->id,
        ]);
    }

    public function test_duplikasi_penugasan_ditolak()
    {
        $admin = $this->createAdmin();
        $userRelawan = $this->createRelawanTerverifikasi();
        $bencana = $this->createBencanaAktif();
        $relawanId = $userRelawan->relawan->id;

        // Relawan sudah punya penugasan aktif di bencana X
        Penugasan::factory()->create([
            'relawan_id' => $relawanId,
            'bencana_id' => $bencana->id,
            'status_tugas' => 'ditugaskan'
        ]);

        $initialCount = Penugasan::count();

        // Admin coba tugaskan relawan yang sama ke bencana X lagi
        $response = $this->actingAs($admin)->post('/api/penugasan', [
            'relawan_id' => $relawanId,
            'bencana_id' => $bencana->id,
            'tanggal_tugas' => now()->addDays(2)->toDateString(),
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Relawan sudah ditugaskan di bencana ini']);
        
        $this->assertEquals($initialCount, Penugasan::count());
    }

    public function test_relawan_lihat_riwayat_misinya_sendiri()
    {
        $userRelawanA = $this->createRelawanTerverifikasi();
        $userRelawanB = $this->createRelawanTerverifikasi();
        
        // Sengaja buat bencana berbeda agar variatif
        $bencanaA = $this->createBencanaAktif();
        $bencanaB = $this->createBencanaAktif();

        // Buat 3 penugasan untuk relawan A
        Penugasan::factory()->count(3)->create([
            'relawan_id' => $userRelawanA->relawan->id,
            'bencana_id' => $bencanaA->id,
        ]);

        // Buat 2 penugasan untuk relawan B
        Penugasan::factory()->count(2)->create([
            'relawan_id' => $userRelawanB->relawan->id,
            'bencana_id' => $bencanaB->id,
        ]);

        // Login sebagai relawan A
        $response = $this->actingAs($userRelawanA)->get('/api/relawan/riwayat');
        $response->assertStatus(200);
        
        $data = $response->json('data');
        
        // Assert: response hanya mengandung 3 penugasan milik relawan A
        $this->assertCount(3, $data);
        
        foreach ($data as $item) {
            $this->assertEquals($userRelawanA->relawan->id, $item['relawan_id']);
            // Assert tidak ada data relawan B
            $this->assertNotEquals($userRelawanB->relawan->id, $item['relawan_id']);
            
            // Assert setiap item ada field bencana (nama, lokasi)
            $this->assertArrayHasKey('bencana', $item);
            $this->assertArrayHasKey('nama_bencana', $item['bencana']);
            $this->assertArrayHasKey('lokasi', $item['bencana']);
        }
    }

    public function test_admin_dapat_memfilter_relawan()
    {
        $admin = $this->createAdmin();

        $user1 = $this->createRelawan();
        Relawan::factory()->create([
            'user_id' => $user1->id,
            'status_verifikasi' => 'terverifikasi',
            'keahlian' => 'Medis, Dapur Umum',
            'lokasi_domisili' => 'Bandung',
        ]);

        $user2 = $this->createRelawan();
        Relawan::factory()->create([
            'user_id' => $user2->id,
            'status_verifikasi' => 'pending',
            'keahlian' => 'Logistik, Evakuasi',
            'lokasi_domisili' => 'Jakarta',
        ]);

        // Filter by lokasi = Bandung
        $response = $this->actingAs($admin)->getJson('/api/relawan?lokasi=Bandung');
        $response->assertStatus(200);
        $data = $response->json('data.data');
        $this->assertCount(1, $data);
        $this->assertEquals('Bandung', $data[0]['lokasi_domisili']);

        // Filter by keahlian = Logistik
        $response2 = $this->actingAs($admin)->getJson('/api/relawan?keahlian=Logistik');
        $response2->assertStatus(200);
        $data2 = $response2->json('data.data');
        $this->assertCount(1, $data2);
        $this->assertStringContainsString('Logistik', $data2[0]['keahlian']);

        // Filter by status = pending
        $response3 = $this->actingAs($admin)->getJson('/api/relawan?status=pending');
        $response3->assertStatus(200);
        $data3 = $response3->json('data.data');
        $this->assertCount(1, $data3);
        $this->assertEquals('pending', $data3[0]['status_verifikasi']);
    }
}
