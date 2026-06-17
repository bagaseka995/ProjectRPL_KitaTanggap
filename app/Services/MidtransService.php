<?php

namespace App\Services;

use App\Models\Donasi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    private string $serverKey;
    private string $apiUrl;
    private bool $isProduction;

    public function __construct()
    {
        $this->serverKey    = config('midtrans.server_key');
        $this->apiUrl       = config('midtrans.api_url');
        $this->isProduction = config('midtrans.is_production');
    }

    /**
     * Buat Snap Token dari Midtrans.
     * Mengirim payload ke Snap API dan mengembalikan token untuk popup frontend.
     *
     * @param Donasi $donasi
     * @return string snap_token
     * @throws \Exception
     */
    public function createSnapToken(Donasi $donasi): string
    {
        $payload = [
            'transaction_details' => [
                'order_id'     => $donasi->kode_transaksi,
                'gross_amount' => (int) $donasi->nominal,
            ],
            'customer_details' => [
                'first_name' => $donasi->nama_donatur,
                'email'      => $donasi->email_donatur,
            ],
            'item_details' => [
                [
                    'id'       => 'DONASI-' . $donasi->bencana_id,
                    'price'    => (int) $donasi->nominal,
                    'quantity' => 1,
                    'name'     => 'Donasi: ' . ($donasi->bencana->nama_bencana ?? 'Bencana'),
                ],
            ],
            'enabled_payments' => config('midtrans.enabled_payments'),
            'credit_card' => [
                'secure' => config('midtrans.is_3ds'),
            ],
        ];

        $response = Http::withBasicAuth($this->serverKey, '')
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ])
            ->post($this->apiUrl, $payload);

        if ($response->failed()) {
            Log::error('Midtrans Snap API error', [
                'status'   => $response->status(),
                'body'     => $response->body(),
                'order_id' => $donasi->kode_transaksi,
            ]);
            throw new \Exception('Gagal membuat transaksi Midtrans: ' . ($response->json('error_messages.0') ?? $response->body()));
        }

        $snapToken = $response->json('token');

        if (!$snapToken) {
            throw new \Exception('Snap token tidak ditemukan dalam respons Midtrans.');
        }

        return $snapToken;
    }

    /**
     * Verifikasi signature Midtrans notification callback.
     *
     * @param array $notification
     * @return bool
     */
    public function verifySignature(array $notification): bool
    {
        $orderId     = $notification['order_id'] ?? '';
        $statusCode  = $notification['status_code'] ?? '';
        $grossAmount = $notification['gross_amount'] ?? '';
        $serverKey   = $this->serverKey;

        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        $receivedSignature = $notification['signature_key'] ?? '';

        return hash_equals($expectedSignature, $receivedSignature);
    }

    /**
     * Dapatkan status transaksi langsung dari Midtrans API.
     *
     * @param string $orderId
     * @return array
     * @throws \Exception
     */
    public function getTransactionStatus(string $orderId): array
    {
        $baseUrl = $this->isProduction
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';

        $response = Http::withBasicAuth($this->serverKey, '')
            ->withHeaders([
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->get("{$baseUrl}/{$orderId}/status");

        if ($response->failed()) {
            Log::error('Midtrans Status API error', [
                'status'   => $response->status(),
                'body'     => $response->body(),
                'order_id' => $orderId,
            ]);
            throw new \Exception('Gagal mendapatkan status transaksi Midtrans.');
        }

        return $response->json();
    }

    /**
     * Mapping status Midtrans ke status internal aplikasi.
     *
     * @param string $transactionStatus
     * @param string $fraudStatus
     * @return string 'sukses'|'pending'|'gagal'
     */
    public function mapTransactionStatus(string $transactionStatus, string $fraudStatus = 'accept'): string
    {
        if ($transactionStatus === 'capture') {
            return $fraudStatus === 'accept' ? 'sukses' : 'gagal';
        }

        return match ($transactionStatus) {
            'settlement' => 'sukses',
            'pending'    => 'pending',
            'deny', 'cancel', 'expire' => 'gagal',
            default      => 'pending',
        };
    }
}
