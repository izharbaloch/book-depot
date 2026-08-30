<?php

namespace App\Livewire\Admin;

use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;

class Suppliers extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showForm = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $phone = '';
    public string $email = '';
    public string $address = '';
    public string $notes = '';
    public bool $is_active = true;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $supplier = Supplier::findOrFail($id);

        $this->editingId = $supplier->id;
        $this->name      = $supplier->name;
        $this->phone     = (string) $supplier->phone;
        $this->email     = (string) $supplier->email;
        $this->address   = (string) $supplier->address;
        $this->notes     = (string) $supplier->notes;
        $this->is_active = $supplier->is_active;

        $this->showForm = true;
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    protected function rules(): array
    {
        return [
            'name'      => 'required|string|max:150',
            'phone'     => 'nullable|string|max:30',
            'email'     => 'nullable|email|max:150',
            'address'   => 'nullable|string|max:255',
            'notes'     => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function save(): void
    {
        $data = $this->validate();
        $data['is_active'] = $this->is_active;

        if ($this->editingId) {
            Supplier::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Supplier updated.');
        } else {
            Supplier::create($data);
            session()->flash('success', 'Supplier created.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete(int $id): void
    {
        $supplier = Supplier::findOrFail($id);

        if ($supplier->purchases()->exists()) {
            session()->flash('error', 'Cannot delete a supplier with purchase history. Deactivate instead.');
            return;
        }

        $supplier->delete();
        session()->flash('success', 'Supplier deleted.');
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'phone', 'email', 'address', 'notes']);
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function render()
    {
        $suppliers = Supplier::withCount('purchases')
            ->search($this->search)
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.admin.suppliers', ['suppliers' => $suppliers])->extends('layouts.admin');
    }
}
