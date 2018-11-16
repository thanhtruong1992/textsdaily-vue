<?php

namespace App\Services\CustomFields;

use App\Repositories\CustomFields\ICustomFieldRepository;
use App\Services\BaseService;
use Illuminate\Support\Facades\Auth;

class CustomFieldService extends BaseService implements ICustomFieldService{
    protected $customFieldRepo;
    protected $validation;
    public function __construct(ICustomFieldRepository $customFieldRepo, ValidationCustomField $validation) {
        $this->customFieldRepo = $customFieldRepo;
        $this->validation = $validation;
    }

    public function createCustomField($request) {

        $validator= $this->validation->createCustomField( $request );

        if (isset ( $validator)) {
            return $this->fail($validator);
        }else {
            $customField = $this->getCustomFieldDuplicate(Auth::user()->id, $request->get("list_id") , $request['field']);
            if(!!$customField->status) {
                return $this->fail(trans('subscriber.custom_field_duplicate'));
            }
            
            $request['user_id'] = Auth::user()->id;
            $request['field_name'] = $request['field'];
            $request['field_default_value'] = '';
            $request['created_by'] = Auth::user()->id;
            $request['updated_by'] = Auth::user()->id;
            $request['required'] = false;
            $request['unique'] = false;
            $request['global'] = false;
            $result = $this->customFieldRepo->create($request->toArray ());

            $tabelName = "subscribers_l_" . $request->get("list_id");
            $fiedName = "custom_field_" . $result->id;
            $this->customFieldRepo->addCustomfield($tabelName, $fiedName);

            return $this->success($result);
        }
    }

    public function getCustomFieldOfSubscriber($list_id) {
        $userId = Auth::user()->id;
        return $this->customFieldRepo->getCusfomFieldOfSubscriber($list_id, $userId);
    }

    public function getCustomFieldOfSubscriberByColumn($list_id, $columnName = null, $userId= null) {
        if ( is_null($userId) ) {
            $userId = Auth::user()->id;
        }
        if ( is_null( $columnName ) ) {
            $columnName = ["field_name"];
        }
        $array_list_id = explode(",", $list_id);
        return $this->customFieldRepo->getCusfomFieldOfSubscriberByColumn($userId, $array_list_id, $columnName);
    }
    
    public function getCustomFieldOfSubscriberByColumnForPersonalize($list_id, $columnName = null, $userId= null) {
        if ( is_null($userId) ) {
            $userId = Auth::user()->id;
        }
        if ( is_null( $columnName ) ) {
            $columnName = ["field_name"];
        }
        $array_list_id = explode(",", $list_id);
        return $this->customFieldRepo->getCusfomFieldOfSubscriberByColumnForPersonalize($userId, $array_list_id, $columnName);
    }

    public function getCustomFieldDuplicate($userId, $list_id, $fiedName ) {
        $result = $this->customFieldRepo->findWhere([
            "user_id" => $userId,
            "list_id" => $list_id,
            "field_name" => $fiedName
        ])->first();
        if(empty($result)) {
            return $this->fail();
        }

        return $this->success($result);
    } 
}
