@push('title')
    <title>{{ $title ?? 'Tugas Saya' }} || {{ config('site.name') }}</title>
@endpush

<div class="grid-row p-6">

    @include('mrcatz::components.ui.breadcrumbs')

    <livewire:task.task-table/>

    @include('livewire.task.task_form')

    @include('livewire.task.detail_modal')

</div>

@include('mrcatz::components.ui.datatable-scripts')
