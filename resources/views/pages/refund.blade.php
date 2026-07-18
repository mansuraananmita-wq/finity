@extends('layouts.app')

@section('title', __('ui.footer.refund'))

@section('content')
<article class="max-w-3xl">
    <h1 class="font-display text-3xl font-bold text-white">{{ __('ui.footer.refund') }}</h1>
    <div class="mt-6 space-y-4 text-slate-300 leading-relaxed">
        @if(app()->getLocale() === 'bn')
            <p>{{-- TODO: replace with final policy text --}}ফেরত ও রিফান্ড নীতির চূড়ান্ত পাঠ্য শীঘ্রই যোগ করা হবে।</p>
        @else
            <p>{{-- TODO: replace with final policy text --}}Final refund and return policy text will be added here.</p>
        @endif
    </div>
</article>
@endsection
