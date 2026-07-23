<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Billing extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'billing_number',
        'customer_id',
        'pet_id',
        'billing_start_date',
        'billing_end_date',
        'status',
        'notes',
        'created_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'billing_start_date' => 'date',
            'billing_end_date' => 'date',
            'status' => 'string',
        ];
    }

    /**
     * Get the customer that owns the billing.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the pet that owns the billing.
     */
    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    /**
     * Get the items for the billing.
     */
    public function items(): HasMany
    {
        return $this->hasMany(BillingItem::class);
    }

    /**
     * Get the invoice for the billing.
     */
    public function invoice(): MorphOne
    {
        return $this->morphOne(Invoice::class, 'source');
    }

    /**
     * Get the user that created the billing.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
