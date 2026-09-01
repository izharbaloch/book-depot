<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Adjust a product's stock and record the movement. This is the only
     * place stock should ever be changed, so POS, online orders, and
     * purchases can never drift out of sync with each other.
     *
     * @param  int  $quantity  positive = stock in, negative = stock out
     */
    public function adjust(Product $product, int $quantity, string $type, ?Model $reference = null, ?string $notes = null): StockMovement
    {
        return DB::transaction(function () use ($product, $quantity, $type, $reference, $notes) {
            $product = Product::whereKey($product->id)->lockForUpdate()->first();

            $newStock = $product->stock + $quantity;
            if ($newStock < 0) {
                throw new \RuntimeException("Insufficient stock for \"{$product->name}\": only {$product->stock} available.");
            }

            $product->update(['stock' => $newStock]);

            return StockMovement::create([
                'product_id'     => $product->id,
                'type'           => $type,
                'quantity'       => $quantity,
                'balance_after'  => $newStock,
                'reference_type' => $reference?->getMorphClass(),
                'reference_id'   => $reference?->getKey(),
                'created_by'     => auth()->id(),
                'notes'          => $notes,
            ]);
        });
    }

    /**
     * Receive purchased stock and roll it into the product's weighted-average
     * cost, so the same product can be bought at different rates over time
     * without ever overwriting its purchase history.
     */
    public function receivePurchase(Product $product, int $quantity, float $unitCost, Model $reference, ?string $notes = null): StockMovement
    {
        return DB::transaction(function () use ($product, $quantity, $unitCost, $reference, $notes) {
            $product = Product::whereKey($product->id)->lockForUpdate()->first();

            $oldStock = $product->stock;
            $oldCost  = (float) $product->cost_price;
            $newStock = $oldStock + $quantity;
            $newCost  = (($oldStock * $oldCost) + ($quantity * $unitCost)) / $newStock;

            $product->update(['stock' => $newStock, 'cost_price' => round($newCost, 2)]);

            return StockMovement::create([
                'product_id'     => $product->id,
                'type'           => 'purchase',
                'quantity'       => $quantity,
                'balance_after'  => $newStock,
                'reference_type' => $reference->getMorphClass(),
                'reference_id'   => $reference->getKey(),
                'created_by'     => auth()->id(),
                'notes'          => $notes,
            ]);
        });
    }
}
