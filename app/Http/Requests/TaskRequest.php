<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:50',
            'description' => 'nullable|string|max:150',
            'category' => 'nullable|string|max:50',
            'due_date' => 'nullable|date',
            'is_completed'=>'bool',
        ];
    }
}
