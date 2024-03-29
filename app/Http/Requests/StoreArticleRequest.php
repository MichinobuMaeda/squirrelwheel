<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
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
            'template_id' => ['required', 'exists:templates,id'],
            'reserved_at' => ['required', 'date'],
            'post_targets' => ['required'],
            'content' => ['nullable'],
            'link' => ['nullable', 'url'],
        ];
    }
}
