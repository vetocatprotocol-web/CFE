<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Adjust stock for a product with a reason and audit trail.
     */
    public function adjustStock(
        Product $product,
        int $quantity,
        string $reason,
        ?string $referenceId,
        User $user,
    ): StockAdjustment {
        return DB::transaction(function () use ($product, $quantity, $reason, $referenceId, $user) {
            $newStock = $product->current_stock + $quantity;

            if ($newStock < 0) {
                throw new \InvalidArgumentException(
                    "Insufficient stock for {$product->name}. Current: {$product->current_stock}, Adjustment: {$quantity}"
                );
            }

            $product->update(['current_stock' => $newStock]);

            return StockAdjustment::create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'reason' => $reason,
                'reference_id' => $referenceId,
                'created_by' => $user->id,
                'notes' => "Stock adjusted by {$quantity}. New stock: {$newStock}",
            ]);
        });
    }

    /**
     * Deduct stock for a product (negative quantity adjustment).
     */
    public function deductStock(
        Product $product,
        int $quantity,
        ?string $referenceId,
        User $user,
    ): StockAdjustment {
        if ($product->current_stock < $quantity) {
            throw new \InvalidArgumentException(
                "Insufficient stock for {$product->name}. Available: {$product->current_stock}, Requested: {$quantity}"
            );
        }

        return $this->adjustStock(
            $product,
            -$quantity,
            'stock_deduction',
            $referenceId,
            $user,
        );
    }

    /**
     * Get products that are below their reorder point.
     */
    public function getLowStockProducts()
    {
        return Product::whereColumn('current_stock', '<', 'reorder_point')
            ->with('category')
            ->orderBy('current_stock')
            ->get();
    }
}
