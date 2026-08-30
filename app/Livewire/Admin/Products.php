<?php

namespace App\Livewire\Admin;

use App\Models\Author;
use App\Models\Category;
use App\Models\Product;
use App\Models\Publisher;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Products extends Component
{
    use WithPagination, WithFileUploads;

    // ── Filters ──────────────────────────────────────────────
    public string $search = '';
    public string $categoryFilter = '';
    public string $authorFilter = '';
    public string $publisherFilter = '';
    public string $stockFilter = '';
    public string $statusFilter = '';

    // ── Form state ───────────────────────────────────────────
    public bool $showForm = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $category_id = '';
    public string $author_id = '';
    public string $publisher_id = '';
    public string $short_description = '';
    public string $description = '';
    public string $price = '';
    public string $sale_price = '';
    public string $cost_price = '';
    public string $sku = '';
    public string $isbn = '';
    public string $barcode = '';
    public string $stock = '0';
    public string $min_stock_level = '5';
    public string $weight = '';
    public bool $is_active = true;
    public bool $is_featured = false;
    public bool $is_trending = false;
    public bool $is_bestseller = false;
    public bool $is_new = false;

    public $image;
    public $gallery = [];
    public ?string $currentImage = null;
    public array $currentGallery = [];

    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }
    public function updatedAuthorFilter()
    {
        $this->resetPage();
    }
    public function updatedPublisherFilter()
    {
        $this->resetPage();
    }
    public function updatedStockFilter()
    {
        $this->resetPage();
    }
    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'categoryFilter', 'authorFilter', 'publisherFilter', 'stockFilter', 'statusFilter']);
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $product = Product::findOrFail($id);

        $this->editingId         = $product->id;
        $this->name              = $product->name;
        $this->category_id       = (string) $product->category_id;
        $this->author_id         = (string) $product->author_id;
        $this->publisher_id      = (string) $product->publisher_id;
        $this->short_description = (string) $product->short_description;
        $this->description       = (string) $product->description;
        $this->price             = (string) $product->price;
        $this->sale_price        = (string) $product->sale_price;
        $this->cost_price        = (string) $product->cost_price;
        $this->sku               = $product->sku;
        $this->isbn              = (string) $product->isbn;
        $this->barcode           = (string) $product->barcode;
        $this->stock             = (string) $product->stock;
        $this->min_stock_level   = (string) $product->min_stock_level;
        $this->weight            = (string) $product->weight;
        $this->is_active         = $product->is_active;
        $this->is_featured       = $product->is_featured;
        $this->is_trending       = $product->is_trending;
        $this->is_bestseller     = $product->is_bestseller;
        $this->is_new            = $product->is_new;
        $this->currentImage      = $product->image;
        $this->currentGallery    = $product->gallery ?? [];
        $this->image             = null;
        $this->gallery           = [];

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
            'name'              => 'required|string|max:180',
            'category_id'       => 'required|exists:categories,id',
            'author_id'         => 'nullable|exists:authors,id',
            'publisher_id'      => 'nullable|exists:publishers,id',
            'short_description' => 'nullable|string|max:300',
            'description'       => 'nullable|string',
            'price'             => 'required|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0|lt:price',
            'cost_price'        => 'nullable|numeric|min:0',
            'sku'               => ['nullable', 'string', 'max:60', Rule::unique('products', 'sku')->ignore($this->editingId)],
            'isbn'              => ['nullable', 'string', 'max:30', Rule::unique('products', 'isbn')->ignore($this->editingId)],
            'barcode'           => ['nullable', 'string', 'max:60', Rule::unique('products', 'barcode')->ignore($this->editingId)],
            'stock'             => 'required|integer|min:0',
            'min_stock_level'   => 'required|integer|min:0',
            'weight'            => 'nullable|numeric|min:0',
            'image'             => 'nullable|image|max:3072',
            'gallery.*'         => 'nullable|image|max:3072',
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        $data['author_id']    = $this->author_id ?: null;
        $data['publisher_id'] = $this->publisher_id ?: null;
        $data['sale_price']   = $this->sale_price !== '' ? $this->sale_price : null;
        $data['cost_price']   = $this->cost_price !== '' ? $this->cost_price : 0;
        $data['isbn']         = $this->isbn ?: null;
        $data['barcode']      = $this->barcode ?: null;
        $data['weight']       = $this->weight !== '' ? $this->weight : null;
        $data['is_active']     = $this->is_active;
        $data['is_featured']   = $this->is_featured;
        $data['is_trending']   = $this->is_trending;
        $data['is_bestseller'] = $this->is_bestseller;
        $data['is_new']        = $this->is_new;

        $product = $this->editingId ? Product::findOrFail($this->editingId) : null;

        if (empty($data['sku'])) {
            $data['sku'] = 'BKD-' . strtoupper(Str::random(8));
        }

        if (!$product) {
            $data['slug'] = Str::slug($this->name) . '-' . Str::random(4);
        }

        if ($this->image) {
            if ($product?->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $this->image->store('products', 'public');
        }

        if (!empty($this->gallery)) {
            foreach ($product?->gallery ?? [] as $old) {
                Storage::disk('public')->delete($old);
            }
            $data['gallery'] = collect($this->gallery)
                ->map(fn($file) => $file->store('products', 'public'))
                ->toArray();
        }

        if ($product) {
            $product->update($data);
            session()->flash('success', 'Product updated successfully.');
        } else {
            Product::create($data);
            session()->flash('success', 'Product created successfully.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete(int $id): void
    {
        $product = Product::findOrFail($id);

        if ($product->purchaseItems()->exists()) {
            session()->flash('error', 'Cannot delete a product with purchase history. Deactivate it instead.');
            return;
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        foreach ($product->gallery ?? [] as $img) {
            Storage::disk('public')->delete($img);
        }

        $product->delete();
        session()->flash('success', 'Product deleted.');
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'name',
            'category_id',
            'author_id',
            'publisher_id',
            'short_description',
            'description',
            'price',
            'sale_price',
            'cost_price',
            'sku',
            'isbn',
            'barcode',
            'weight',
            'image',
            'gallery',
            'currentImage',
            'currentGallery',
        ]);
        $this->stock           = '0';
        $this->min_stock_level = '5';
        $this->is_active       = true;
        $this->is_featured     = false;
        $this->is_trending     = false;
        $this->is_bestseller   = false;
        $this->is_new          = false;
        $this->resetErrorBag();
    }

    public function render()
    {
        $products = Product::with(['category', 'author', 'publisher'])
            ->search($this->search)
            ->when($this->categoryFilter, fn($q, $c) => $q->where('category_id', $c))
            ->when($this->authorFilter, fn($q, $a) => $q->where('author_id', $a))
            ->when($this->publisherFilter, fn($q, $p) => $q->where('publisher_id', $p))
            ->when($this->stockFilter === 'low', fn($q) => $q->lowStock())
            ->when($this->stockFilter === 'out', fn($q) => $q->outOfStock())
            ->when($this->statusFilter !== '', fn($q) => $q->where('is_active', (bool) $this->statusFilter))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.admin.products', [
            'products'   => $products,
            'categories' => Category::orderBy('name')->get(),
            'authors'    => Author::active()->get(),
            'publishers' => Publisher::active()->get(),
        ])->extends('layouts.admin');
    }
}
