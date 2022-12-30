<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['required', Rule::unique('categories')->ignore($this->id)],
            'priority' => ['required', 'integer', 'min:0', 'max:9'],
            'checked_at' => ['required', 'date'],
            'update_only' => ['required', 'boolean'],
        ];
    }
}
