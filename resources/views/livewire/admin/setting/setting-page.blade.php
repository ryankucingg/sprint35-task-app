@push('title')
    <title>{{ $title ?? 'Pengaturan Situs' }} || {{ config('site.name') }}</title>
@endpush

<div class="p-6">
    @include('mrcatz::components.ui.breadcrumbs')

    <div class="w-full">
        @include('mrcatz::components.ui.form-standalone', [
            'submitMethod' => 'save',
            'submitLabel'  => 'Simpan Pengaturan',
            'submitIcon'   => 'save',
            'cancelUrl'    => route('admin.dashboard'),
            'cancelLabel'  => 'Kembali',
            'buttonCard'   => true,
        ])
    </div>
</div>
