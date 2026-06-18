<?php

namespace App\Jobs;

use App\Mail\BencanaAlert;
use App\Models\Bencana;
use App\Models\User;
use App\Services\FcmService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class NotifyAffectedUsersJob implements ShouldQueue
{
    use Queueable;

    public $bencana;

    /**
     * Create a new job instance.
     */
    public function __construct(Bencana $bencana)
    {
        $this->bencana = $bencana;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->bencana || !$this->bencana->lokasi) {
            return;
        }

        // Cari user yang berada di area lokasi_domisili sesuai lokasi bencana
        $users = User::whereNotNull('email_verified_at')
            ->where('notif_aktif', true)
            ->where('lokasi_domisili', 'LIKE', '%' . $this->bencana->lokasi . '%')
            ->get();

        $fcmService = app(FcmService::class);

        foreach ($users as $user) {
            Mail::to($user->email)->send(new BencanaAlert($this->bencana));

            // Kirim notifikasi FCM jika user memiliki token
            foreach ($user->fcmTokens as $fcmToken) {
                $fcmService->sendPushNotification(
                    $fcmToken->fcm_token,
                    "Peringatan Dini: " . $this->bencana->nama_bencana,
                    "Status: " . strtoupper($this->bencana->status_siaga) . " di " . $this->bencana->lokasi,
                    ['click_action' => url('/donasi/' . $this->bencana->id)]
                );
            }
        }
    }
}
