@extends('layouts.app')

@section('title', __('ui.footer.about'))

@section('content')
<article class="max-w-3xl">
    <h1 class="font-display text-3xl font-bold text-white">{{ __('ui.footer.about') }}</h1>
    <div class="mt-6 space-y-4 text-slate-300 leading-relaxed">
        @if(app()->getLocale() === 'bn')
            <p><!-- TODO: replace with final policy text --> Finity হল বাংলাদেশের একটি প্রিমিয়াম অর্নামেন্টাল মাছের অনলাইন স্টোর। আমরা সারা দেশে স্বাস্থ্যকর, সুন্দর মাছ সরবরাহ করি।</p>
        @else
            <p><!-- TODO: replace with final policy text --> Finity is a premium ornamental fish online store in Bangladesh. We deliver healthy, beautiful fish nationwide.</p>
        @endif
    </div>
</article>
@endsection
