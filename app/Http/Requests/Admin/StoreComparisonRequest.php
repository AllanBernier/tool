<?php

namespace App\Http\Requests\Admin;

use App\Models\Comparison;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreComparisonRequest extends FormRequest
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
            'tool_a_id' => ['required', 'exists:tools,id'],
            'tool_b_id' => ['required', 'exists:tools,id', 'different:tool_a_id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tool_a_id.required' => 'L\'outil A est obligatoire.',
            'tool_a_id.exists' => 'L\'outil A sélectionné n\'existe pas.',
            'tool_b_id.required' => 'L\'outil B est obligatoire.',
            'tool_b_id.exists' => 'L\'outil B sélectionné n\'existe pas.',
            'tool_b_id.different' => 'L\'outil B doit être différent de l\'outil A.',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $exists = Comparison::query()
                    ->where(function ($query) {
                        $query->where('tool_a_id', $this->input('tool_a_id'))
                            ->where('tool_b_id', $this->input('tool_b_id'));
                    })
                    ->orWhere(function ($query) {
                        $query->where('tool_a_id', $this->input('tool_b_id'))
                            ->where('tool_b_id', $this->input('tool_a_id'));
                    })
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('tool_b_id', 'Cette paire d\'outils a déjà une comparaison.');
                }
            },
        ];
    }
}
