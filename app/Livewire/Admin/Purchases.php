<?php

namespace App\Livewire\Admin;

use App\Models\Author;
use App\Models\Category;
use App\Models\Product;
use App\Models\Publisher;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Purchases extends Component
{
    use WithPagination;

    public string $search = '';
    public string $supplierFilter = '';

    public bool $showForm = false;
    public ?int $expandedId = null;

    public string $supplier_id = '';
    public string $purchase_date = '';
    public string $invoice_number = '';
    public string $notes = '';

    public string $itemSearch = '';

    /** @var array<int, array{product_id: int, product_name: string, quantity: string, purchase_price: string}> */
    public array $items = [];

    // ── New Product mini-form ───────────────────────────────
    public bool $showNewProduct = false;
    public string $np_name = '';
    public string $np_category_id = '';
    public string $np_author_id = '';
    public string $np_publisher_id = '';
    public string $np_price = '';
    public string $np_sku = '';
    public string $np_isbn = '';
    public string $np_barcode = '';

    public bool $processing = false;

    public function mount(): void
    {
        $this->purchase_date = today()->toDateString();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedSupplierFilter()
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    public function addProduct(int $productId): void
    {
        foreach ($this->items as $item) {
            if ((int) $item['product_id'] === $productId) {
                $this->itemSearch = '';
                return;
            }
        }

        $product = Product::find($productId);
        if (!$product) {
            return;
        }

        $this->items[] = [
            'product_id'     => $product->id,
            'product_name'   => $product->name,
            'quantity'       => '1',
            'purchase_price' => (string) $product->cost_price,
        ];

        $this->itemSearch = '';
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function toggleExpand(int $id): void
    {
        $this->expandedId = $this->expandedId === $id ? null : $id;
    }

    public function getItemResultsProperty()
    {
        $term = trim($this->itemSearch);
        if ($term === '') {
            return collect();
        }

        return Product::search($term)->limit(8)->get();
    }

    public function getLineTotalsProperty(): array
    {
        return collect($this->items)
            ->map(fn($item) => (float) ($item['quantity'] ?: 0) * (float) ($item['purchase_price'] ?: 0))
            ->all();
    }

    public function getGrandTotalProperty(): float
    {
        return array_sum($this->lineTotals);
    }

    // ── New Product mini-form ───────────────────────────────
    public function openNewProduct(): void
    {
        $this->resetNewProductForm();
        $this->showNewProduct = true;
    }

    public function cancelNewProduct(): void
    {
        $this->resetNewProductForm();
        $this->showNewProduct = false;
    }

    protected function newProductRules(): array
    {
        return [
            'np_name'        => 'required|string|max:180',
            'np_category_id' => 'required|exists:categories,id',
            'np_author_id'   => 'nullable|exists:authors,id',
            'np_publisher_id' => 'nullable|exists:publishers,id',
            'np_price'       => 'required|numeric|min:0',
            'np_sku'         => ['nullable', 'string', 'max:60', Rule::unique('products', 'sku')],
            'np_isbn'        => ['nullable', 'string', 'max:30', Rule::unique('products', 'isbn')],
            'np_barcode'     => ['nullable', 'string', 'max:60', Rule::unique('products', 'barcode')],
        ];
    }

    public function saveNewProduct(): void
    {
        $data = $this->validate($this->newProductRules(), [], [
            'np_name' => 'name', 'np_category_id' => 'category', 'np_author_id' => 'author',
            'np_publisher_id' => 'publisher', 'np_price' => 'price', 'np_sku' => 'SKU',
            'np_isbn' => 'ISBN', 'np_barcode' => 'barcode',
        ]);

        $product = Product::create([
            'name'            => $data['np_name'],
            'slug'            => Str::slug($data['np_name']) . '-' . Str::random(4),
            'category_id'     => $data['np_category_id'],
            'author_id'       => $data['np_author_id'] ?: null,
            'publisher_id'    => $data['np_publisher_id'] ?: null,
            'price'           => $data['np_price'],
            'cost_price'      => 0,
            'sku'             => $data['np_sku'] ?: 'BKD-' . strtoupper(Str::random(8)),
            'isbn'            => $data['np_isbn'] ?: null,
            'barcode'         => $data['np_barcode'] ?: null,
            'stock'           => 0,
            'min_stock_level' => 5,
            'is_active'       => true,
        ]);

        $this->cancelNewProduct();
        $this->addProduct($product->id);
        session()->flash('success', "\"{$product->name}\" created. Enter its quantity and purchase rate below.");
    }

    private function resetNewProductForm(): void
    {
        $this->reset(['np_name', 'np_category_id', 'np_author_id', 'np_publisher_id', 'np_price', 'np_sku', 'np_isbn', 'np_barcode']);
        $this->resetErrorBag();
    }

    protected function rules(): array
    {
        return [
            'supplier_id'              => 'required|exists:suppliers,id',
            'purchase_date'            => 'required|date',
            'invoice_number'           => 'nullable|string|max:100',
            'notes'                    => 'nullable|string',
            'items'                    => 'required|array|min:1',
            'items.*.product_id'      => 'required|exists:products,id',
            'items.*.quantity'         => 'required|integer|min:1',
            'items.*.purchase_price'   => 'required|numeric|min:0',
        ];
    }

    public function save(InventoryService $inventory): void
    {
        if ($this->processing) {
            return;
        }
        $this->processing = true;

        try {
            $data = $this->validate();

            DB::transaction(function () use ($data, $inventory) {
                $total = collect($data['items'])->sum(fn($i) => $i['quantity'] * $i['purchase_price']);

                $purchase = Purchase::create([
                    'purchase_number' => Purchase::generatePurchaseNumber(),
                    'supplier_id'     => $data['supplier_id'],
                    'user_id'         => auth()->id(),
                    'purchase_date'   => $data['purchase_date'],
                    'invoice_number'  => $data['invoice_number'] ?: null,
                    'total'           => $total,
                    'notes'           => $data['notes'] ?: null,
                ]);

                foreach ($data['items'] as $item) {
                    $product  = Product::findOrFail($item['product_id']);
                    $subtotal = $item['quantity'] * $item['purchase_price'];

                    PurchaseItem::create([
                        'purchase_id'    => $purchase->id,
                        'product_id'     => $product->id,
                        'quantity'       => $item['quantity'],
                        'purchase_price' => $item['purchase_price'],
                        'subtotal'       => $subtotal,
                    ]);

                    $inventory->receivePurchase($product, (int) $item['quantity'], (float) $item['purchase_price'], $purchase, "Received via {$purchase->purchase_number}");
                }
            });

            session()->flash('success', 'Stock received and inventory updated.');
            $this->resetForm();
            $this->showForm = false;
        } finally {
            $this->processing = false;
        }
    }

    private function resetForm(): void
    {
        $this->reset(['supplier_id', 'invoice_number', 'notes', 'itemSearch']);
        $this->purchase_date = today()->toDateString();
        $this->items = [];
        $this->resetNewProductForm();
        $this->showNewProduct = false;
        $this->resetErrorBag();
    }

    public function render()
    {
        $purchases = Purchase::with(['supplier', 'items.product'])
            ->when($this->search, fn($q, $s) => $q->where(
                fn($w) => $w->where('purchase_number', 'like', "%$s%")
                    ->orWhere('invoice_number', 'like', "%$s%")
            ))
            ->when($this->supplierFilter, fn($q, $s) => $q->where('supplier_id', $s))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.admin.purchases', [
            'purchases'  => $purchases,
            'suppliers'  => Supplier::active()->get(),
            'categories' => Category::orderBy('name')->get(),
            'authors'    => Author::active()->get(),
            'publishers' => Publisher::active()->get(),
        ])->extends('layouts.admin');
    }
}
