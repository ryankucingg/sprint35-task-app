<div>

    @extends('mrcatz::components.ui.datatable-form')

    @section('forms')

        <x-ui.input id="name"
                     placeholder="Nama Lengkap"
                     message="{{ $errors->first('name') }}"
                     input_type="text"
                     icon="person">
        </x-ui.input>

        <x-ui.input id="username"
                     placeholder="Username"
                     message="{{ $errors->first('username') }}"
                     disabled="{{ $isEdit ? 'true' : 'false' }}"
                     input_type="text"
                     icon="alternate_email">
        </x-ui.input>

        <x-ui.input id="email"
                     placeholder="Email"
                     message="{{ $errors->first('email') }}"
                     input_type="email"
                     icon="mail">
        </x-ui.input>

        <x-ui.select id="role"
                      :data="[['value' => 'user', 'label' => 'User'], ['value' => 'admin', 'label' => 'Admin']]"
                      value="value"
                      option="label"
                      label="Role"
                      message="{{ $errors->first('role') }}">
        </x-ui.select>

        <x-ui.input id="password"
                     placeholder="{{ $isEdit ? 'Password (kosongkan jika tidak diubah)' : 'Password' }}"
                     message="{{ $errors->first('password') }}"
                     input_type="password"
                     icon="lock">
        </x-ui.input>

        <x-ui.input id="password_confirmation"
                     placeholder="Konfirmasi Password"
                     message="{{ $errors->first('password_confirmation') }}"
                     input_type="password"
                     icon="lock">
        </x-ui.input>

    @endsection

</div>
