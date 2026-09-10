@php
    $userOptions = \App\Models\User::where('role', 'user')->orderBy('name')->get()
        ->map(fn ($u) => ['value' => $u->id, 'label' => $u->name])
        ->all();

    $categoryOptions = \App\Models\Category::orderBy('name')->get()
        ->map(fn ($c) => ['value' => $c->id, 'label' => $c->name])
        ->prepend(['value' => '', 'label' => 'Tanpa Kategori'])
        ->all();

    $priorityOptions = collect(\App\Models\Task::PRIORITIES)
        ->map(fn ($label, $value) => ['value' => $value, 'label' => $label])
        ->values()->all();

    $statusOptions = collect(\App\Models\Task::STATUSES)
        ->map(fn ($label, $value) => ['value' => $value, 'label' => $label])
        ->values()->all();
@endphp

<div>

    @extends('mrcatz::components.ui.datatable-form')

    @section('forms')

        <x-ui.select id="user_id"
                      :data="$userOptions"
                      value="value"
                      option="label"
                      label="Pengguna"
                      message="{{ $errors->first('user_id') }}">
        </x-ui.select>

        <x-ui.input id="title"
                     placeholder="Judul Tugas"
                     message="{{ $errors->first('title') }}"
                     input_type="text"
                     icon="edit_note">
        </x-ui.input>

        <x-ui.textarea id="description"
                        label="Deskripsi"
                        placeholder="Rincian tugas (opsional)"
                        message="{{ $errors->first('description') }}">
        </x-ui.textarea>

        <x-ui.select id="category_id"
                      :data="$categoryOptions"
                      value="value"
                      option="label"
                      label="Kategori"
                      message="{{ $errors->first('category_id') }}">
        </x-ui.select>

        <x-ui.select id="priority"
                      :data="$priorityOptions"
                      value="value"
                      option="label"
                      label="Prioritas"
                      message="{{ $errors->first('priority') }}">
        </x-ui.select>

        <x-ui.select id="status"
                      :data="$statusOptions"
                      value="value"
                      option="label"
                      label="Status"
                      message="{{ $errors->first('status') }}">
        </x-ui.select>

        <x-ui.input id="due_date"
                     placeholder="Tenggat Waktu"
                     message="{{ $errors->first('due_date') }}"
                     input_type="date"
                     icon="event">
        </x-ui.input>

    @endsection

</div>
