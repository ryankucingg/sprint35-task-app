<?php

namespace App\Livewire\Admin\User;

use MrCatz\DataTable\MrCatzDataTableFilter;
use MrCatz\DataTable\MrCatzDataTables;
use MrCatz\DataTable\MrCatzDataTablesComponent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserTable extends MrCatzDataTablesComponent
{
    public $showSearch = true;
    public $showAddButton = true;
    public $bulkPrimaryKey = 'id';
    public $showBulkButton = true;
    public $expandableRows = 'both';
    public $exportTitle = 'Data User';

    public function configTable()
    {
        return [
            'table_name' => 'users',
            'table_id' => 'id'
        ];
    }

    public function baseQuery()
    {
        return DB::table('users');
    }

    public function setTable()
    {
        return $this->CreateMrCatzTable()
            ->enableBulk(function ($data, $i) {
                return Auth::id() !== $data->id;
            })
            ->enableExpand(function ($data, $i) {
                return MrCatzDataTables::getExpandView($data, [
                    'Email' => 'email',
                    'Username' => 'username',
                    'Foto' => [
                        'type' => 'image',
                        'key' => 'avatar',
                        'width' => 64,
                        'height' => 64,
                        'previewClass' => 'rounded-lg shadow-sm',
                        'fallback' => 'name',
                    ],
                    'Dibuat' => 'created_at',
                    'Diperbarui' => 'updated_at',
                    'Dokumen' => [
                        'type' => 'button',
                        'label' => 'Download File',
                        'url' => fn($d) => route('admin.dashboard'),
                        'icon' => 'download',
                        'download' => true,
                    ],
                    'Profil' => [
                        'type' => 'link',
                        'label' => 'Lihat Profil',
                        'url' => fn($d) => route('admin.profile'),
                        'icon' => 'person',
                        'style' => 'info',
                        'newTab' => true,
                    ],
                ]);
            })
            ->enableEditable(function ($data, $i, $column_key) {
                if( $column_key === 'name'){
                    return $data->role === 'admin';
                }
                return true;
            })
            ->withColumnIndex("No")
            ->withColumnImage("Avatar", "avatar",  40, 40,  'rounded-full ring ring-primary',  'name')
            ->withColumn("Nama", "name", editable: true, rules: 'required|max:255')
            ->withColumn("Username", "username")
            ->withColumn('Email', 'email', showOn: 'desktop')

            ->withCustomColumn("Role", function ($data, $i) {
                $roleLabel = str_replace('-', ' ', $data->role);
                $badgeClass = $data->role === 'super-admin' ? 'badge-primary' : 'badge-secondary';
                return '<span class="badge ' . $badgeClass . ' badge-sm text-white uppercase whitespace-nowrap truncate max-w-full">' . $roleLabel . '</span>';
            }, 'role', false)
            ->withActionColumn();
    }

    public function setFilter()
    {
        $roles = [
            ['value' => 'super-admin', 'label' => 'Super Admin'],
            ['value' => 'admin', 'label' => 'Admin'],
        ];

        $roleFilter = MrCatzDataTableFilter::create(
            'filter_role',
            'Role',
            $roles,
            'value',
            'label',
            'role'
        )->get();

        $usernameFilter = MrCatzDataTableFilter::create(
            'filter_username',
            'Username',
            [],
            'value',
            'label',
            'username',
            false
        )->get();

        return [$roleFilter, $usernameFilter];
    }

    public function onFilterChanged($id, $value)
    {
        if ($id === 'filter_role') {
            $this->resetFilter('filter_username');

            if (!empty($value)) {
                $users = DB::table('users')
                    ->where('role', $value)
                    ->orderBy('username')
                    ->get()
                    ->toArray();

                $users = json_decode(json_encode($users), true);

                $this->setFilterData('filter_username', array_map(fn($u) => [
                    'value' => $u['username'],
                    'label' => $u['username'] . ' (' . $u['name'] . ')',
                ], $users));
                $this->setFilterShow('filter_username', true);
            } else {
                $this->setFilterShow('filter_username', false);
            }
        }
    }

    public function getRowPerPageOption()
    {
        return [10, 15, 20, 30];
    }
}
