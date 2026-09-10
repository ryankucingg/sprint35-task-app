<div>

    @extends('mrcatz::components.ui.datatable-form')

    @section('forms')

        <x-ui.input id="name"
                     placeholder="Nama Kategori"
                     message="{{ $errors->first('name') }}"
                     input_type="text"
                     icon="category">
        </x-ui.input>

    @endsection

</div>
