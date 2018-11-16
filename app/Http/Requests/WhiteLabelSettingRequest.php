<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WhiteLabelSettingRequest extends FormRequest
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
            //
            'avatar' => 'nullable|mimetypes:image/jpeg,image/png,image/jpg,image/gif,image/svg|max: 25000',
            'host_name' => 'nullable|min:10|url',
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
                'host_name.url' => trans('validationForm.link.url'),
        ];
    }
}
