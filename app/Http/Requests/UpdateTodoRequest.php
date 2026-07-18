<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'completed' => ['boolean'],
            'priority' => ['integer', 'between:1,5'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
            'column_id' => ['sometimes', 'exists:columns,id'],
        ];
    }
}
