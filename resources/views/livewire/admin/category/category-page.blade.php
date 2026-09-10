@push('title')
    <title>{{ $title ?? 'Kategori' }} || {{ config('site.name') }}</title>
@endpush

<div class="grid-row p-6">

    @include('mrcatz::components.ui.breadcrumbs')

    <livewire:admin.category.category-table/>

    @include('livewire.admin.category.category_form')

</div>

@include('mrcatz::components.ui.datatable-scripts')
