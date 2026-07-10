<?php

namespace App\Http\Requests;

use App\Models\Board;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddCollaboratorRequest extends FormRequest
{
    public function authorize(): bool
    {
        $board = Board::where('slug', $this->route('slug'))->firstOrFail();

        return $this->user()->can('manageCollaborators', $board);
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', Rule::exists('users', 'email')],
        ];
    }
}
