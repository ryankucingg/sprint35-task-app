@push('title')
    <title>{{ $title ?? 'Semua Tugas' }} || {{ config('site.name') }}</title>
@endpush

<div class="grid-row p-6">

    @include('mrcatz::components.ui.breadcrumbs')

    <livewire:admin.alltask.alltask-table/>

    @include('livewire.admin.alltask.alltask_form')

</div>

@include('mrcatz::components.ui.datatable-scripts')
