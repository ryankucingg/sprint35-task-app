<?php

namespace App\Livewire\Admin\AllTask;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use MrCatz\DataTable\MrCatzComponent;
use MrCatz\DataTable\MrCatzFormField;

class AllTaskPage extends MrCatzComponent
{
    public $user_id;
    public $title;
    public $description;
    public $category_id;
    public $priority;
    public $status;
    public $due_date;

    public function setForm(): array
    {
        return [
            MrCatzFormField::select('user_id', label: 'Pengguna',
                data: User::where('role', 'user')->orderBy('name')
                    ->get()->map(fn ($u) => ['value' => $u->id, 'label' => $u->name])->all(),
                value: 'value',
                option: 'label',
                rules: 'required|exists:users,id',
                messages: ['required' => 'Pengguna wajib dipilih', 'exists' => 'Pengguna yang dipilih tidak valid'],
            ),
            MrCatzFormField::text('title', label: 'Judul',
                rules: 'required|max:255',
                messages: ['required' => 'Judul tugas wajib diisi', 'max' => 'Judul maksimal 255 karakter'],
                icon: 'edit_note',
            ),
            MrCatzFormField::textarea('description', label: 'Deskripsi',
                rules: 'nullable|max:5000',
                messages: ['max' => 'Deskripsi maksimal 5000 karakter'],
                placeholder: 'Rincian tugas (opsional)',
            ),
            MrCatzFormField::select('category_id', label: 'Kategori',
                data: Category::orderBy('name')->get(['id', 'name'])->map(fn ($c) => ['value' => $c->id, 'label' => $c->name])->all(),
                value: 'value',
                option: 'label',
                rules: 'nullable|exists:categories,id',
                messages: ['exists' => 'Kategori yang dipilih tidak valid'],
            ),
            MrCatzFormField::select('priority', label: 'Prioritas',
                data: collect(Task::PRIORITIES)->map(fn ($label, $value) => ['value' => $value, 'label' => $label])->values()->all(),
                value: 'value',
                option: 'label',
                rules: 'required|in:' . implode(',', array_keys(Task::PRIORITIES)),
                messages: ['required' => 'Prioritas wajib dipilih', 'in' => 'Prioritas yang dipilih tidak valid'],
            ),
            MrCatzFormField::select('status', label: 'Status',
                data: collect(Task::STATUSES)->map(fn ($label, $value) => ['value' => $value, 'label' => $label])->values()->all(),
                value: 'value',
                option: 'label',
                rules: 'required|in:' . implode(',', array_keys(Task::STATUSES)),
                messages: ['required' => 'Status wajib dipilih', 'in' => 'Status yang dipilih tidak valid'],
            ),
            MrCatzFormField::date('due_date', label: 'Tenggat Waktu',
                rules: 'nullable|date',
                messages: ['date' => 'Tanggal tenggat harus berupa tanggal yang valid'],
                icon: 'event',
            ),
        ];
    }

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

    public function prepareAddData()
    {
        $this->form_title = 'Buatkan Tugas untuk Pengguna';
        $this->resetErrorBag();
        $this->resetValidation();
        $this->user_id = '';
        $this->title = '';
        $this->description = '';
        $this->category_id = '';
        $this->priority = Task::PRIORITY_SEDANG;
        $this->status = Task::STATUS_BELUM;
        $this->due_date = '';
    }

    public function prepareEditData($data)
    {
        $task = Task::find($data['id']);

        if (!$task) {
            $this->show_notif('error', 'Tugas tidak ditemukan!');
            return;
        }

        $this->id = $task->id;
        $this->form_title = 'Edit Tugas';
        $this->resetErrorBag();
        $this->resetValidation();
        $this->user_id = $task->user_id;
        $this->title = $task->title;
        $this->description = $task->description ?? '';
        $this->category_id = $task->category_id ?? '';
        $this->priority = $task->priority;
        $this->status = $task->status;
        $this->due_date = $task->due_date?->toDateString() ?? '';
    }

    public function saveData()
    {
        $this->validate(
            $this->getFormValidationRules(),
            $this->getFormValidationMessages()
        );

        $targetUser = User::find($this->user_id);

        if (!$targetUser || $targetUser->isAdmin()) {
            $this->addError('user_id', 'Tugas hanya dapat diberikan kepada pengguna berrole user');
            return;
        }

        $payload = [
            'user_id' => $this->user_id,
            'category_id' => $this->category_id !== '' ? $this->category_id : null,
            'title' => $this->title,
            'description' => $this->description !== '' ? $this->description : null,
            'priority' => $this->priority,
            'status' => $this->status,
            'due_date' => $this->due_date !== '' ? $this->due_date : null,
        ];

        if ($this->isEdit) {
            $task = Task::find($this->id);

            if (!$task) {
                $this->show_notif('error', 'Tugas tidak ditemukan!');
                return;
            }

            $task->fill($payload)->save();

            Log::info('Task updated by admin', [
                'updated_by' => Auth::id(),
                'task_id' => $task->id,
                'owner' => $task->user_id,
            ]);

            $this->dispatch_to_view(true, 'update');
        } else {
            $task = Task::create($payload);

            Log::info('Task created by admin', [
                'created_by' => Auth::id(),
                'task_id' => $task->id,
                'owner' => $task->user_id,
            ]);

            $this->dispatch_to_view($task->exists, 'insert');
        }
    }

    public function prepareDeleteData($data)
    {
        $task = Task::find($data['id']);

        if (!$task) {
            $this->show_notif('error', 'Tugas tidak ditemukan!');
            return;
        }

        $this->id = $task->id;
        $this->form_title = 'Hapus Tugas';
        $this->deleted_text = $task->title . ' (' . ($task->user?->name ?? '-') . ')';
    }

    public function dropData()
    {
        $task = Task::find($this->id);

        if (!$task) {
            $this->show_notif('error', 'Tugas tidak ditemukan!');
            return;
        }

        Log::info('Task deleted by admin', [
            'deleted_by' => Auth::id(),
            'task_id' => $task->id,
            'owner' => $task->user_id,
        ]);

        $delete = $task->delete();
        $this->dispatch_to_view($delete, 'delete');
    }

    public function dropBulkData($selectedRows)
    {
        if (empty($selectedRows)) return;

        $count = Task::whereIn('id', $selectedRows)->delete();

        Log::info('Bulk task deleted by admin', [
            'deleted_by' => Auth::id(),
            'count' => $count,
        ]);

        $this->dispatch('refresh-data', [
            'status' => true,
            'text' => $count . ' tugas berhasil dihapus!',
        ]);
    }
}
