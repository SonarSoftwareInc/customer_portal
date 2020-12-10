<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Request;

class CreateTokenizedCreditCardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
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
            'legalDisclaimer' => 'accepted'
        ];
    }
}
