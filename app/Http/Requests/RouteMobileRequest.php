<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RouteMobileRequest extends FormRequest
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
            'sSender' => 'required',
            'sMobileNo' => 'required',
            'sStatus' => 'required',
            'dtSubmit' => 'required',
            'dtDone' => 'required',
            'sMessageId' => 'required',
            'iCostPerSms' => 'required',
            'iCharge' => 'required',
            'iMCCMNC' => 'required',
            'ErrCode' => 'required',
        ];
    }

    public function response(array $errors) {

        // Put whatever response you want here.
        return response()->json([
            'status' => false,
            'errors' => $errors,
        ], 400);
    }
}
