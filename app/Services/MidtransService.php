<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    protected string $serverKey;

    protected string $clientKey;

    protected bool $isProduction;

    protected string $snapUrl;

    protected string $apiUrl;

    public function __construct()
    {
        $this->serverKey = (string) config('midtrans.server_key', '');
        $this->clientKey = (string) config('midtrans.client_key', '');
        $this->isProduction = (bool) config('midtrans.is_production', false);
        $this->snapUrl = (string) config('midtrans.snap_url', 'https://app.sandbox.midtrans.com/snap/v1/transactions');
        $this->apiUrl = (string) config('midtrans.api_url', 'https://api.sandbox.midtrans.com');
    }

    /**
     * Create Midtrans Snap Transaction and obtain token & redirect URL.
     *
     * @param  array{first_name?: string, email?: string, phone?: string}  $customerDetails
     * @param  array<int, array{id: string, price: int, quantity: int, name: string}>  $itemDetails
     * @return array{token: string, redirect_url: string}
     */
    public function createSnapTransaction(string $orderId, int $grossAmount, array $customerDetails = [], array $itemDetails = []): array
    {
        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => $customerDetails,
            'item_details' => $itemDetails,
            'callbacks' => [
                'finish' => url('/pesanan'),
            ],
        ];

        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->timeout(15)
                ->post($this->snapUrl, $payload);

            if (! $response->successful()) {
                Log::error('Midtrans Snap Error', [
                    'order_id' => $orderId,
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);

                throw new Exception('Gagal membuat transaksi ke Midtrans: '.($response->json('error_messages.0') ?? $response->body()));
            }

            $data = $response->json();

            return [
                'token' => $data['token'] ?? '',
                'redirect_url' => $data['redirect_url'] ?? '',
            ];
        } catch (Exception $e) {
            Log::error('Midtrans Exception: '.$e->getMessage(), ['order_id' => $orderId]);
            throw $e;
        }
    }

    /**
     * Verify Midtrans callback SHA512 signature.
     */
    public function isValidSignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        // Formula: SHA512(order_id + status_code + gross_amount + ServerKey)
        $expectedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$this->serverKey);

        return hash_equals($expectedSignature, $signatureKey);
    }

    /**
     * Query Midtrans payment status directly from Midtrans REST API.
     *
     * @return array<string, mixed>|null
     */
    public function getTransactionStatus(string $orderId): ?array
    {
        try {
            $url = "{$this->apiUrl}/v2/{$orderId}/status";
            $response = Http::withBasicAuth($this->serverKey, '')
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->timeout(10)
                ->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (Exception $e) {
            Log::warning('Midtrans Status Query Failed', ['order_id' => $orderId, 'error' => $e->getMessage()]);

            return null;
        }
    }
}
