<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
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

    /** @var array<int, array{product_id: string, quantity: string, purchase_price: string}> */
    public array $items = [];

    public bool $processing = false;

    public function mount(): void
    {
        $this->items = [['product_id' => '', 'quantity' => '1', 'purchase_price' => '']];
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

    public function addItem(): void
    {
        $this->items[] = ['product_id' => '', 'quantity' => '1', 'purchase_price' => ''];
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

                    $inventory->adjust($product, (int) $item['quantity'], 'purchase', $purchase, "Received via {$purchase->purchase_number}");
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
        $this->reset(['supplier_id', 'purchase_date', 'invoice_number', 'notes']);
        $this->items = [['product_id' => '', 'quantity' => '1', 'purchase_price' => '']];
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
            'purchases' => $purchases,
            'suppliers' => Supplier::active()->get(),
            'products'  => Product::orderBy('name')->get(['id', 'name', 'sku']),
        ])->extends('layouts.admin');
    }
}
