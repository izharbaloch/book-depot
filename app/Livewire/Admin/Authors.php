<?php

namespace App\Livewire\Admin;

use App\Models\Author;
use Livewire\Component;
use Livewire\WithPagination;

class Authors extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showForm = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $bio = '';
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
        $author = Author::findOrFail($id);

        $this->editingId = $author->id;
        $this->name      = $author->name;
        $this->bio       = (string) $author->bio;
        $this->is_active = $author->is_active;

        $this->showForm = true;
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    public function toggleActive(int $id): void
    {
        $author = Author::findOrFail($id);
        $author->update(['is_active' => !$author->is_active]);
    }

    protected function rules(): array
    {
        return [
            'name'      => 'required|string|max:150',
            'bio'       => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function save(): void
    {
        $data = $this->validate();
        $data['is_active'] = $this->is_active;

        if ($this->editingId) {
            Author::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Author updated.');
        } else {
            Author::create($data);
            session()->flash('success', 'Author created.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete(int $id): void
    {
        $author = Author::findOrFail($id);

        if ($author->products()->exists()) {
            session()->flash('error', 'Cannot delete an author with products. Deactivate instead.');
            return;
        }

        $author->delete();
        session()->flash('success', 'Author deleted.');
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'bio']);
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function render()
    {
        $authors = Author::withCount('products')
            ->search($this->search)
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.admin.authors', ['authors' => $authors])->extends('layouts.admin');
    }
}
