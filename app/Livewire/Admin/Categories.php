<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Categories extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';

    public bool $showForm = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $parent_id = '';
    public string $description = '';
    public string $sort_order = '0';
    public bool $is_active = true;

    public $image;
    public ?string $currentImage = null;

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
        $category = Category::findOrFail($id);

        $this->editingId    = $category->id;
        $this->name         = $category->name;
        $this->parent_id    = (string) $category->parent_id;
        $this->description  = (string) $category->description;
        $this->sort_order    = (string) $category->sort_order;
        $this->is_active    = $category->is_active;
        $this->currentImage = $category->image;
        $this->image        = null;

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
            'name'        => 'required|string|max:100',
            'parent_id'   => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'sort_order'  => 'nullable|integer',
            'image'       => 'nullable|image|max:2048',
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->parent_id && (int) $this->parent_id === $this->editingId) {
            $this->addError('parent_id', 'A category cannot be its own parent.');
            return;
        }

        $data['parent_id']  = $this->parent_id ?: null;
        $data['sort_order'] = $this->sort_order !== '' ? $this->sort_order : 0;
        $data['is_active']  = $this->is_active;
        $data['slug']       = Str::slug($this->name);

        $category = $this->editingId ? Category::findOrFail($this->editingId) : null;

        if ($this->image) {
            if ($category?->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $this->image->store('categories', 'public');
        }

        if ($category) {
            $category->update($data);
            session()->flash('success', 'Category updated.');
        } else {
            Category::create($data);
            session()->flash('success', 'Category created.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete(int $id): void
    {
        $category = Category::findOrFail($id);

        if ($category->products()->exists()) {
            session()->flash('error', 'Cannot delete category with products.');
            return;
        }
        if ($category->children()->exists()) {
            session()->flash('error', 'Cannot delete category with subcategories.');
            return;
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }
        $category->delete();
        session()->flash('success', 'Category deleted.');
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'parent_id', 'description', 'image', 'currentImage']);
        $this->sort_order = '0';
        $this->is_active  = true;
        $this->resetErrorBag();
    }

    public function render()
    {
        $categories = Category::withCount('products')
            ->when($this->search, fn($q, $s) => $q->where('name', 'like', "%$s%"))
            ->orderBy('sort_order')
            ->paginate(15);

        return view('livewire.admin.categories', [
            'categories'   => $categories,
            'allCategories' => Category::orderBy('name')->get(),
        ])->extends('layouts.admin');
    }
}
