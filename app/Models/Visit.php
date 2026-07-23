<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Visit extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'visit_number',
        'customer_id',
        'pet_id',
        'visit_date',
        'visit_time',
        'chief_complaint',
        'physical_exam_notes',
        'diagnosis',
        'treatment_notes',
        'weight_kg',
        'temperature',
        'heart_rate',
        'status',
        'created_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'weight_kg' => 'decimal:2',
            'temperature' => 'decimal:1',
            'heart_rate' => 'integer',
            'status' => 'string',
        ];
    }

    /**
     * Get the customer that owns the visit.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the pet that owns the visit.
     */
    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    /**
     * Get the items for the visit.
     */
    public function items(): HasMany
    {
        return $this->hasMany(VisitItem::class);
    }

    /**
     * Get the invoice for the visit.
     */
    public function invoice(): MorphOne
    {
        return $this->morphOne(Invoice::class, 'source');
    }

    /**
     * Get the user that created the visit.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the prescriptions for the visit.
     */
    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }
}
