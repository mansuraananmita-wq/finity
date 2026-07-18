@extends('layouts.app')

@section('title', __('ui.footer.privacy'))

@section('content')
<article class="max-w-3xl">
    <h1 class="font-display text-3xl font-bold text-white">{{ __('ui.footer.privacy') }}</h1>
    <div class="mt-6 space-y-4 text-slate-300 leading-relaxed">
        @if(app()->getLocale() === 'bn')
            <p>{{-- TODO: replace with final policy text --}}গোপনীয়তা নীতির চূড়ান্ত পাঠ্য শীঘ্রই যোগ করা হবে। আপনার ব্যক্তিগত তথ্য (নাম, ফোন, ঠিকানা) শুধুমাত্র অর্ডার প্রক্রিয়াকরণ ও ডেলিভারির জন্য ব্যবহার করা হয়।</p>
        @else
            <p>{{-- TODO: replace with final policy text --}}Final privacy policy text will be added here. Your personal data (name, phone, address) is used only for order processing and delivery.</p>
        @endif
    </div>
</article>
@endsection
