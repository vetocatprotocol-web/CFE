<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'category',
        'price',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'status' => 'string',
        ];
    }

    /**
     * Get the visit items for the service.
     */
    public function visitItems(): HasMany
    {
        return $this->hasMany(VisitItem::class);
    }

    /**
     * Get the billing items for the service.
     */
    public function billingItems(): HasMany
    {
        return $this->hasMany(BillingItem::class);
    }
}
