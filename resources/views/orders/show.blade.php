@extends('layouts.app')

@section('title', '#'.$order->id)

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <h1 class="font-display text-3xl font-bold text-white">Order #{{ $order->id }}</h1>
    <span class="rounded-full bg-ocean-light/40 px-4 py-1 text-sm">{{ __("messages.status_{$order->status}") }}</span>
</div>

{{-- Status tracker --}}
@php
    $steps = ['pending', 'processing', 'shipped', 'delivered'];
    $currentIdx = array_search($order->status, $steps);
    if ($order->status === 'cancelled') $currentIdx = -1;
@endphp
@if($order->status !== 'cancelled')
<ol class="mb-10 flex justify-between gap-2">
    @foreach($steps as $i => $step)
        <li class="flex flex-1 flex-col items-center text-center">
            <span class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold {{ $i <= $currentIdx ? 'bg-coral-accent text-ocean-dark' : 'bg-ocean-light/30 text-slate-500' }}">{{ $i + 1 }}</span>
            <span class="mt-2 hidden text-xs text-slate-400 sm:block">{{ __("messages.status_{$step}") }}</span>
        </li>
        @if(!$loop->last)<div class="mt-4 h-0.5 flex-1 {{ $i < $currentIdx ? 'bg-coral-accent' : 'bg-ocean-light/30' }}"></div>@endif
    @endforeach
</ol>
@endif

<div class="grid gap-8 lg:grid-cols-3">
    <div class="lg:col-span-2 space-y-6">
        <div class="card-ocean p-6">
            <h2 class="font-semibold text-white">{{ __('ui.labels.items') }}</h2>
            <ul class="mt-4 divide-y divide-ocean-light/10">
                @foreach($order->items as $item)
                    <li class="flex justify-between gap-4 py-3">
                        <div class="flex gap-3">
                            <img src="{{ $item->product->image_path }}" alt="" class="h-14 w-14 rounded-lg object-cover">
                            <div>
                                <a href="{{ route('products.show', $item->product) }}" class="font-medium hover:text-coral-accent">{{ $item->product->name }}</a>
                                <p class="text-sm text-slate-400">× {{ $item->quantity }}</p>
                                @if($order->status !== 'cancelled')
                                    <a href="{{ route('products.show', $item->product) }}#reviews" class="text-xs text-coral-accent hover:underline">Write a review</a>
                                @endif
                            </div>
                        </div>
                        <span class="text-coral-accent">৳{{ number_format($item->price_at_purchase * $item->quantity, 0) }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="card-ocean p-6 text-sm">
            <h2 class="font-semibold text-white">{{ __('ui.labels.shipping') }}</h2>
            <p class="mt-2 text-slate-300">{{ $order->shipping_address }}</p>
            <p class="text-slate-400">{{ $order->shipping_district }} · {{ $order->phone }}</p>
            @if($order->notes)<p class="mt-2 text-slate-500">{{ $order->notes }}</p>@endif
        </div>
    </div>
    <div class="card-ocean h-fit p-6">
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-slate-400">{{ __('ui.labels.subtotal') }}</dt><dd>৳{{ number_format($order->subtotal, 0) }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">{{ __('ui.labels.shipping') }}</dt><dd>৳{{ number_format($order->shipping_cost, 0) }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">{{ __('ui.labels.discount') }}</dt><dd>৳{{ number_format($order->discount_amount, 0) }}</dd></div>
            <div class="flex justify-between border-t border-ocean-light/20 pt-3 text-lg font-bold text-coral-accent">
                <dt>{{ __('ui.labels.total') }}</dt><dd>৳{{ number_format($order->total_amount, 0) }}</dd>
            </div>
        </dl>
        <p class="mt-4 text-sm text-slate-400">{{ __('ui.labels.payment_method') }}: <span class="text-white">{{ strtoupper($order->payment_method) }}</span></p>
        <p class="text-sm text-slate-400">{{ __('ui.labels.payment_status') }}: <span class="text-white">{{ $order->payment_status }}</span></p>
        @if($order->payment_status === 'failed' && in_array($order->payment_method, ['bkash', 'nagad']))
            <a href="{{ route('payment.'.$order->payment_method.'.initiate', $order) }}" class="btn-coral mt-6 block text-center text-sm">{{ __('messages.retry_payment') }}</a>
        @endif
    </div>
</div>
@endsection
