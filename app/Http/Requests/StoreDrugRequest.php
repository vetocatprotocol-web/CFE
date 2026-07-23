<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDrugRequest extends FormRequest
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
        $drugId = $this->route('drug')?->id;

        return [
            'name' => ['required', 'string', 'max:255', 'unique:drugs,name,' . $drugId],
            'description' => ['nullable', 'string'],
            'unit' => ['required', 'string'],
            'price_per_unit' => ['required', 'numeric', 'min:0'],
        ];
    }
}
