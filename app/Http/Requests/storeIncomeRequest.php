<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class storeIncomeRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'monthly_income' => ['required', 'numeric'],
            'annual_income' => ['required', 'numeric'],
            'category_percentages' => ['required', 'array'],
            'category_percentages.*' => ['numeric', 'between:0,100'],
        ];
    }
}
