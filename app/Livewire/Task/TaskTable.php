<?php

namespace App\Livewire\Task;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use MrCatz\DataTable\MrCatzDataTableFilter;
use MrCatz\DataTable\MrCatzDataTables;
use MrCatz\DataTable\MrCatzDataTablesComponent;

class TaskTable extends MrCatzDataTablesComponent
{
    public $showSearch = true;
    public $showAddButton = true;
    public $bulkPrimaryKey = 'id';
    public $showBulkButton = true;
    public $expandableRows = 'both';
    public $exportTitle = 'Data Tugas Saya';

    public function configTable()
    {
        return [
            'table_name' => 'tasks',
            'table_id' => 'id'
        ];
    }

    public function baseQuery()
    {
        return DB::table('tasks')
            ->leftJoin('categories', 'categories.id', '=', 'tasks.category_id')
            ->where('tasks.user_id', Auth::id())
            ->select('tasks.*', 'categories.name as category_name');
    }

    public function setTable()
    {
        return $this->CreateMrCatzTable()
            ->enableExpand(function ($data, $i) {
                return MrCatzDataTables::getExpandView($data, [
                    'Judul' => 'title',
                    'Deskripsi' => 'description',
                    'Kategori' => 'category_name',
                    'Dibuat' => 'created_at',
                    'Diperbarui' => 'updated_at',
                ]);
            })
            ->enableBulk()
            ->withColumnIndex('No')
            ->withColumn('Judul', 'title')
            ->withCustomColumn('Kategori', function ($data, $i) {
                if (empty($data->category_name)) {
                    return '<span class="text-base-content/30 text-xs">—</span>';
                }
                return '<span class="badge badge-sm badge-ghost border border-base-content/15 whitespace-nowrap">' . e($data->category_name) . '</span>';
            }, 'category_name')
            ->withCustomColumn('Prioritas', function ($data, $i) {
                $label = Task::PRIORITIES[$data->priority] ?? $data->priority;
                return '<span class="badge ' . Task::priorityBadge($data->priority) . ' badge-sm text-white whitespace-nowrap">' . $label . '</span>';
            }, 'priority')
            ->withCustomColumn('Status', function ($data, $i) {
                $label = Task::STATUSES[$data->status] ?? $data->status;
                return '<span class="badge ' . Task::statusBadge($data->status) . ' badge-sm whitespace-nowrap">' . $label . '</span>';
            }, 'status')
            ->withCustomColumn('Tenggat', function ($data, $i) {
                if (empty($data->due_date)) {
                    return '<span class="text-base-content/30 text-xs">—</span>';
                }
                $date = Carbon::parse($data->due_date)->locale('id');
                $isOverdue = $date->isPast() && $data->status !== Task::STATUS_SELESAI;
                $class = $isOverdue ? 'text-error font-semibold' : 'text-base-content';
                $hint = $isOverdue ? ' <span class="badge badge-error badge-xs text-white ml-1">terlambat</span>' : '';
                return '<span class="' . $class . ' whitespace-nowrap">' . $date->translatedFormat('d M Y') . '</span>' . $hint;
            }, 'due_date')
            ->withCustomColumn('Detail', function ($data, $i) {
                return '<button class="btn btn-ghost btn-sm btn-square text-info hover:bg-info/10 transition-colors duration-200 tooltip tooltip-top" data-tip="Detail Tugas" wire:click="openDetail(' . $data->id . ')">'
                    . '<span class="material-symbols-outlined text-lg">visibility</span>'
                    . '</button>';
            }, null, false, true, 'both', 'action')
            ->withActionColumn()
            ->setDefaultOrder('created_at', 'desc');
    }

    public function setFilter()
    {
        $statusOptions = collect(Task::STATUSES)
            ->map(fn ($label, $value) => ['value' => $value, 'label' => $label])
            ->values()->all();

        $priorityOptions = collect(Task::PRIORITIES)
            ->map(fn ($label, $value) => ['value' => $value, 'label' => $label])
            ->values()->all();

        $categoryOptions = DB::table('categories')->orderBy('name')
            ->get()->map(fn ($c) => ['value' => $c->id, 'label' => $c->name])->all();

        return [
            MrCatzDataTableFilter::create('filter_status', 'Status', $statusOptions, 'value', 'label', 'status')->get(),
            MrCatzDataTableFilter::create('filter_priority', 'Prioritas', $priorityOptions, 'value', 'label', 'priority')->get(),
            MrCatzDataTableFilter::create('filter_category', 'Kategori', $categoryOptions, 'value', 'label', 'category_id')->get(),
        ];
    }

    public function getRowPerPageOption()
    {
        return [10, 15, 20, 30];
    }

    public function openDetail($id)
    {
        $this->dispatch('open-task-detail', ['id' => $id]);
    }
}
