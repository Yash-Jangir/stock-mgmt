<?php

namespace App\Http\Requests;

use App\Enums\AgeGroup;
use App\Enums\Gender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
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
            'name'        => ['required', 'string', 'max:255'],
            'code'        => ['required', Rule::unique('products')->where('user_id', auth()->id()), 'string', 'max:255'],
            'category_id' => ['nullable', Rule::exists('categories', 'id')->where('user_id', auth()->id()), 'numeric'],
            'gender'      => ['nullable', Rule::enum(Gender::class)],
            'age_group'   => ['nullable', Rule::enum(AgeGroup::class)],
            'price'       => ['nullable', 'numeric', 'max_digits:9' ],
            'description' => ['nullable', 'string'],
            'is_active'   => ['nullable'],
            
            // sku
            'color_id'          => ['nullable', 'array'],
            'color_id.*'        => ['numeric', Rule::exists('colors', 'id')->where('user_id', auth()->id())],
            'size_id'           => ['nullable', 'array'],
            'size_id.*'         => ['numeric', Rule::exists('sizes', 'id')->where('user_id', auth()->id())],
            'sku_price'         => ['nullable', 'array'],
            'sku_price.*'       => ['nullable', 'numeric', 'max_digits:9' ],
            'sku_description'   => ['nullable', 'array'],
            'sku_description.*' => ['nullable', 'string'],
            'sku_is_active'     => ['nullable', 'array'],
            'sku_is_active.*'   => ['nullable'],
        ];
    }
}
