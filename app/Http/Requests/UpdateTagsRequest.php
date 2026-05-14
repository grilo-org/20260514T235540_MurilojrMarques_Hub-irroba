<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTagsRequest extends FormRequest
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
            'tags' => ['required', 'array', 'min:1'],
            'tags.*' => ['required', 'string', 'alpha_dash', 'max:30'],
        ];
    }

    public function messages(): array
    {
        return [
            'tags.*.alpha_dash' => 'As tags devem conter apenas letras, números e traços.',
        ];
    }
}
