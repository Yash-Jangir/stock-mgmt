<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSellRequest extends FormRequest
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
            'customer_name' => 'required|string|max:255',
            'address'       => 'nullable|string|max:255',
            'gst_number'    => 'nullable|string|max:255',
            'contact_no'    => 'nullable|string|max:15',
            'email'         => 'nullable|string|max:255',
            'total_price'   => 'nullable|numeric',
            'discount'      => 'nullable|integer|max:99|min:0',

            'product_id'    => 'array|required',
            'product_id.*'  => 'required|string|max:255',
            'qty'           => 'array|required',
            'qty.*'         => 'required|numeric|min:1',
            'unit_price'    => 'array|required',
            'unit_price.*'  => 'required|numeric|min:1',
            'price'         => 'array|required',
            'price.*'       => 'required|numeric|min:1',
        ];
    }
}
