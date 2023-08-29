<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCreditCardRequest extends FormRequest
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
        return [
            'cc-number' => 'required|string', //this can contain spaces
            'name' => 'required|string|max:255',
            'expirationDate' => 'required|string', //this has the / separator in it
            'country' => 'required|string',
            'line1' => 'required|string',
            'city' => 'required|string',
            'state' => 'string',
            'zip' => 'required|string',
            'auto' => 'boolean',
            'cvc' => 'required|numeric',
        ];
    }
}
