<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:140'],
            'description' => ['nullable', 'string', 'max:2000'],
            'priority' => ['required', 'string', 'in:low,normal,high,urgent'],
            'category' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * Get the validation messages in French.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est obligatoire.',
            'title.max' => 'Le titre doit faire 140 caracteres maximum.',
            'description.max' => 'La description doit faire 2000 caracteres maximum.',
            'priority.required' => 'La priorite est obligatoire.',
            'priority.in' => 'La priorite selectionnee est invalide.',
            'category.max' => 'La categorie doit faire 50 caracteres maximum.',
        ];
    }
}
