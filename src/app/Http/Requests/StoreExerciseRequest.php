<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreExerciseRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'youtube_url' => ['required', 'url'],
            'start_time' => ['required', 'numeric', 'min:0'],
            'end_time' => ['required', 'numeric', 'min:0'],
            'timer_duration' => ['required', 'integer', 'min:0'],
        ];
    }
}
