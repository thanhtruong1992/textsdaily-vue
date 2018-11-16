<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CustomFields\ICustomFieldService;
use Session;
use Lang;

class CustomFieldController extends Controller
{
    protected $request;
    protected $customFieldService;
    public function __construct(Request $request, ICustomFieldService $customFieldService){
        $this->request = $request;
        $this->customFieldService = $customFieldService;
    }

    public function create() {
        $arrayField = ["phone", "first name", "last name"];
        if(in_array( strtolower($this->request->get('field')) ,$arrayField)) {
            return response()->json([
                "message" => trans('subscriber.custom_field_duplicate')
            ], 400);
        }
        $result = $this->customFieldService->createCustomField($this->request);

        if(!!$result->status) {
            $message = Lang::get ( 'notify.add_custom_field' );
            $data = (object) $result->data;
            return response()->json(['data' => $data, 'message' => $message], 200);
        }

        if (!!is_array($result->error) && count ( $result->error ) > 0) {
            $message = ( object ) $result->error->toArray ();
            $error = count ( $message->field ) > 0 ? $message->field[0] : '';
            if (isset ( $error )) {
                Session::flash ( 'error', $error );
            }
        }else {
            Session::flash ( 'error', $result->error );
            $error = $result->error;
        }
        
        return response()->json([
            "message" => $error
        ], 400);
    }
    
    public function getCustomField(Request $request) {
        $customfield = [["field_name" => 'Phone'], ["field_name" => 'First name'], ["field_name" => 'Last name']];
        $result = $this->customFieldService->getCustomFieldOfSubscriberByColumnForPersonalize($request->list_id, ['field_name']);
        foreach ($result as $item) {
            $customfield[] = [
                    'field_name' => $item->field_name,
//                     'name' => $item->name        
            ];
        }
//         dd($customfield);
        return  [
                'data' => $customfield,
                'recordsTotal' => count($customfield),
                'recordsFiltered' => count($customfield)
        ];
    }
}
