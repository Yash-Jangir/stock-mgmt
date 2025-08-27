<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
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
            'code'          => ['required', Rule::unique('categories')->ignore($this->route('category'), 'id')->where('user_id', auth()->id())],
            'name'          => ['required', 'string', 'max:255'],
            'description'   => ['nullable'],
            'rank'          => ['required', 'integer', 'min:1', 'max:999'],
            'is_active'     => ['nullable'],
        ];
    }
}
