<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateReaderRequest extends FormRequest {
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
                'reader_id' => 'required',
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6|confirmed',
                'password_confirmation' => 'sometimes|required_with:password' 
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
                'reader_id.required' => trans('validationForm.reader_id.required'),
                'name.required'     => trans('validationForm.client_name.required'),
                'email.required'  => trans('validationForm.email.required'),
                'email.email'  => trans('validationForm.email.email'),
                'password.required'   => trans('validationForm.password.required'),
                'password.confirmed'   => trans('validationForm.password.confirmed'),
                'confirmPassword.required'   => trans('validationForm.confirm_password.required'),
        ];
    }
}
