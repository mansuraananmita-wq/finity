@if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
         class="fixed right-4 top-20 z-50 max-w-sm animate-fade-in rounded-xl border border-emerald-500/40 bg-emerald-950/90 px-4 py-3 text-sm text-emerald-100 shadow-glow backdrop-blur">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
         class="fixed right-4 top-20 z-50 max-w-sm animate-fade-in rounded-xl border border-red-500/40 bg-red-950/90 px-4 py-3 text-sm text-red-100 shadow-glow backdrop-blur">
        {{ session('error') }}
    </div>
@endif

@if(isset($errors) && $errors->any() && !request()->routeIs('checkout.*', 'login', 'register'))
    <div class="mx-auto mb-6 max-w-7xl rounded-xl border border-red-500/30 bg-red-950/40 px-4 py-3 text-sm text-red-200">
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<div id="toast-root" class="pointer-events-none fixed right-4 top-20 z-50 flex w-full max-w-sm flex-col gap-2"></div>
