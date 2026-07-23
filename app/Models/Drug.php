<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Drug extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'unit',
        'price_per_unit',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_per_unit' => 'decimal:2',
            'status' => 'string',
        ];
    }

    /**
     * Get the visit items for the drug.
     */
    public function visitItems(): HasMany
    {
        return $this->hasMany(VisitItem::class);
    }

    /**
     * Get the billing items for the drug.
     */
    public function billingItems(): HasMany
    {
        return $this->hasMany(BillingItem::class);
    }

    /**
     * Get the prescription items for the drug.
     */
    public function prescriptionItems(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class);
    }
}
