<?php

namespace App\Services\Subscribers;

use App\Services\BaseService;
use App\Repositories\Subscribers\ISubscriberRepository;
use Illuminate\Support\Facades\Auth;
use App\Services\UploadService;
use App\Services\Subscribers\ValidationSubscriber;
use Session;
use App\Repositories\CustomFields\ICustomFieldRepository;
use App\Facades\CustomLog;
use App\Services\Settings\IMobilePatternService;
use App\Services\Settings\IPreferredServiceProviderService;
use App\Services\SubscriberLists\ISubscriberListService;
use Carbon\Carbon;
use App\Repositories\Auth\IAuthenticationRepository;
use App\Services\Campaign\ICampaignService;
use App\Services\Campaign\IQueueService;

class SubscriberService extends BaseService implements ISubscriberService {
    protected $subscriberRepo;
    protected $validation;
    protected $uploadFile;
    protected $customFieldRepo;
    protected $mobilePatternService;
    protected $preferredServiceProviderService;
    protected $subscriberListService;

    public function __construct(ISubscriberRepository $subscriberRepo, ValidationSubscriber $validation, UploadService $uploadFile, ICustomFieldRepository $customFieldRepo, IMobilePatternService $IMobilePatternService, IPreferredServiceProviderService $IPreferredServiceProviderService, ISubscriberListService $ISubscriberListService) {
        $this->subscriberRepo = $subscriberRepo;
        $this->validation= $validation;
        $this->uploadFile = $uploadFile;
        $this->customFieldRepo = $customFieldRepo;
        $this->mobilePatternService = $IMobilePatternService;
        $this->preferredServiceProviderService = $IPreferredServiceProviderService;
        $this->subscriberListService = $ISubscriberListService;
    }
    public function getListSubscribers($listId, $request) {
        // find subscriler list
        $subscrilerList = $this->subscriberListService->getSubscriberList($listId);

        if(!$subscrilerList->status) {
            return $this->fail();
        }

        $paging = $request->get ( 'page' );
        $search_key = $request->get ( 'search' );
        $column_sort = $request->get ( 'field' );
        $orderBy = $request->get ( 'orderBy' );
        $filter = (array)json_decode($request->get("filter"));
        $flagFilter = $request->get('flagFilter');
        $result = $this->subscriberRepo->getListSubscribers($listId, $paging, $search_key, $column_sort, $orderBy, $filter, $flagFilter);
        return $this->success($result);
    }
    public function uploadCSV($request, $key = null) {
        $validator= $this->validation->uploadCSV ( $request );

        if (isset ( $validator)) {
            return $this->fail($validator);
        }else {
            $userId = Auth::user()->id;

            $pathFile = config("constants.path_file_subscriber"). md5($userId);
            $file = $request->file('file');
            $fileName= $this->uploadFile->uploadFile($file, $pathFile);
            $line = $request->get('check_header') == "on" ? 2 : 1;
            $file_terminated = $request->get("file_terminated", ",");
            $file_enclosed = $request->get("file_enclosed", '"');

            $this->sessionFileCSV($fileName, $line, $file_terminated, $file_enclosed, $key);

            return $this->success(['file_name' => $fileName]);
        }
    }
    public function copyPaste($request, $key = null) {
        $validator= $this->validation->copyPaste ( $request );

        if (isset ( $validator)) {
            return $this->fail($validator);
        }else {
            $userId = Auth::user()->id;

            $pathFile = config("constants.path_file_subscriber"). md5($userId) . "/";
            $content = $request->get('content');
            $fileName = $this->uploadFile->saveFileCSV($pathFile, "", $content) . ".csv";
            $file_terminated = $request->get("file_terminated", ",");
            $file_enclosed = $request->get("file_enclosed", '"');

            $this->sessionFileCSV($fileName, 1, $file_terminated, $file_enclosed, $key);

            return $this->success(['file_name' => $fileName]);
        }
    }
    public function getCustomFieldOfUser($listID) {
        $userId = Auth::user()->id;
        $customFields = $this->subscriberRepo->getCustomFieldOfUser($userId, $listID);

        return $customFields;
    }
    public function readFileCSV($key = null) {
        $userId = Auth::user()->id;

        // read file csv
        $pathFile = config("constants.path_file_subscriber"). md5($userId);
        if($key == 'update') {
            $fileName = Session::get('fileNameUpdate');
            $line = Session::get('lineFileUpdate');
            $file_terminated = Session::get('fileTerminatedUpdate');
            $file_enclosed = Session::get('fileEnclosedUpdate');
        }else {
            $fileName = Session::get('fileName');
            $line = Session::get('lineFile');
            $file_terminated = Session::get('fileTerminated');
            $file_enclosed = Session::get('fileEnclosed');
        }
        $data = $this->subscriberRepo->readFile(public_path($pathFile . '/' . $fileName), $line, $file_terminated, $file_enclosed);
        Session::put('totalField', count($data));

        return $data ? $data : [];
    }
    public function importFileCSV($request) {
        $userId = Auth::user()->id;
        $totalField = Session::get('totalField');
        $stringCol = "";
        $stringField = "";
        $stringUpdate = "";
        for($i=1; $i <= $totalField; $i++) {
            $stringCol = $stringCol == "" ? "@col" . $i : $stringCol . ", " . "@col" . $i;
            $value = $request->get("field_" . $i);
            if($value != "") {
                $stringField = $stringField == "" ? $value . " = @col" . $i : $stringField . ", " . $value . " = @col" . $i;
                $stringUpdate = $stringUpdate == "" ? $value : $stringUpdate . "," . $value;
            }
        }
        $stringField = $stringField . ", created_by = " . $userId . ", updated_by = ". $userId .", created_at = '" . Carbon::now() . "', updated_at = '" . Carbon::now() ."'";
        $list_id = $request->get("list_id");
        $tableName = "subscribers_l_" . $list_id;
        $tableTempName = "subscribers_temp_l_" . $list_id;
        $pathFile = config("constants.path_file_subscriber"). md5($userId);
        $fileName = public_path($pathFile . '/' . Session::get('fileName'));
        $file_terminated = Session::get('fileTerminated');
        $file_enclosed = Session::get('fileEnclosed');
        $line = Session::get('lineFile') - 1;
        $breakLine = Session::get('breakLine');
        $updateSubscriber = $request->get('update_subscriber') == "on" ? true : false;

        // create table subscribers temp
        $closeTable = $this->subscriberRepo->cloneTableSubscriber($tableName, $tableTempName);
        if(!!$closeTable) {
            // import file csv
            $import = $this->subscriberRepo->importSubscribersCSV($fileName, $stringField, $tableTempName, $line, $file_terminated, $file_enclosed, $breakLine, $stringCol);
            if(!!$import) {
                $result = $this->subscriberRepo->moveDataSubscribers($tableTempName, $tableName, $updateSubscriber, $stringUpdate, $list_id, $userId);
                if(isset($result)) {
                    if(isset($result[0])) {
                        $result = $result[0];
                    }

                    $result->TotalDuplicates = $result->DuplicateData == "" ? 0 : count(explode("\n", $result->DuplicateData));
                    $result->TotalInvalid= $result->InvalidData == "" ? 0 : count(explode("\n", $result->InvalidData));
                    $result->fileDuplicate = "";
                    $result->fileInvalid = "";

                    $arrHeader = [];
                    // header when create file duplicate and invalid
                    foreach (explode(",", $stringField) as $item) {
                        switch (trim($item)) {
                            case 'phone':
                                array_push($arrHeader, 'Phone');
                                break;
                            case 'first_name':
                                array_push($arrHeader, 'First Name');
                                break;
                            case 'last_name':
                                array_push($arrHeader, 'Last Name');
                                break;
                            default: {
                                $arrData = explode("custom_field_", $item);
                                if(isset($arrData[1])) {
                                    $customField = $this->customFieldRepo->find($arrData[1]);
                                    if(isset($customField)) {
                                        array_push($arrHeader, $customField->field_name);
                                    }
                                }
                                break;
                            }
                        }
                    }
                    if(isset($result->DuplicateData)) {
                        //create file csv duplicate when imported
                        $fileDuplicate = $this->uploadFile->saveFileCSV(config("constants.path_file_result_import_subscriber"), implode(",", $arrHeader), $result->DuplicateData);
                        $result->fileDuplicate = $fileDuplicate;
                    }
                    if(isset($result->InvalidData)) {
                        // create file csv invalid when imported
                        $fileInvalid = $this->uploadFile->saveFileCSV(config("constants.path_file_result_import_subscriber"), implode(",", $arrHeader), $result->InvalidData);
                        $result->fileInvalid = $fileInvalid;
                    }

                    // write log
                    CustomLog::info ( 'Import Subscriber', 'Import_Subscriber_User_' . $userId . '_List_' . $list_id . '_' . date ( 'd-m-Y_H:i' ), (array) $result);
                    // delete file CSV
                    $this->uploadFile->removeFile($pathFile, Session::get('fileName'));
                    // remove session
                    $this->removeSessionFile();
                    // Active status detect subscriber
                    $this->subscriberListService->updateSubscriberList($list_id, [
                            'detect_status' => 'PENDING'
                    ]);
                    return $this->success($result);
                }

                return $this->fail();
            }

            return $this->fail();
        }

        return $this->fail();
    }
    public function exportSubscribers($listId, $request) {
        try {
            $user = Auth::user();
            $userId = $user->id;
            $enscrypted = $request->get('enscrypted', false);
            $arrField = [];

            if(!!$user->encrypted || $enscrypted == 'true') {
                array_push($arrField, "ENCRYPT_PHONE(phone) AS phone");
            }else {
                array_push($arrField, "IFNULL(phone, '') AS phone");
            }
            array_push($arrField, "IFNULL(first_name, '') AS first_name");
            array_push($arrField, "IFNULL(last_name, '') AS last_name");
            $headerExport = array(
                    "'Phone'",
                    "'First Name'",
                    "'Last Name'",
            );
            $customFields = $this->customFieldRepo->getCusfomFieldOfSubscriber($listId, $userId);
            foreach ($customFields->toArray() as $item) {
                array_push($arrField, "IFNULL(custom_field_" . $item['id'] . ", '') AS custom_field_" . $item['id']);
                array_push($headerExport, "'".$item['field_name']."'");
            }
            $filter = (array)json_decode($request->get("filter"));
            $flagFilter = $request->get('flagFilter');
            $tableName = "subscribers_l_" . $listId;


            $file_name = 'export_subscribers_' . time() . '.csv';
            $path = config("constants.path_file_export_subscriber"). md5($userId);
            $fileName= public_path($path. "/" .$file_name);
            $this->uploadFile->makeForder($path);
            //$fileName= "/var/www/html/abc/" .$file_name;
            $this->subscriberRepo->exportSubscribersCSV($tableName, $arrField, $fileName, $filter, $flagFilter, $headerExport);

            return ($fileName);
        }catch(\Exception $e) {
            return false;
        }

    }
    public function deleteSubscribers($list_id, $request) {
        try {
            $userId = Auth::user()->id;
            $tableCampaign = "campaign_recipients_u_" . $userId;
            $tableSubscriber = "subscribers_l_" . $list_id;
            $ids = $request->get("ids");
            if(empty($ids)) {
                return $this->fail();
            }

            $result = $this->subscriberRepo->deleteSubscribers($ids, $tableCampaign, $tableSubscriber);
            if(!$result) {
                return $this->fail();
            }

            return $this->success();

        }catch(\Exception $e) {
            return false;
        }
    }
    public function downloadCSV($hash) {
        try {
            $path = config("constants.path_file_result_import_subscriber");
            $filename = $hash . ".csv";
            return $this->uploadFile->checkFile($path . "/" . $filename);
        }catch(\Exception $e) {
            return false;
        }
    }

    public function updateSubscriber( $attributes, $id, $listId )
    {
       return $this->subscriberRepo->updateByListId( $attributes, $id, $listId );
    }

    public function updateStatus($request, $key = null) {
        $userId = Auth::user()->id;
        $totalField = Session::get('totalField');
        $stringCol = "";
        $stringField = "";
        $stringUpdate = "";

        if($key == 'copy-paste') {
            $stringCol = "@col1";
            $stringField = "phone = @col1";
            $stringUpdate = "phone";
        }else {
            for($i=1; $i <= $totalField; $i++) {
                $stringCol = $stringCol == "" ? "@col" . $i : $stringCol . ", " . "@col" . $i;
                $value = $request->get("field_" . $i);
                if($value != "") {
                    $stringField = $stringField == "" ? $value . " = @col" . $i : $stringField . ", " . $value . " = @col" . $i;
                    $stringUpdate = $stringUpdate == "" ? $value : $stringUpdate . "," . $value;
                }
            }
        }

        $list_id = $request->get("list_id");
        $tableName = "subscribers_l_" . $list_id;
        $tableTempName = "subscribers_update_l_" . $list_id;
        $pathFile = config("constants.path_file_subscriber"). md5($userId);
        $fileName = public_path($pathFile . '/' . Session::get('fileNameUpdate'));
        $file_terminated = Session::get('fileTerminatedUpdate');
        $file_enclosed = Session::get('fileEnclosedUpdate');
        $line = Session::get('lineFileUpdate') - 1;
        $breakLine = '\r\n';
        $updateSubscriber = $request->get('update_subscriber') == "on" ? true : false;
        $statusSub = $request->get('status', 'SUBSCRIBED');

        // create table subscribers temp
        $closeTable = $this->subscriberRepo->cloneTableSubscriber($tableName, $tableTempName);
        if(!!$closeTable) {
            // import file csv
            $import = $this->subscriberRepo->importSubscribersCSV($fileName, $stringField, $tableTempName, $line, $file_terminated, $file_enclosed, $breakLine, $stringCol);
            if(!!$import) {
                $result = $this->subscriberRepo->moveDataSubscribers($tableTempName, $tableName, $updateSubscriber, $stringUpdate, $list_id, $userId, $statusSub, true);
                if(isset($result)) {
                    if(isset($result[0])) {
                        $result = $result[0];
                    }
                    if(empty($result->SkipData)) {
                        $result->SkipData = "";
                    }

                    $result->TotalDuplicates = $result->DuplicateData == "" ? 0 : count(explode("\n", $result->DuplicateData));
                    $result->TotalInvalid= $result->InvalidData == "" ? 0 : count(explode("\n", $result->InvalidData));
                    $result->TotalSkip= $result->SkipData == "" ? 0 : count(explode("\n", $result->SkipData));
                    $result->fileDuplicate = "";
                    $result->fileInvalid = "";
                    $result->fileSkip = "";

                    $arrHeader = [];
                    // header when create file duplicate and invalid
                    foreach (explode(",", $stringField) as $item) {
                        switch (trim($item)) {
                            case 'phone':
                                array_push($arrHeader, 'Phone');
                                break;
                            case 'first_name':
                                array_push($arrHeader, 'First Name');
                                break;
                            case 'last_name':
                                array_push($arrHeader, 'Last Name');
                                break;
                            default: {
                                $arrData = explode("custom_field_", $item);
                                if(isset($arrData[1])) {
                                    $customField = $this->customFieldRepo->find($arrData[1]);
                                    if(isset($customField)) {
                                        array_push($arrHeader, $customField->field_name);
                                    }
                                }
                                break;
                            }
                        }
                    }
                    if(isset($result->DuplicateData)) {
                        //create file csv duplicate when imported
                        $fileDuplicate = $this->uploadFile->saveFileCSV(config("constants.path_file_result_import_subscriber"), implode(",", $arrHeader), $result->DuplicateData);
                        $result->fileDuplicate = $fileDuplicate;
                    }
                    if(isset($result->InvalidData)) {
                        // create file csv invalid when imported
                        $fileInvalid = $this->uploadFile->saveFileCSV(config("constants.path_file_result_import_subscriber"), implode(",", $arrHeader), $result->InvalidData);
                        $result->fileInvalid = $fileInvalid;
                    }
                    if(isset($result->SkipData)) {
                        // skip data
                        $fileSkip = $this->uploadFile->saveFileCSV(config("constants.path_file_result_import_subscriber"), implode(",", $arrHeader), $result->SkipData);
                        $result->fileSkip = $fileSkip;
                    }

                    // write log
                    CustomLog::info ( 'Import Subscriber', 'Import_Subscriber_User_' . $userId . '_List_' . $list_id . '_' . date ( 'd-m-Y_H:i' ), (array) $result);

                    // delete file CSV
                    $this->uploadFile->removeFile($pathFile, Session::get('fileNameUpdate'));
                    // remove session
                    $this->removeSessionFile('update');
                    return $this->success($result);
                }

                return $this->fail();
            }

            return $this->fail();
        }

        return $this->fail();
    }

    public function sessionFileCSV($fileName, $lineFile, $fileTerminated, $fileEnclosed, $key) {
        if($key == 'update') {
            Session::put('fileNameUpdate', $fileName);
            Session::put('lineFileUpdate', $lineFile);
            Session::put('fileTerminatedUpdate', $fileTerminated);
            Session::put('fileEnclosedUpdate', $fileEnclosed);
        }else {
            Session::put('fileName', $fileName);
            Session::put('lineFile', $lineFile);
            Session::put('fileTerminated', $fileTerminated);
            Session::put('fileEnclosed', $fileEnclosed);
        }
    }

    public function removeSessionFile($key = null) {
        if($key == "update") {
            Session::forget("fileTerminatedUpdate");
            Session::forget("fileEnclosedUpdate");
            Session::forget("lineFileUpdate");
            Session::forget("fileNameUpdate");
            Session::forget("breakLine");
        }else {
            Session::forget("fileTerminated");
            Session::forget("fileEnclosed");
            Session::forget("lineFile");
            Session::forget("fileName");
            Session::forget("breakLine");
        }
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Subscribers\ISubscriberService::getSubscriberInfo()
     */
    public function getSubscriberInfo( $idList, $id ) {
        return $this->subscriberRepo->findSubscriber( $idList, $id );
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Subscribers\ISubscriberService::detectSubscribers()
     */
    public function detectSubscribers( $idList, $limit = 1000 ) {
        $resultTotals = ['PENDING' => 0, 'PROCESSING' => 0, 'PROCESSED' => 0];
        // Get subscribers data
        $subscribers = $this->subscriberRepo->getDetectData( $idList, $limit );
        if ( $subscribers ) {
            $allMobilePattern = $this->mobilePatternService->fetchAll();
            //
            $updateProcessedData = [];
            foreach ( $subscribers as $key => $subscriber ) {
                foreach ( $allMobilePattern as $mobilePattern ) {
                    if (
                            strlen( $mobilePattern->number_pattern) == strlen($subscriber->phone) &&
                            strpos( $subscriber->phone, preg_replace('/[*]/', '', $mobilePattern->number_pattern)) === 0
                    ) {
                        $updateProcessedData[$mobilePattern->country][$mobilePattern->network][] = $subscriber->id;
                        unset($subscribers[$key]);
                    }
                }
            }
            // Update data processed
            $allPreferredServiceProvider = $this->preferredServiceProviderService->fetchAllPreferredGroupByCountryNetwork();
            foreach ( $updateProcessedData as $country => $networks ) {
                foreach ( $networks as $network => $subscriberIds ) {
                    //
                    $subscriberDataUpdate = [];
                    $subscriberDataUpdate['country'] = $country;
                    $subscriberDataUpdate['network'] = $network;
                    $subscriberDataUpdate['detect_status'] = 'PROCESSED';
                    $subscriberDataUpdate['detect_updated_at'] = date('Y-m-d H:i:s');
                    // Detect Preferred service provider
                    if ( isset($allPreferredServiceProvider[$country]) && isset($allPreferredServiceProvider[$country][$network])) {
                        $subscriberDataUpdate['service_provider'] = $allPreferredServiceProvider[$country][$network];
                    }
                    //
                    $this->subscriberRepo->updateByListId($subscriberDataUpdate, $subscriberIds, $idList);
                }
            }
            // Update data pending
            if ( count($subscribers->toArray()) ) {
                $updatePendingData = [];
                foreach ( $subscribers as $sub ) {
                    $updatePendingData[] = $sub->id;
                }
                $this->subscriberRepo->updateByListId([
                        'detect_status' => 'PENDING',
                        'detect_updated_at' => date('Y-m-d H:i:s')
                ], $updatePendingData, $idList);
            }
            // Count totals
            $statusTotals = $this->subscriberRepo->countTotalsByDetectStatus( $idList );
            foreach ( $statusTotals as $item ) {
                $resultTotals[$item->detect_status] = $item->totals;
            }
            //
            return $resultTotals;
        }
        return $resultTotals;
    }

    /**
     * FN delete subscriber with status
     * {@inheritDoc}
     * @see \App\Services\Subscribers\ISubscriberService::deleteSubscribersWithStatus()
     */
    public function deleteSubscribersWithStatus($listID, $request) {
        $user = Auth::user();
        $userId = $user->id;
        $supperssed = $this->subscriberListService->getGlobalSuppressionList($user->type == "GROUP4" ? $user->reader_id : $user->id);
        $status = $request->get('status', '');
        $flagSupperssed = $request->get("flagSupperssed", "") == "on" ? true : false;
        if($status == "MOBILE") {
            $pathFile = config("constants.path_file_subscriber"). md5($userId) . "/";
            $content = $request->get('content');
            $fileName = $this->uploadFile->saveFileCSV($pathFile, "", $content) . ".csv";
            $file = public_path($pathFile .$fileName);
            $file_terminated = ",";
            $file_enclosed = '"';
            $line = 0;
            $breakLine = "\r\n";
            $tableName = "subscribers_l_" . $listID;
            $tableTempName = "subscribers_remove_l_" . $listID;
            $stringField = "phone = @col1";
            $stringCol = "@col1";
            // create table subscribers temp remove
            $closeTable = $this->subscriberRepo->cloneTableSubscriber($tableName, $tableTempName);

            // import file csv
            $import = $this->subscriberRepo->importSubscribersCSV($file, $stringField, $tableTempName, $line, $file_terminated, $file_enclosed, $breakLine, $stringCol);

            // delete file CSV
            $this->uploadFile->removeFile($pathFile, $fileName);
        }
        $result = $this->subscriberRepo->deleteSubscribersWithStatus($listID, $supperssed->id, $status, $flagSupperssed);

        if($result) {
            $result = $result[0];
            $data = [];
            $arrHeader = ["Phone"];

            if($result->DataRemove!= "") {
                //create file csv remove when remove
                $fileRemove= $this->uploadFile->saveFileCSV(config("constants.path_file_result_import_subscriber"), implode(",", $arrHeader), $result->DataRemove);
                $data['fileRemove'] = $fileRemove;
            }
            if($result->DataSkip != "") {
                // create file csv skip when remove
                $fileSkip = $this->uploadFile->saveFileCSV(config("constants.path_file_result_import_subscriber"), implode(",", $arrHeader), $result->DataSkip);
                $data['fileSkip'] = $fileSkip;
            }
            if($result->DataInvalid != "") {
                // create file csv invalid when remove
                $fileInvalid = $this->uploadFile->saveFileCSV(config("constants.path_file_result_import_subscriber"), implode(",", $arrHeader), $result->DataInvalid);
                $data['fileInvalid'] = $fileInvalid;
            }

            // write log
            CustomLog::info ( 'Remove Subscriber', 'Remove_Subscriber_User_' . $userId . '_List_' . $listID. '_' . date ( 'd-m-Y_H:i' ), (array) $result);

            if($status != "MOBILE") {
                $data['TotalSubscribers'] = $result->DataRemove != "" ? count(explode("\n", $result->DataRemove)) : 0;
            }else {
                $data['TotalSubscribers'] = $result->TotalSubscribers;
            }
            $data['TotalInvalid'] = $result->DataInvalid != "" ? count(explode("\n", $result->DataInvalid)) : 0;
            $data['TotalRemove'] = $result->DataRemove!= "" ? count(explode("\n", $result->DataRemove)) : 0;
            $data['TotalSkip'] = $result->DataSkip != "" ? count(explode("\n", $result->DataSkip)) : 0;

            return $this->success((object) $data);
        }

        return $this->fail();
    }

    public function viewExport($listId) {
        $subscriberList = $this->subscriberListService->getSubscriberList($listId);
        if(!$subscriberList->status) {
            return $this->fail();
        }
        $customFiels = $this->customFieldRepo->getCusfomFieldOfSubscriber($listId)->toArray();

        $arrField = [
                (object) ["key" => "field_1", "name" => "Phone", "field" => "phone"],
                (object) ["key" => "field_2", "name" => "First Name", "field" => "first_name"],
                (object) ["key" => "field_3", "name" => "Last Name", "field" => "last_name"],
        ];

        foreach($customFiels as $key => $item){
            array_push($arrField, (object) [
                    "key" => "field_" . ($key+4),
                    "name" => $item['field_name'],
                    "field" => "custom_field_" . $item['id']
            ]);
        }

        Session::put('dataExport', $arrField);

        return $this->success((object) [
                "subscriberList" => (object) $subscriberList->data,
                "fields" => (object) $arrField
        ]);
    }

    public function exportSubscriberWithStatus($listID, $request) {
        $user = Auth::user();
        $supperssed = $this->subscriberListService->getGlobalSuppressionList($user->type == "GROUP4" ? $user->reader_id : $user->id);
        $dataExport = Session::get('dataExport');
        $status = $request->get("status");
        $headers = "";
        $fields = [];
        foreach($dataExport as $item) {
            if($request->get($item->key) == 'on' || $item->field == 'phone') {
                if($headers == "") {
                    $headers = "'" . ucfirst($item->name) ."' AS " . $item->field;
                }else {
                    $headers = $headers . ", '" . $item->name ."' AS " . $item->field;
                }

                if(!empty($user) && !!$user->encrypted && $item->field == 'phone') {
                    $field = 'ENCRYPT_PHONE(phone) AS phone';
                }else {
                    $field = $item->field;
                }

                array_push($fields, $field);
            }
        }
        $fields = implode(",", $fields);
        $pathFile = config("constants.path_file_export_subscriber"). md5($user->id);
        $this->uploadFile->makeForder($pathFile);
        $fileName = md5($user->id . uniqid() . time()) . ".csv";
        $file = public_path($pathFile . "/" . $fileName);
        //$file = "/var/www/html/abc/" . $fileName;
        $this->uploadFile->removeFileCSV($file);
        $result = $this->subscriberRepo->exportSubscriberWithStatus($listID, $status, $headers, $fields, $file, $supperssed->id);

        if($result) {
            return $this->success($file);
        }

        return $this->fail();
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Subscribers\ISubscriberService::createSubscriber()
     */
    public function createSubscriber(array $attributes, $idList) {
        return $this->subscriberRepo->createSubscriber($attributes, $idList);
    }

    /**
     * fn add unsubscribe
     * {@inheritDoc}
     * @see \App\Services\Subscribers\ISubscriberService::addUnsubscriber()
     */
    public function addUnsubscriber($data, $userID) {
        try {
            $suppressionList = $this->subscriberListService->getGlobalSuppressionList($userID);
            $suppressionList = (object) $suppressionList->toArray();

            $subscriber = $this->subscriberRepo->getSubscriberByPhone($data->phone, $data->campaign_id, $suppressionList->id);
            if(!empty($subscriber)) {
                return $subscriber;
            }

            return $this->subscriberRepo->createSubscriber((array)$data, $suppressionList->id);
        }catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function addPhoneInvalid($data, $userID) {
        try {
            $invalidEntriesList = $this->subscriberListService->getInvalidEntriesList($userID);
            $invalidEntriesList = (object) $invalidEntriesList->toArray();

            $subscriber = $this->subscriberRepo->getSubscriberByPhone($data->phone, $data->campaign_id, $invalidEntriesList->id);
            if(!empty($subscriber)) {
                return $subscriber;
            }
            return $this->subscriberRepo->createSubscriber((array)$data, $invalidEntriesList->id);
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
