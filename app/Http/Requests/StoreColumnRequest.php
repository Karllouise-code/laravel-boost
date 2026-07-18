<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreColumnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $board = $this->route('slug') ? \App\Models\Board::where('slug', $this->route('slug'))->first() : null;
        $maxColumns = $board ? $board->columns()->count() : 0;

        return [
            'name' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) use ($maxColumns) {
                if ($maxColumns >= 10) {
                    $fail('Maximum 10 columns per board.');
                }
            }],
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }
}
