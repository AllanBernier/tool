<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateComparisonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'content' => ['nullable', 'string'],
            'verdict' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:160'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'meta_title.max' => 'Le meta title ne peut pas dépasser 70 caractères.',
            'meta_description.max' => 'La meta description ne peut pas dépasser 160 caractères.',
        ];
    }
}
