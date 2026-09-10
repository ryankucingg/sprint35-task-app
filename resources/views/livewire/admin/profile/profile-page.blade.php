@push('title')
    <title>{{ $title }} || {{ config('app.name') }}</title>
@endpush

<div class="p-6">
    @include('mrcatz::components.ui.breadcrumbs')

    <div class="w-full">
        @include('mrcatz::components.ui.form-standalone', [
            'submitMethod' => 'save',
            'submitLabel'  => 'Simpan Perubahan',
            'submitIcon'   => 'check_circle',
            'cancelUrl'    => route('admin.dashboard'),
            'cancelLabel'  => 'Kembali',
            'buttonCard'   => true,
        ])
    </div>
</div>
