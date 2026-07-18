<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class BkashService
{
    private const TOKEN_CACHE_KEY = 'bkash_grant_token';

    public function grantToken(): string
    {
        if ($token = Cache::get(self::TOKEN_CACHE_KEY)) {
            return $token;
        }

        $response = Http::withHeaders([
            'username' => config('bkash.username'),
            'password' => config('bkash.password'),
        ])->post(config('bkash.base_url').'/tokenized/checkout/token/grant', [
            'app_key' => config('bkash.app_key'),
            'app_secret' => config('bkash.app_secret'),
        ]);

        Log::info('bKash grantToken response', ['status' => $response->status()]);

        if (! $response->successful()) {
            throw new RuntimeException(__('messages.payment_init_failed'));
        }

        $data = $response->json();
        $token = $data['id_token'] ?? $data['token'] ?? null;
        $expiresIn = (int) ($data['expires_in'] ?? 3600);

        if (! $token) {
            throw new RuntimeException(__('messages.payment_init_failed'));
        }

        Cache::put(self::TOKEN_CACHE_KEY, $token, max(60, $expiresIn - 60));

        return $token;
    }

    public function createPayment(Order $order): array
    {
        $token = $this->grantToken();

        $response = Http::withHeaders([
            'Authorization' => $token,
            'X-APP-Key' => config('bkash.app_key'),
        ])->post(config('bkash.base_url').'/tokenized/checkout/create', [
            'mode' => '0011',
            'payerReference' => (string) $order->user_id,
            'callbackURL' => route('payment.bkash.callback'),
            'amount' => number_format((float) $order->total_amount, 2, '.', ''),
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => 'ORDER-'.$order->id,
        ]);

        Log::info('bKash createPayment response', [
            'order_id' => $order->id,
            'status' => $response->status(),
        ]);

        if (! $response->successful()) {
            throw new RuntimeException(__('messages.payment_init_failed'));
        }

        $data = $response->json();
        $paymentId = $data['paymentID'] ?? null;
        $bkashUrl = $data['bkashURL'] ?? null;

        if (! $paymentId || ! $bkashUrl) {
            throw new RuntimeException(__('messages.payment_init_failed'));
        }

        PaymentTransaction::create([
            'order_id' => $order->id,
            'gateway' => 'bkash',
            'gateway_payment_id' => $paymentId,
            'amount' => $order->total_amount,
            'status' => 'initiated',
            'raw_response' => $data,
        ]);

        return [
            'payment_id' => $paymentId,
            'redirect_url' => $bkashUrl,
        ];
    }

    public function executePayment(string $paymentId): array
    {
        $token = $this->grantToken();

        $response = Http::withHeaders([
            'Authorization' => $token,
            'X-APP-Key' => config('bkash.app_key'),
        ])->post(config('bkash.base_url').'/tokenized/checkout/execute', [
            'paymentID' => $paymentId,
        ]);

        Log::info('bKash executePayment response', [
            'payment_id' => $paymentId,
            'status' => $response->status(),
        ]);

        if (! $response->successful()) {
            throw new RuntimeException(__('messages.payment_verification_failed'));
        }

        return $response->json();
    }

    public function queryPayment(string $paymentId): array
    {
        $token = $this->grantToken();

        $response = Http::withHeaders([
            'Authorization' => $token,
            'X-APP-Key' => config('bkash.app_key'),
        ])->post(config('bkash.base_url').'/tokenized/checkout/payment/status', [
            'paymentID' => $paymentId,
        ]);

        Log::info('bKash queryPayment response', [
            'payment_id' => $paymentId,
            'status' => $response->status(),
        ]);

        if (! $response->successful()) {
            throw new RuntimeException(__('messages.payment_verification_failed'));
        }

        return $response->json();
    }
}
