<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UpdateClientRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
                //
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'nullable|min:6|confirmed',
                'password_confirmation' => 'sometimes|required_with:password',
                'country' => 'required',
                'language' => 'required',
                'time_zone' => 'required',
                'default_price_sms' => 'required|min:0.01'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
                'name.required'     => trans('validationForm.client_name.required'),
                'email.required'  => trans('validationForm.email.required'),
                'email.email'  => trans('validationForm.email.email'),
                'password.confirmed'   => trans('validationForm.password.confirmed'),
                'country.required'   => trans('validationForm.country_select.required'),
                'language.required'   => trans('validationForm.language_select.required'),
                'time_zone.required'   => trans('validationForm.timezone_select.required'),
                'default_price_sms.required'   => trans('validationForm.default_price.required'),
                'default_price_sms.min'   => trans('validationForm.default_price.min'),
        ];
    }
}
