<?php

namespace App\Livewire\Admin;

use App\Models\Publisher;
use Livewire\Component;
use Livewire\WithPagination;

class Publishers extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showForm = false;
    public ?int $editingId = null;

    public string $name = '';
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
        $publisher = Publisher::findOrFail($id);

        $this->editingId = $publisher->id;
        $this->name      = $publisher->name;
        $this->is_active = $publisher->is_active;

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
            'is_active' => 'boolean',
        ];
    }

    public function save(): void
    {
        $data = $this->validate();
        $data['is_active'] = $this->is_active;

        if ($this->editingId) {
            Publisher::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Publisher updated.');
        } else {
            Publisher::create($data);
            session()->flash('success', 'Publisher created.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete(int $id): void
    {
        $publisher = Publisher::findOrFail($id);

        if ($publisher->products()->exists()) {
            session()->flash('error', 'Cannot delete a publisher with products. Deactivate instead.');
            return;
        }

        $publisher->delete();
        session()->flash('success', 'Publisher deleted.');
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name']);
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function render()
    {
        $publishers = Publisher::withCount('products')
            ->search($this->search)
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.admin.publishers', ['publishers' => $publishers])->extends('layouts.admin');
    }
}
