<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;

class CreditCardPaymentRequest extends FormRequest
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
            'cc-number' => 'required_if:payment_method,new_card', //this can contain spaces
            'name' => 'required_if:payment_method,new_card',
            'expirationDate' => 'required_if:payment_method,new_card', //this has the / separator in it
            'makeAuto' => 'boolean',
            'amount' => 'required|numeric|min:0.01',
            'country' => 'required_if:payment_method,new_card',
            'line1' => 'required_if:payment_method,new_card',
            'city' => 'required_if:payment_method,new_card',
            'state' => 'required_if:payment_method,new_card',
            'zip' => 'required_if:payment_method,new_card',
            'cvc' => 'required_if:payment_method,new_card',
        ];
    }
}
