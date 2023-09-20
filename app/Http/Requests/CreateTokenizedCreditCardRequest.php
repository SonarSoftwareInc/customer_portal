<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTokenizedCreditCardRequest extends FormRequest
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
            'customerId' => 'required|string',
            'token' => 'required|string',
            'identifier' => 'required|string',
            'expirationDate' => 'required|string', //this has the / separator in it
            'auto' => 'boolean',
            'line1' => 'required|string',
            'city' => 'required|string',
            'state' => 'string',
            'zip' => 'required|string',
            'country' => 'required|string',
            'name' => 'required|string',
            'cardType' => 'required|string',
        ];
    }
}
