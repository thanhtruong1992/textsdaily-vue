<?php

namespace App\Services\Subscribers;

use Illuminate\Http\Request;
use Validator;

class ValidationSubscriber {
    /**
     *  fn validate upload csv
     * @param Request $request
     */
    public function uploadCSV($request) {
        $validator = Validator::make ( $request->all (), [
                'file' => 'required|max: 100000|mimetypes: text/csv,text/plain',
                'file_terminated' => 'required'
        ] );

        if ($validator->fails ()) {
            return $validator->errors ();
        }
    }

    /**
     * fn validation copyPaste subscriber
     * @param Request $request
     */
    public function copyPaste($request) {
        $validator = Validator::make ( $request->all (), [
                'content' => 'required'
        ] );

        if ($validator->fails ()) {
            return $validator->errors ();
        }
    }
}
