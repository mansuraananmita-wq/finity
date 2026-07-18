<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class NagadService
{
    public function initializePayment(Order $order): array
    {
        $merchantId = config('nagad.merchant_id');
        $orderId = 'NAGAD-'.$order->id.'-'.Str::upper(Str::random(6));
        $amount = number_format((float) $order->total_amount, 2, '.', '');
        $challenge = Str::uuid()->toString();

        $sensitiveData = [
            'merchantId' => $merchantId,
            'datetime' => now()->format('YmdHis'),
            'orderId' => $orderId,
            'challenge' => $challenge,
        ];

        $postData = [
            'accountNumber' => $merchantId,
            'dateTime' => $sensitiveData['datetime'],
            'sensitiveData' => $this->encryptSensitiveData($sensitiveData),
            'signature' => $this->signData($sensitiveData),
        ];

        $response = Http::post(
            config('nagad.base_url').'/check-out/initialize/'.$merchantId.'/'.$orderId,
            $postData
        );

        Log::info('Nagad initialize response', [
            'order_id' => $order->id,
            'status' => $response->status(),
        ]);

        if (! $response->successful()) {
            throw new RuntimeException(__('messages.payment_init_failed'));
        }

        $data = $response->json();
        $paymentRefId = $data['paymentReferenceId'] ?? null;
        $redirectUrl = $data['callBackUrl'] ?? null;

        if (! $paymentRefId) {
            throw new RuntimeException(__('messages.payment_init_failed'));
        }

        PaymentTransaction::create([
            'order_id' => $order->id,
            'gateway' => 'nagad',
            'gateway_payment_id' => $paymentRefId,
            'amount' => $order->total_amount,
            'status' => 'initiated',
            'raw_response' => array_merge($data, ['nagad_order_id' => $orderId]),
        ]);

        $completeUrl = config('nagad.base_url').'/check-out/complete/'.$paymentRefId;

        return [
            'payment_id' => $paymentRefId,
            'redirect_url' => $redirectUrl ?: $completeUrl,
            'nagad_order_id' => $orderId,
        ];
    }

    public function verifyPayment(string $paymentRefId, string $nagadOrderId): array
    {
        $merchantId = config('nagad.merchant_id');

        $response = Http::get(
            config('nagad.base_url').'/verify/payment/'.$merchantId.'/'.$nagadOrderId
        );

        Log::info('Nagad verify response', [
            'payment_ref' => $paymentRefId,
            'status' => $response->status(),
        ]);

        if (! $response->successful()) {
            throw new RuntimeException(__('messages.payment_verification_failed'));
        }

        return $response->json();
    }

    public function verifyCallbackSignature(array $payload): bool
    {
        if (empty($payload['signature'])) {
            return false;
        }

        $expected = $this->signData([
            'merchantId' => $payload['merchantId'] ?? config('nagad.merchant_id'),
            'orderId' => $payload['orderId'] ?? '',
            'paymentRefId' => $payload['paymentRefId'] ?? '',
            'amount' => $payload['amount'] ?? '',
            'status' => $payload['status'] ?? '',
        ]);

        return hash_equals($expected, $payload['signature']);
    }

    private function encryptSensitiveData(array $data): string
    {
        $publicKey = config('nagad.pg_public_key');
        if (! $publicKey) {
            return base64_encode(json_encode($data));
        }

        $key = openssl_pkey_get_public($this->formatKey($publicKey, 'PUBLIC'));
        $encrypted = '';
        openssl_public_encrypt(json_encode($data), $encrypted, $key);

        return base64_encode($encrypted);
    }

    private function signData(array $data): string
    {
        $privateKey = config('nagad.merchant_private_key');
        if (! $privateKey) {
            return base64_encode(json_encode($data));
        }

        $key = openssl_pkey_get_private($this->formatKey($privateKey, 'PRIVATE'));
        $signature = '';
        openssl_sign(json_encode($data), $signature, $key, OPENSSL_ALGO_SHA256);

        return base64_encode($signature);
    }

    private function formatKey(string $key, string $type): string
    {
        if (str_contains($key, 'BEGIN')) {
            return $key;
        }

        $wrapped = chunk_split($key, 64, "\n");

        return "-----BEGIN {$type} KEY-----\n{$wrapped}-----END {$type} KEY-----";
    }
}
