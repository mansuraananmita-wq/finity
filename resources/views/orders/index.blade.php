@extends('layouts.app')

@section('title', __('ui.nav.orders'))

@section('content')
<h1 class="font-display text-3xl font-bold text-white">{{ __('ui.nav.orders') }}</h1>

@if($orders->isEmpty())
    <p class="mt-8 text-slate-400">{{ __('ui.labels.no_orders') }}</p>
@else
    <div class="mt-8 space-y-4">
        @foreach($orders as $order)
            @php
                $statusColors = [
                    'pending' => 'bg-amber-500/20 text-amber-300',
                    'processing' => 'bg-blue-500/20 text-blue-300',
                    'shipped' => 'bg-purple-500/20 text-purple-300',
                    'delivered' => 'bg-emerald-500/20 text-emerald-300',
                    'cancelled' => 'bg-red-500/20 text-red-300',
                ];
            @endphp
            <a href="{{ route('orders.show', $order) }}" class="card-ocean flex flex-col gap-3 p-4 transition hover:border-coral-accent/40 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <span class="font-semibold text-white">#{{ $order->id }}</span>
                    <span class="ml-3 rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColors[$order->status] ?? '' }}">
                        {{ __("messages.status_{$order->status}") }}
                    </span>
                    <p class="mt-1 text-sm text-slate-400">{{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-lg font-bold text-coral-accent">৳{{ number_format($order->total_amount, 0) }}</p>
                    <p class="text-xs text-slate-500">{{ $order->items_count }} {{ __('ui.labels.items') }}</p>
                </div>
            </a>
        @endforeach
    </div>
    <div class="mt-8">{{ $orders->links() }}</div>
@endif
@endsection
