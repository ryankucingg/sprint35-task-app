<?php

namespace App\Livewire\Admin\Category;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use MrCatz\DataTable\MrCatzComponent;
use MrCatz\DataTable\MrCatzFormField;

class CategoryPage extends MrCatzComponent
{
    public $name;
    public $oldName;

    public function setForm(): array
    {
        return [
            MrCatzFormField::text('name', label: 'Nama Kategori',
                rules: 'required|max:100',
                messages: ['required' => 'Nama kategori wajib diisi', 'max' => 'Nama kategori maksimal 100 karakter'],
                icon: 'category',
            ),
        ];
    }

    public function mount()
    {
        $this->setTitle('Kategori');
        session()->put('active', 'admin-categories');
        $this->breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['title' => 'Kategori Tugas', 'url' => null],
        ];
    }

    public function render()
    {
        return view('livewire.admin.category.category-page')->layout('components.layouts.admin_layout');
    }

    public function prepareAddData()
    {
        $this->form_title = 'Tambah Kategori';
        $this->resetErrorBag();
        $this->resetValidation();
        $this->name = '';
        $this->oldName = null;
    }

    public function prepareEditData($data)
    {
        $this->id = $data['id'];
        $this->form_title = 'Edit Kategori';
        $this->resetErrorBag();
        $this->resetValidation();
        $this->name = $data['name'];
        $this->oldName = $data['name'];
    }

    public function saveData()
    {
        $this->validate(
            $this->getFormValidationRules(),
            $this->getFormValidationMessages()
        );

        if ($this->oldName === null || $this->name !== $this->oldName) {
            if (Category::where('name', $this->name)->exists()) {
                $this->addError('name', 'Nama kategori sudah digunakan!');
                return;
            }
        }

        if ($this->isEdit) {
            $category = Category::find($this->id);

            if (!$category) {
                $this->show_notif('error', 'Kategori tidak ditemukan!');
                return;
            }

            $category->name = $this->name;
            $category->save();

            Log::info('Category updated', [
                'updated_by' => Auth::id(),
                'category_id' => $category->id,
            ]);

            $this->dispatch_to_view(true, 'update');
        } else {
            $category = Category::create(['name' => $this->name]);

            Log::info('Category created', [
                'created_by' => Auth::id(),
                'category_id' => $category->id,
            ]);

            $this->dispatch_to_view(true, 'insert');
        }
    }

    public function onInlineUpdate($rowData, $columnKey, $newValue)
    {
        if ($columnKey === 'name') {
            $exists = Category::where('name', $newValue)->where('id', '!=', $rowData['id'])->exists();
            if ($exists) {
                $this->show_notif('error', 'Nama kategori sudah digunakan!');
                return;
            }
        }

        Category::where('id', $rowData['id'])->update([$columnKey => $newValue]);
        $this->dispatch_to_view(true, 'update');
    }

    public function prepareDeleteData($data)
    {
        $category = Category::find($data['id']);

        if (!$category) {
            $this->show_notif('error', 'Kategori tidak ditemukan!');
            return;
        }

        $this->id = $category->id;
        $this->form_title = 'Hapus Kategori';
        $this->deleted_text = $category->name . ' (' . $category->tasks()->count() . ' tugas terkait)';
    }

    public function dropData()
    {
        $category = Category::find($this->id);

        if (!$category) {
            $this->show_notif('error', 'Kategori tidak ditemukan!');
            return;
        }

        Log::info('Category deleted', [
            'deleted_by' => Auth::id(),
            'category_id' => $category->id,
        ]);

        $delete = $category->delete();
        $this->dispatch_to_view($delete, 'delete');
    }

    public function dropBulkData($selectedRows)
    {
        if (empty($selectedRows)) return;

        $count = Category::whereIn('id', $selectedRows)->delete();

        Log::info('Bulk category deleted', [
            'deleted_by' => Auth::id(),
            'count' => $count,
        ]);

        $this->dispatch('refresh-data', [
            'status' => true,
            'text' => $count . ' kategori berhasil dihapus!',
        ]);
    }
}
