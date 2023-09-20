<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TokenizedCreditCardPaymentRequest extends FormRequest
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
            'customerId' => 'required_if:payment_method,new_card',
            'token' => 'required_if:payment_method,new_card',
            'identifier' => 'required_if:payment_method,new_card',
            'name' => 'required_if:payment_method,new_card',
            'expirationDate' => 'required_if:payment_method,new_card', //this has the / separator in it
            'makeAuto' => 'boolean',
            'amount' => 'required|numeric|min:0.01',
            'country' => 'required_if:payment_method,new_card',
            'line1' => 'required_if:payment_method,new_card',
            'city' => 'required_if:payment_method,new_card',
            'state' => 'required_if:payment_method,new_card',
            'zip' => 'required_if:payment_method,new_card',
        ];
    }
}
