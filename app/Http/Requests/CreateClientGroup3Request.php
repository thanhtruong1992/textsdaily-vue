<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateClientGroup3Request extends FormRequest {
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
                'username' => 'required|unique:users,username',
                'email' => 'required|email',
                'password' => 'required|min:6|confirmed',
                'password_confirmation' => 'sometimes|required_with:password',
                'country' => 'required',
                'language' => 'required',
                'time_zone' => 'required',
                'default_price_sms' => 'required'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages() {
        return [
                'name.required' => trans ( 'validationForm.client_name.required' ),
                'username.required'     => trans('validationForm.username.required'),
                'username.unique'     => trans('validationForm.username.unique'),
                'email.required' => trans ( 'validationForm.email.required' ),
                'email.email' => trans ( 'validationForm.email.email' ),
                'password.required' => trans ( 'validationForm.password.required' ),
                'password.confirmed' => trans ( 'validationForm.password.confirmed' ),
                'confirmPassword.required' => trans ( 'validationForm.confirm_password.required' ),
                'country.required' => trans ( 'validationForm.country_select.required' ),
                'language.required' => trans ( 'validationForm.language_select.required' ),
                'time_zone.required' => trans ( 'validationForm.timezone_select.required' ),
                'default_price_sms.required' => trans ( 'validationForm.default_price.required' )
        ];
    }
}
