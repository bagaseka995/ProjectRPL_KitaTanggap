<?php

namespace Tests\Integration\Notifikasi;

use Tests\Integration\IntegrationTestCase;
use App\Models\User;
use App\Models\Bencana;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use App\Mail\BencanaAlert;
use App\Jobs\NotifyAffectedUsersJob;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class NotifikasiTest extends IntegrationTestCase
{
    public function test_email_alert_terkirim_ke_user_wilayah_terdampak()
    {
        Mail::fake();

        // SETUP 3 user
        $userA = User::factory()->create([
            'lokasi_domisili' => 'Bandung, Jawa Barat',
            'notif_aktif' => true,
            'email_verified_at' => now(),
        ]);
        
        $userB = User::factory()->create([
            'lokasi_domisili' => 'Bandung Barat',
            'notif_aktif' => true,
            'email_verified_at' => now(),
        ]);

        $userC = User::factory()->create([
            'lokasi_domisili' => 'Surabaya, Jawa Timur',
            'notif_aktif' => true,
            'email_verified_at' => now(),
        ]);

        $admin = $this->createAdmin();

        // Admin POST bencana
        $response = $this->actingAs($admin)->post('/admin/bencana', [
            'nama_bencana' => 'Gempa Bumi Lokal',
            'jenis_bencana' => 'gempa',
            'lokasi' => 'Bandung',
            'latitude' => -6.9175,
            'longitude' => 107.6191,
            'tanggal_kejadian' => now()->toDateString(),
            'status_siaga' => 'siaga',
            'deskripsi' => 'Terjadi gempa ringan'
        ]);

        $response->assertRedirect();

        // Karena default config menggunakan queue sync, job langsung ter-execute
        // Assert: email alert terkirim ke user A dan B, tapi tidak ke C
        Mail::assertSent(BencanaAlert::class, function ($mail) use ($userA) {
            return $mail->hasTo($userA->email);
        });

        Mail::assertSent(BencanaAlert::class, function ($mail) use ($userB) {
            return $mail->hasTo($userB->email);
        });

        Mail::assertNotSent(BencanaAlert::class, function ($mail) use ($userC) {
            return $mail->hasTo($userC->email);
        });

        // Assert jumlah total email BencanaAlert
        // Kita hitung jumlah seluruh pengiriman Mailable jenis ini
        // Karena framework Laravel Mail Fake assertSent parameter count harus exact, 
        // pastikan hanya 2 yang dikirim.
        Mail::assertSent(BencanaAlert::class, 2);
    }

    public function test_user_dengan_notif_aktif_false_tidak_menerima_alert()
    {
        Mail::fake();

        $userA = User::factory()->create([
            'lokasi_domisili' => 'Jakarta',
            'notif_aktif' => true,
            'email_verified_at' => now(),
        ]);

        $userB = User::factory()->create([
            'lokasi_domisili' => 'Jakarta',
            'notif_aktif' => false, // false
            'email_verified_at' => now(),
        ]);

        $admin = $this->createAdmin();

        $this->actingAs($admin)->post('/admin/bencana', [
            'nama_bencana' => 'Banjir Jakarta',
            'jenis_bencana' => 'banjir',
            'lokasi' => 'Jakarta',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'tanggal_kejadian' => now()->toDateString(),
            'status_siaga' => 'awas',
            'deskripsi' => 'Banjir merendam beberapa titik'
        ]);

        Mail::assertSent(BencanaAlert::class, function ($mail) use ($userA) {
            return $mail->hasTo($userA->email);
        });

        Mail::assertNotSent(BencanaAlert::class, function ($mail) use ($userB) {
            return $mail->hasTo($userB->email);
        });

        Mail::assertSent(BencanaAlert::class, 1);
    }

    public function test_toggle_preferensi_notifikasi()
    {
        $relawan = $this->createRelawan();

        // Default notif_aktif biasanya false atau true bergantung factory
        // Kita paksa true dulu sebagai base assumption
        $relawan->update(['notif_aktif' => true]);
        
        $this->assertTrue($relawan->notif_aktif);

        // PATCH notif_aktif: false
        $response = $this->actingAs($relawan)->patchJson('/pengaturan/notifikasi/update', [
            'notif_aktif' => false,
            'fcm_aktif' => false,
            'lokasi_domisili' => 'Yogyakarta',
        ]);

        $response->assertStatus(200);
        $this->assertFalse($relawan->fresh()->notif_aktif);

        // PATCH notif_aktif: true
        $response = $this->actingAs($relawan)->patchJson('/pengaturan/notifikasi/update', [
            'notif_aktif' => true,
            'fcm_aktif' => false,
            'lokasi_domisili' => 'Yogyakarta',
        ]);

        $response->assertStatus(200);
        $this->assertTrue($relawan->fresh()->notif_aktif);
    }

    public function test_update_bencana_tidak_trigger_email_alert_ulang()
    {
        Mail::fake();

        $user = User::factory()->create([
            'lokasi_domisili' => 'Medan',
            'notif_aktif' => true,
            'email_verified_at' => now(),
        ]);

        $admin = $this->createAdmin();

        // Admin publish bencana baru → email terkirim 1
        $this->actingAs($admin)->post('/admin/bencana', [
            'nama_bencana' => 'Gempa Medan',
            'jenis_bencana' => 'gempa',
            'lokasi' => 'Medan',
            'latitude' => 3.5952,
            'longitude' => 98.6722,
            'tanggal_kejadian' => now()->toDateString(),
            'status_siaga' => 'siaga',
            'deskripsi' => 'Deskripsi lama'
        ]);

        Mail::assertSent(BencanaAlert::class, 1);

        $bencana = Bencana::where('lokasi', 'Medan')->first();

        // Admin UPDATE bencana yang sama
        $this->actingAs($admin)->put("/admin/bencana/{$bencana->id}", [
            'nama_bencana' => 'Gempa Medan Update',
            'jenis_bencana' => 'gempa',
            'lokasi' => 'Medan',
            'latitude' => 3.5952,
            'longitude' => 98.6722,
            'tanggal_kejadian' => now()->toDateString(),
            'status_siaga' => 'siaga',
            'deskripsi' => 'Deskripsi BARU diubah'
        ]);

        // Assert: jumlah total email tidak bertambah
        Mail::assertSent(BencanaAlert::class, 1);
    }

    public function test_email_dikirim_via_queue_async()
    {
        // Ganti config queue agar tertahan di database
        Config::set('queue.default', 'database');
        
        // Membersihkan jobs sebelumnya (jika ada sisa)
        DB::table('jobs')->truncate();

        $admin = $this->createAdmin();

        // Buat user target
        User::factory()->create([
            'lokasi_domisili' => 'Makassar',
            'notif_aktif' => true,
            'email_verified_at' => now(),
        ]);

        // Admin publish bencana baru
        $this->actingAs($admin)->post('/admin/bencana', [
            'nama_bencana' => 'Bencana Makassar',
            'jenis_bencana' => 'lainnya',
            'lokasi' => 'Makassar',
            'latitude' => -5.1477,
            'longitude' => 119.4327,
            'tanggal_kejadian' => now()->toDateString(),
            'status_siaga' => 'waspada',
            'deskripsi' => 'Testing Queue'
        ]);

        // Assert: job ada di tabel jobs (belum dieksekusi)
        $this->assertEquals(1, DB::table('jobs')->count());

        // Jalankan artisan queue:work --once untuk memproses tepat 1 antrean
        Artisan::call('queue:work', [
            '--once' => true
        ]);

        // Setelah NotifyAffectedUsersJob sukses memprosesnya, karena di dalam Handle ada 
        // pemanggilan Mail::send (yg sudah diubah di file job-nya), pekerjaan seharusnya beres
        // tanpa memunculkan jobs turunan.
        
        // Assert: tabel jobs kosong
        $this->assertEquals(0, DB::table('jobs')->count());
    }

    public function test_fcm_token_dapat_disimpan()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/fcm/register', [
            'fcm_token' => 'dummy-fcm-token-12345'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('user_fcm_tokens', [
            'user_id' => $user->id,
            'fcm_token' => 'dummy-fcm-token-12345'
        ]);
    }

    public function test_push_notification_fcm_terkirim_saat_bencana_baru()
    {
        Mail::fake();

        // Mock FcmService
        $mock = \Mockery::mock('App\Services\FcmService');
        $mock->shouldReceive('sendPushNotification')->once()->andReturn(true);
        $this->app->instance('App\Services\FcmService', $mock);

        $user = User::factory()->create([
            'lokasi_domisili' => 'Surabaya',
            'notif_aktif' => true,
            'email_verified_at' => now(),
        ]);

        // Simulasikan user punya FCM token
        \App\Models\UserFcmToken::create([
            'user_id' => $user->id,
            'fcm_token' => 'test-token-fcm'
        ]);

        $bencana = Bencana::factory()->create([
            'nama_bencana' => 'Gempa Surabaya',
            'lokasi' => 'Surabaya',
            'status_siaga' => 'siaga',
            'status_aktif' => true
        ]);

        // Jalankan Job secara manual
        $job = new \App\Jobs\NotifyAffectedUsersJob($bencana);
        $job->handle();
        
        $this->assertDatabaseHas('user_fcm_tokens', ['fcm_token' => 'test-token-fcm']);
    }
}
