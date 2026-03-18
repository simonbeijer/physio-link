<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSchemaExerciseRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'exercises' => ['required', 'array'],
            'exercises.*.exercise_id' => ['required', 'integer'],
            'exercises.*.order' => ['required', 'integer'],
            'exercises.*.comfort_level' => ['nullable', 'integer'],
        ];
    }
}
