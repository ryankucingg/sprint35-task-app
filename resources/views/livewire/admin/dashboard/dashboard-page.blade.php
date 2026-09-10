@push('title')
    <title>Dashboard - {{ config('site.name') }}</title>
@endpush

<div class="grid-row p-6">

    @include('mrcatz::components.ui.breadcrumbs')

    <div class="flex items-center justify-center min-h-[50vh]">
        <div class="text-center">
            <div class="w-20 h-20 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <span class="material-icons text-primary" style="font-size: 40px;">verified_user</span>
            </div>
            <h2 class="text-2xl font-bold text-base-content mb-2">Ini adalah halaman admin</h2>
            <p class="text-base-content/50 mb-4">
                Selamat datang, <span class="font-semibold text-primary">{{ auth()->user()->name }}</span>!
                Anda login sebagai <span class="badge badge-sm badge-primary text-white uppercase">{{ str_replace('-', ' ', auth()->user()->role) }}</span>
            </p>
            <p class="text-base-content/40 text-sm">Halaman ini akan dikembangkan lebih lanjut.</p>
        </div>
    </div>

</div>
