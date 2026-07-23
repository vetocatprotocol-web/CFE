<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'category_id',
        'price',
        'description',
        'image_url',
        'current_stock',
        'reorder_point',
        'barcode',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'current_stock' => 'integer',
            'reorder_point' => 'integer',
            'status' => 'string',
        ];
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Get the POS order items for the product.
     */
    public function posOrderItems(): HasMany
    {
        return $this->hasMany(PosOrderItem::class);
    }

    /**
     * Get the billing items for the product.
     */
    public function billingItems(): HasMany
    {
        return $this->hasMany(BillingItem::class);
    }

    /**
     * Get the stock adjustments for the product.
     */
    public function stockAdjustments(): HasMany
    {
        return $this->hasMany(StockAdjustment::class);
    }

    /**
     * Determine if the product is low on stock.
     */
    public function isLowStock(): bool
    {
        return $this->current_stock < $this->reorder_point;
    }
}
