<?php

namespace App\Services\CustomFields;

use Validator;

class ValidationCustomField {
    /**
     *  fn validate upload csv
     * @param Request $request
     */
    public function createCustomField($request) {
        $validator = Validator::make ( $request->all (), [
                'field' => 'required',
                'list_id' => "required",
        ] );

        if ($validator->fails ()) {
            return $validator->errors ();
        }
    }
}