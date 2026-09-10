<?php

namespace App\Livewire\Admin\AllTask;

use MrCatz\DataTable\MrCatzComponent;

class AllTaskPage extends MrCatzComponent
{
    public function mount()
    {
        $this->setTitle('Semua Tugas');
        session()->put('active', 'admin-all-tasks');
        $this->breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['title' => 'Semua Tugas', 'url' => null],
        ];
    }

    public function render()
    {
        return view('livewire.admin.alltask.alltask-page')->layout('components.layouts.admin_layout');
    }
}
