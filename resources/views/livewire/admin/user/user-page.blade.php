@push('title')
    <title>{{ $title ?? 'User Management' }} || {{ config('site.name') }}</title>
@endpush

<div class="grid-row p-6">

    @include('mrcatz::components.ui.breadcrumbs')

    <livewire:admin.user.user-table/>

    @include('livewire.admin.user.user_form')

</div>

@include('mrcatz::components.ui.datatable-scripts')
