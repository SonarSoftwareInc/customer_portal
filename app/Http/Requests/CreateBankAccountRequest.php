<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBankAccountRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'account_number' => 'required|numeric',
            'account_type' => 'required|string|in:checking,savings',
            'country' => 'required|string',
            'line1' => 'required|string',
            'city' => 'required|string',
            'state' => 'string',
            'zip' => 'required|string',
        ];

        // Canadian routing number validation
        if ($this->input('country') === 'CA') {
            // Check if they're using standard routing (for US banks from Canada)
            if ($this->has('routing_number') && !empty($this->input('routing_number'))) {
                $rules['routing_number'] = 'required|numeric|digits:9';
            } else {
                // Canadian format validation
                $rules['institution_number'] = 'required|numeric|digits:3';
                $rules['transit_number'] = 'required|numeric|digits:5';
            }
        } else {
            $rules['routing_number'] = 'required|numeric|digits:9';
        }

        return $rules;
    }

    /**
     * Get the custom validation messages.
     */
    public function messages(): array
    {
        return [
            'institution_number.required' => 'The institution number is required for Canadian bank accounts.',
            'institution_number.numeric' => 'The institution number must be numeric.',
            'institution_number.digits' => 'The institution number must be exactly 3 digits.',
            'transit_number.required' => 'The transit/branch number is required for Canadian bank accounts.',
            'transit_number.numeric' => 'The transit/branch number must be numeric.',
            'transit_number.digits' => 'The transit/branch number must be exactly 5 digits.',
        ];
    }
}
