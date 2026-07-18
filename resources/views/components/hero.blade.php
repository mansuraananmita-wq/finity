{{-- Hero video: resources/video/hero-fish.mp4 (served from /video/hero-fish.mp4) --}}
<section class="relative flex min-h-[100svh] items-center justify-center overflow-hidden">
    {{-- Poster / reduced-motion / <480px fallback (see app.css) --}}
    <img src="/images/hero-poster.jpg" alt="" class="absolute inset-0 h-full w-full object-cover" aria-hidden="true">

    <video id="hero-video"
           class="hero-video absolute inset-0 h-full w-full object-cover"
           autoplay muted loop playsinline
           poster="/images/hero-poster.jpg">
        <source src="/video/hero-fish.mp4" type="video/mp4">
    </video>

    <div class="hero-overlay absolute inset-0 z-10"></div>

    {{-- Ambient bubbles --}}
    <div class="pointer-events-none absolute inset-0 z-20 overflow-hidden" aria-hidden="true">
        @foreach([10, 25, 40, 60, 75, 88] as $i => $left)
            <span class="bubble-particle absolute bottom-0 h-3 w-3 rounded-full bg-white/20"
                  style="left: {{ $left }}%; animation-delay: {{ $i * 1.2 }}s; animation-duration: {{ 7 + $i }}s;"></span>
        @endforeach
    </div>

    <div class="relative z-30 mx-auto max-w-4xl px-4 text-center animate-fade-in">
        <p class="font-display text-sm uppercase tracking-[0.3em] text-coral-accent/90 sm:text-base">
            {{ __('ui.hero.subtitle') }}
        </p>
        <h1 class="mt-4 font-display text-4xl font-bold leading-tight text-white sm:text-5xl md:text-6xl">
            {{ __('ui.hero.title') }}
        </h1>
        <p class="mx-auto mt-5 max-w-2xl text-base text-slate-200 sm:text-lg">
            {{ __('ui.hero.tagline') }}
        </p>
        <div class="mt-8 flex flex-col items-center justify-center gap-4 sm:flex-row">
            <a href="{{ route('products.index') }}" class="btn-coral text-base">
                {{ __('ui.hero.cta') }}
            </a>
            <a href="{{ route('pages.about') }}" class="btn-ocean">
                {{ __('ui.footer.about') }}
            </a>
        </div>
    </div>

    <a href="#featured" class="absolute bottom-8 left-1/2 z-30 -translate-x-1/2 text-slate-400 transition hover:text-coral-accent" aria-label="Scroll">
        <svg class="h-8 w-8 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
    </a>
</section>
