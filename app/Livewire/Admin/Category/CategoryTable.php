<?php

namespace App\Livewire\Admin\Category;

use Illuminate\Support\Facades\DB;
use MrCatz\DataTable\MrCatzDataTablesComponent;

class CategoryTable extends MrCatzDataTablesComponent
{
    public $showSearch = true;
    public $showAddButton = true;
    public $bulkPrimaryKey = 'id';
    public $showBulkButton = true;
    public $exportTitle = 'Data Kategori';

    public function configTable()
    {
        return [
            'table_name' => 'categories',
            'table_id' => 'id'
        ];
    }

    public function baseQuery()
    {
        return DB::table('categories');
    }

    public function setTable()
    {
        return $this->CreateMrCatzTable()
            ->enableBulk()
            ->withColumnIndex('No')
            ->withColumn('Nama Kategori', 'name', editable: true, rules: 'required|max:100')
            ->withCustomColumn('Jumlah Tugas', function ($data, $i) {
                $count = DB::table('tasks')->where('category_id', $data->id)->count();
                $class = $count > 0 ? 'badge-primary' : 'badge-ghost border border-base-content/15';
                return '<span class="badge ' . $class . ' badge-sm whitespace-nowrap">' . $count . ' tugas</span>';
            }, null, false)
            ->withActionColumn()
            ->setDefaultOrder('name', 'asc');
    }

    public function getRowPerPageOption()
    {
        return [10, 15, 20, 30];
    }
}
