<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Notifications\OrderPlacedNotification;
use App\Notifications\PaymentStatusNotification;
use App\Services\BkashService;
use App\Services\NagadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        private BkashService $bkashService,
        private NagadService $nagadService,
    ) {
        $this->middleware('auth')->except(['bkashCallback', 'nagadCallback']);
    }

    public function bkashInitiate(Order $order): RedirectResponse
    {
        $this->authorizeOrder($order);

        if ($order->payment_method !== 'bkash' || $order->payment_status === 'paid') {
            return redirect()->route('orders.show', $order);
        }

        try {
            $payment = $this->bkashService->createPayment($order);

            return redirect()->away($payment['redirect_url']);
        } catch (\Throwable $e) {
            Log::error('bKash initiate failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);

            return redirect()->route('orders.show', $order)
                ->withErrors(['payment' => __('messages.payment_init_failed')]);
        }
    }

    public function bkashCallback(Request $request): RedirectResponse
    {
        $paymentId = $request->query('paymentID');
        if (! $paymentId) {
            return redirect()->route('orders.index')->withErrors(['payment' => __('messages.payment_verification_failed')]);
        }

        $transaction = PaymentTransaction::where('gateway_payment_id', $paymentId)
            ->where('gateway', 'bkash')
            ->first();

        if (! $transaction) {
            return redirect()->route('orders.index')->withErrors(['payment' => __('messages.payment_verification_failed')]);
        }

        try {
            $result = $this->bkashService->executePayment($paymentId);
            $status = $result['transactionStatus'] ?? $result['statusMessage'] ?? '';

            if (in_array($status, ['Completed', 'Success'], true)) {
                $this->markOrderPaid($transaction, $result['trxID'] ?? null, $result);

                return redirect()->route('orders.show', $transaction->order)
                    ->with('success', __('messages.payment_success'));
            }

            $this->markOrderFailed($transaction, $result);

            return redirect()->route('orders.show', $transaction->order)
                ->withErrors(['payment' => __('messages.payment_failed')]);
        } catch (\Throwable $e) {
            Log::error('bKash callback failed', ['payment_id' => $paymentId, 'error' => $e->getMessage()]);
            $this->markOrderFailed($transaction, ['error' => $e->getMessage()]);

            return redirect()->route('orders.show', $transaction->order)
                ->withErrors(['payment' => __('messages.payment_verification_failed')]);
        }
    }

    public function bkashExecute(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOrder($order);
        $paymentId = $request->input('paymentID');

        if (! $paymentId) {
            return redirect()->route('orders.show', $order)
                ->withErrors(['payment' => __('messages.payment_verification_failed')]);
        }

        return $this->bkashCallback($request);
    }

    public function nagadInitiate(Order $order): RedirectResponse
    {
        $this->authorizeOrder($order);

        if ($order->payment_method !== 'nagad' || $order->payment_status === 'paid') {
            return redirect()->route('orders.show', $order);
        }

        try {
            $payment = $this->nagadService->initializePayment($order);

            return redirect()->away($payment['redirect_url']);
        } catch (\Throwable $e) {
            Log::error('Nagad initiate failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);

            return redirect()->route('orders.show', $order)
                ->withErrors(['payment' => __('messages.payment_init_failed')]);
        }
    }

    public function nagadCallback(Request $request): RedirectResponse
    {
        $paymentRefId = $request->input('payment_ref_id') ?? $request->input('paymentRefId');
        $orderId = $request->input('order_id') ?? $request->input('orderId');
        $status = $request->input('status');

        $transaction = PaymentTransaction::where('gateway_payment_id', $paymentRefId)
            ->where('gateway', 'nagad')
            ->first();

        if (! $transaction) {
            return redirect()->route('orders.index')->withErrors(['payment' => __('messages.payment_verification_failed')]);
        }

        try {
            $nagadOrderId = $transaction->raw_response['nagad_order_id'] ?? $orderId;
            $verifyResult = $this->nagadService->verifyPayment($paymentRefId, $nagadOrderId);

            if (! $this->nagadService->verifyCallbackSignature($request->all())) {
                Log::warning('Nagad callback signature mismatch', ['payment_ref' => $paymentRefId]);
            }

            $verifiedStatus = $verifyResult['status'] ?? $status;

            if (in_array($verifiedStatus, ['Success', 'success', 'SuccessFull'], true)) {
                $this->markOrderPaid(
                    $transaction,
                    $verifyResult['issuerPaymentRefNo'] ?? $paymentRefId,
                    $verifyResult
                );

                return redirect()->route('orders.show', $transaction->order)
                    ->with('success', __('messages.payment_success'));
            }

            $this->markOrderFailed($transaction, $verifyResult);

            return redirect()->route('orders.show', $transaction->order)
                ->withErrors(['payment' => __('messages.payment_failed')]);
        } catch (\Throwable $e) {
            Log::error('Nagad callback failed', ['payment_ref' => $paymentRefId, 'error' => $e->getMessage()]);
            $this->markOrderFailed($transaction, ['error' => $e->getMessage()]);

            return redirect()->route('orders.show', $transaction->order)
                ->withErrors(['payment' => __('messages.payment_verification_failed')]);
        }
    }

    private function markOrderPaid(PaymentTransaction $transaction, ?string $trxId, array $raw): void
    {
        DB::transaction(function () use ($transaction, $trxId, $raw) {
            $transaction->refresh();
            if ($transaction->status === 'success') {
                return;
            }

            $transaction->update([
                'status' => 'success',
                'gateway_trx_id' => $trxId,
                'raw_response' => $raw,
            ]);

            $order = $transaction->order()->lockForUpdate()->first();
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
            ]);

            $order->user->notify(new OrderPlacedNotification($order));
            $order->user->notify(new PaymentStatusNotification($order, true));
        });
    }

    private function markOrderFailed(PaymentTransaction $transaction, array $raw): void
    {
        DB::transaction(function () use ($transaction, $raw) {
            $transaction->update([
                'status' => 'failed',
                'raw_response' => $raw,
            ]);

            $order = $transaction->order;
            $order->update(['payment_status' => 'failed']);
            $order->user->notify(new PaymentStatusNotification($order, false));
        });
    }

    private function authorizeOrder(Order $order): void
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
