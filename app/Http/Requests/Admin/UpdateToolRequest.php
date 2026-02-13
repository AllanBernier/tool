<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateToolRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', Rule::unique('tools', 'name')->ignore($this->route('tool'))],
            'url' => ['required', 'url', 'max:500'],
            'category_id' => ['required', 'exists:categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
            'description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'pricing' => ['nullable', 'array'],
            'pros' => ['nullable', 'array'],
            'cons' => ['nullable', 'array'],
            'features' => ['nullable', 'array'],
            'faq' => ['nullable', 'array'],
            'platforms' => ['nullable', 'array'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'name.unique' => 'Ce nom d\'outil existe déjà.',
            'url.required' => "L'URL est obligatoire.",
            'url.url' => "L'URL n'est pas valide.",
            'url.max' => "L'URL ne peut pas dépasser 500 caractères.",
            'category_id.required' => 'La catégorie est obligatoire.',
            'category_id.exists' => "La catégorie sélectionnée n'existe pas.",
            'tags.array' => 'Les tags doivent être un tableau.',
            'tags.*.exists' => "Le tag sélectionné n'existe pas.",
        ];
    }
}
