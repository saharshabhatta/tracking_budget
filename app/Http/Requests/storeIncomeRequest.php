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
            'income_type' => ['required', 'in:monthly,annual'],
            'monthly_income' => ['nullable', 'numeric', 'min:0', 'required_if:income_type,monthly', 'max:9999999999999999.9999'],
            'annual_income' => ['nullable', 'numeric', 'min:0', 'required_if:income_type,annual', 'max:9999999999999999.9999'],
            'category_percentages' => ['required', 'array'],
            'category_percentages.*' => ['numeric', 'between:0,100'],
        ];

    }
    public function attributes(): array
    {
        return [
            'category_percentages' => 'category percentages',
            'category_percentages.*' => 'category percentage',
        ];
    }

    public function messages(): array
    {
        return [
            'monthly_income.max' => 'The monthly income exceeds the maximum allowable.',
            'annual_income.max' => 'The annual income exceeds the maximum allowable.',
        ];
    }
}
