<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DestroyTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $todo = $this->route('todo');

        return $todo && $this->user()->id === $todo->user_id;
    }

    public function rules(): array
    {
        return [];
    }
}
