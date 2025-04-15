<?php

namespace App\Http\Requests;

use App\Rules\FutureDateRule;
use Illuminate\Foundation\Http\FormRequest;

class storeExpenseRequest extends FormRequest
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
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string',
            'date' => ['required', 'date', new FutureDateRule()],
        ];
    }
}
