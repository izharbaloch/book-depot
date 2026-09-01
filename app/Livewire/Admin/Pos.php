<?php

namespace App\Livewire\Admin;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Pos extends Component
{
    public string $search = '';
    public array $cart = [];
    public ?string $cartError = null;

    public string $customerSearch = '';
    public ?int $customerId = null;
    public ?string $customerName = null;
    public bool $showCustomerForm = false;
    public string $newCustomerName = '';
    public string $newCustomerPhone = '';

    public string $discount = '0';
    public string $paymentMethod = 'cash';
    public string $amountPaid = '';

    public bool $processing = false;
    public ?int $completedSaleId = null;

    public function addToCart(int $productId): void
    {
        $this->cartError = null;
        $product = Product::find($productId);

        if (!$product || !$product->is_active) {
            $this->cartError = 'Product not available.';
            return;
        }

        $currentQty = $this->cart[$productId]['quantity'] ?? 0;

        if ($currentQty + 1 > $product->stock) {
            $this->cartError = "Only {$product->stock} available for \"{$product->name}\".";
            return;
        }

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
        } else {
            $this->cart[$productId] = [
                'product_id' => $product->id,
                'name'       => $product->name,
                'sku'        => $product->sku,
                'price'      => (float) $product->current_price,
                'quantity'   => 1,
                'stock'      => $product->stock,
            ];
        }
    }

    public function scanEnter(): void
    {
        $term = trim($this->search);
        if ($term === '') {
            return;
        }

        $product = Product::where('barcode', $term)->orWhere('sku', $term)->orWhere('isbn', $term)->first();

        if ($product) {
            $this->addToCart($product->id);
            $this->search = '';
        }
    }

    public function incrementQty(int $productId): void
    {
        $product = Product::find($productId);
        if (!$product) {
            return;
        }

        if (($this->cart[$productId]['quantity'] ?? 0) + 1 > $product->stock) {
            $this->cartError = "Only {$product->stock} available for \"{$product->name}\".";
            return;
        }

        $this->cart[$productId]['quantity']++;
        $this->cartError = null;
    }

    public function decrementQty(int $productId): void
    {
        if (!isset($this->cart[$productId])) {
            return;
        }

        $this->cart[$productId]['quantity']--;
        if ($this->cart[$productId]['quantity'] <= 0) {
            unset($this->cart[$productId]);
        }
    }

    public function removeFromCart(int $productId): void
    {
        unset($this->cart[$productId]);
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->cartError = null;
    }

    public function selectCustomer(int $id): void
    {
        $customer = Customer::find($id);
        if ($customer) {
            $this->customerId = $customer->id;
            $this->customerName = $customer->name;
            $this->customerSearch = '';
        }
    }

    public function clearCustomer(): void
    {
        $this->customerId = null;
        $this->customerName = null;
    }

    public function openCustomerForm(): void
    {
        $this->showCustomerForm = true;
        $this->newCustomerName = $this->customerSearch;
    }

    public function cancelCustomerForm(): void
    {
        $this->showCustomerForm = false;
        $this->newCustomerName = '';
        $this->newCustomerPhone = '';
    }

    public function saveNewCustomer(): void
    {
        $this->validate([
            'newCustomerName'  => 'required|string|max:150',
            'newCustomerPhone' => 'nullable|string|max:30',
        ]);

        $customer = Customer::create([
            'name'  => $this->newCustomerName,
            'phone' => $this->newCustomerPhone ?: null,
        ]);

        $this->selectCustomer($customer->id);
        $this->cancelCustomerForm();
    }

    public function getSubtotalProperty(): float
    {
        return collect($this->cart)->sum(fn($i) => $i['price'] * $i['quantity']);
    }

    public function getTotalProperty(): float
    {
        return max(0, $this->subtotal - (float) ($this->discount ?: 0));
    }

    public function getChangeProperty(): float
    {
        if ($this->paymentMethod !== 'cash') {
            return 0;
        }
        return max(0, (float) ($this->amountPaid ?: 0) - $this->total);
    }

    public function completeSale(InventoryService $inventory): void
    {
        if ($this->processing || empty($this->cart)) {
            return;
        }
        $this->processing = true;

        try {
            $total = $this->total;
            $amountPaid = $this->paymentMethod === 'cash' ? (float) ($this->amountPaid ?: 0) : $total;

            if ($this->paymentMethod === 'cash' && $amountPaid < $total) {
                $this->cartError = 'Amount paid is less than the total due.';
                return;
            }

            $cartSnapshot = $this->cart;

            $sale = DB::transaction(function () use ($cartSnapshot, $total, $amountPaid, $inventory) {
                $sale = Sale::create([
                    'sale_number'    => Sale::generateSaleNumber(),
                    'customer_id'    => $this->customerId,
                    'user_id'        => auth()->id(),
                    'subtotal'       => $this->subtotal,
                    'discount'       => (float) ($this->discount ?: 0),
                    'total'          => $total,
                    'payment_method' => $this->paymentMethod,
                    'amount_paid'    => $amountPaid,
                    'change_amount'  => $this->paymentMethod === 'cash' ? max(0, $amountPaid - $total) : 0,
                ]);

                foreach ($cartSnapshot as $item) {
                    $product = Product::findOrFail($item['product_id']);

                    SaleItem::create([
                        'sale_id'      => $sale->id,
                        'product_id'   => $product->id,
                        'product_name' => $product->name,
                        'quantity'     => $item['quantity'],
                        'price'        => $item['price'],
                        'cost_price'   => $product->cost_price,
                        'subtotal'     => $item['price'] * $item['quantity'],
                    ]);

                    $inventory->adjust($product, -$item['quantity'], 'pos_sale', $sale);
                }

                return $sale;
            });

            $this->cart = [];
            $this->discount = '0';
            $this->amountPaid = '';
            $this->customerId = null;
            $this->customerName = null;
            $this->cartError = null;
            $this->completedSaleId = $sale->id;
        } catch (\RuntimeException $e) {
            $this->cartError = $e->getMessage();
        } finally {
            $this->processing = false;
        }
    }

    public function startNewSale(): void
    {
        $this->completedSaleId = null;
    }

    public function render()
    {
        $results = collect();
        if (trim($this->search) !== '') {
            $results = Product::active()->search($this->search)->limit(8)->get();
        }

        $customerResults = collect();
        if (trim($this->customerSearch) !== '' && !$this->customerId) {
            $customerResults = Customer::search($this->customerSearch)->limit(6)->get();
        }

        return view('livewire.admin.pos', [
            'results'          => $results,
            'customerResults'  => $customerResults,
        ])->extends('layouts.admin');
    }
}
