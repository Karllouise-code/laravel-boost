<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReorderTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'todo_id' => 'required|integer|exists:todos,id',
            'column_id' => 'required|exists:columns,id',
            'priority' => 'required|integer|min:1',
        ];
    }
}
