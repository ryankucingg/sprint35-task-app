<?php

namespace App\Livewire\Admin\Dashboard;

use MrCatz\DataTable\MrCatzComponent;

class DashboardPage extends MrCatzComponent
{
    public function mount()
    {
        $this->setTitle('Dashboard');
        session()->put('active', 'admin-dashboard');
        $this->breadcrumbs = [
            ['title' => 'Dashboard', 'url' => null]
        ];
    }

    public function render()
    {
        return view('livewire.admin.dashboard.dashboard-page')->layout('components.layouts.admin_layout');
    }
}
