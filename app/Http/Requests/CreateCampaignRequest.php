<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCampaignRequest extends FormRequest
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
            'name'      => 'required',
            'list_id'   => 'required',
            'sender'    => 'required',
            'language'  => 'required',
            'message'   => 'required',
            'schedule_type'   => 'required',
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
                'name.required'     => trans('validationForm.campaign_name.required'),
                'list_id.required'  => trans('validationForm.subscriber_list_name.required'),
                'sender.required'   => trans('validationForm.sender.required'),
                'message.required'  => trans("validationForm.message.required"),
        ];
    }
}
