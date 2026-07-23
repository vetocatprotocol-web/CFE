<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payable_type' => ['required', 'string', 'in:visit,billing,pos_order'],
            'payable_id' => ['required', 'integer'],
            'payment_method' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ];
    }
}
