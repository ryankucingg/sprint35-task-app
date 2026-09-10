<?php

namespace App\Livewire\Task;

use App\Models\Category;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use MrCatz\DataTable\MrCatzComponent;
use MrCatz\DataTable\MrCatzFormField;

class TaskPage extends MrCatzComponent
{
    public $title;
    public $description;
    public $category_id;
    public $priority;
    public $status;
    public $due_date;

    public $showDetailModal = false;
    public $detailTask = null;

    public function setForm(): array
    {
        return [
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
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.all-tasks');
        }

        $this->setTitle('Tugas Saya');
        session()->put('active', 'admin-tasks');
        $this->breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['title' => 'Tugas Saya', 'url' => null],
        ];
    }

    public function render()
    {
        return view('livewire.task.task-page')->layout('components.layouts.admin_layout');
    }

    public function prepareAddData()
    {
        $this->form_title = 'Tambah Tugas';
        $this->resetErrorBag();
        $this->resetValidation();
        $this->title = '';
        $this->description = '';
        $this->category_id = '';
        $this->priority = Task::PRIORITY_SEDANG;
        $this->status = Task::STATUS_BELUM;
        $this->due_date = '';
    }

    public function prepareEditData($data)
    {
        $task = $this->findOwnedTask($data['id']);

        if (!$task) {
            $this->show_notif('error', 'Tugas tidak ditemukan atau bukan milik Anda!');
            return;
        }

        $this->id = $task->id;
        $this->form_title = 'Edit Tugas';
        $this->resetErrorBag();
        $this->resetValidation();
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

        $payload = [
            'category_id' => $this->category_id !== '' ? $this->category_id : null,
            'title' => $this->title,
            'description' => $this->description !== '' ? $this->description : null,
            'priority' => $this->priority,
            'status' => $this->status,
            'due_date' => $this->due_date !== '' ? $this->due_date : null,
        ];

        if ($this->isEdit) {
            $task = $this->findOwnedTask($this->id);

            if (!$task) {
                $this->show_notif('error', 'Tugas tidak ditemukan atau bukan milik Anda!');
                return;
            }

            $task->fill($payload)->save();

            Log::info('Task updated', [
                'updated_by' => Auth::id(),
                'task_id' => $task->id,
            ]);

            $this->dispatch_to_view(true, 'update');
        } else {
            $task = Auth::user()->tasks()->create($payload);

            Log::info('Task created', [
                'created_by' => Auth::id(),
                'task_id' => $task->id,
            ]);

            $this->dispatch_to_view($task->exists, 'insert');
        }
    }

    public function prepareDeleteData($data)
    {
        $task = $this->findOwnedTask($data['id']);

        if (!$task) {
            $this->show_notif('error', 'Tugas tidak ditemukan atau bukan milik Anda!');
            return;
        }

        $this->id = $task->id;
        $this->form_title = 'Hapus Tugas';
        $this->deleted_text = $task->title;
    }

    public function dropData()
    {
        $task = $this->findOwnedTask($this->id);

        if (!$task) {
            $this->show_notif('error', 'Tugas tidak ditemukan atau bukan milik Anda!');
            return;
        }

        Log::info('Task deleted', [
            'deleted_by' => Auth::id(),
            'task_id' => $task->id,
        ]);

        $delete = $task->delete();
        $this->dispatch_to_view($delete, 'delete');
    }

    public function dropBulkData($selectedRows)
    {
        if (empty($selectedRows)) return;

        $count = Task::where('user_id', Auth::id())->whereIn('id', $selectedRows)->delete();

        Log::info('Bulk task deleted', [
            'deleted_by' => Auth::id(),
            'count' => $count,
        ]);

        $this->dispatch('refresh-data', [
            'status' => true,
            'text' => $count . ' tugas berhasil dihapus!',
        ]);
    }

    private function findOwnedTask($id): ?Task
    {
        return Task::where('user_id', Auth::id())->find($id);
    }

    #[On('open-task-detail')]
    public function loadDetail($data)
    {
        $task = $this->findOwnedTask($data['id'] ?? null);

        if (!$task) {
            $this->show_notif('error', 'Tugas tidak ditemukan atau bukan milik Anda!');
            return;
        }

        $task->load('category');
        $this->detailTask = $task;
        $this->showDetailModal = true;
    }

    public function closeDetail()
    {
        $this->showDetailModal = false;
    }

    public function editFromDetail($id)
    {
        $this->showDetailModal = false;
        $this->listenEditData(['id' => $id]);
    }
}
