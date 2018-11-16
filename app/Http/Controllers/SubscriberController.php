<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Subscribers\ISubscriberService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Lang;
use App\Services\CustomFields\ICustomFieldService;
use Illuminate\Support\Facades\App;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Response;
use Exception;
use App\Services\SubscriberLists\ISubscriberListService;
use Illuminate\Support\Facades\Auth;
use App\Services\Campaign\ICampaignService;
use App\Services\Campaign\IQueueService;
use Carbon\Carbon;

class SubscriberController extends Controller
{
    protected $request;
    protected $subscriberService;
    protected $customFieldService;
    protected $subscriberListService;
    protected $campaignService;
    protected $queueService;

    public function __construct(Request $request, ISubscriberService $subscriberService, ICustomFieldService $customFieldService, ISubscriberListService $subscriberListService, ICampaignService $campaignService, IQueueService $queueService) {
        $this->request = $request;
        $this->subscriberService = $subscriberService;
        $this->customFieldService= $customFieldService;
        $this->subscriberListService = $subscriberListService;
        $this->campaignService = $campaignService;
        $this->queueService = $queueService;
    }

    public function index($list_id) {
        $lang = $this->request->session ()->get ( "locale" );
        $filterFields = config("constants.lang." . $lang . ".filter_fiels");
        $headerCustomField = $this->customFieldService->getCustomFieldOfSubscriber($list_id);
        $subscriberList = $this->subscriberListService->getSubscriberList($list_id);
        if(!$subscriberList->status) {
            return redirect("404");
        }
        $user = Auth::user();
        return view('admins.subscribers.index',[
            "filterFields" => $filterFields ? $filterFields : [],
            "headerCustomField" => $headerCustomField,
            "list_id" => $list_id,
            "user" => $user,
            "subscriber_list" => (object) $subscriberList->data
        ]);
    }

    public function getListSubscribers($listId) {
        $result =  $this->subscriberService->getListSubscribers($listId, $this->request);

        // check data
        if(!$result->status) {
            return redirect('404');
        }

        $collect = collect($result->data);
        $data = [
            "recordsTotal" => $collect['total'],
            "recordsFiltered" => $collect['total'],
            "data" => $collect['data']
        ];
        return $data;
    }

    public function viewAdd($id) {
        $result = $this->subscriberService->viewExport($id);
        if(!$result->status) {
            Session::flash ( 'error', Lang::get ( 'notify.subscriber_list_not_found' ));
            return redirect ()->back ();
        }
        return view ( 'admins.subscribers.add', ["list_id" => $id]);
    }

    public function viewUploadCSV($id) {
        $result = $this->subscriberService->viewExport($id);
        if(!$result->status) {
            Session::flash ( 'error', Lang::get ( 'notify.subscriber_list_not_found' ));
            return redirect ()->back ();
        }
        return view ( 'admins.subscribers.upload-csv', ["list_id" => $id]);
    }

    public function viewCopyPaste($id) {
        $result = $this->subscriberService->viewExport($id);
        if(!$result->status) {
            Session::flash ( 'error', Lang::get ( 'notify.subscriber_list_not_found' ));
            return redirect ()->back ();
        }
        return view ( 'admins.subscribers.copy-paste', ["list_id" => $id]);
    }

    public function uploadCSV($listId) {
        $result = $this->subscriberService->uploadCSV($this->request);

        if(!!$result->status) {
            return redirect('admin/subscribers/' . $listId . '/mapping');
        }

        if (count ( $result->error ) > 0) {
            $message = ( object ) $result->error->toArray ();
            $error = count ( $message->file ) > 0 ? $message->file[0] : '';
            if (isset ( $error )) {
                Session::flash ( 'error', $error );
            }
        }

        return redirect ()->back ();
    }

    public function copyPaste($listId) {
        $result = $this->subscriberService->copyPaste($this->request);
        if(!!$result->status) {
            return redirect('admin/subscribers/' . $listId . '/mapping');
        }

        if (count ( $result->error ) > 0) {
            $message = ( object ) $result->error->toArray ();
            $error = count ( $message->content ) > 0 ? $message->content[0] : '';
            if (isset ( $error )) {
                Session::flash ( 'error', $error );
            }
        }

        return redirect ()->back ();
    }

    public function mapping($listId) {
        $fileName = Session::get('fileName');
        if(isset($fileName)) {
            $customFields = $this->subscriberService->getCustomFieldOfUser($listId);
            $dataFile = $this->subscriberService->readFileCSV();

            return view('admins.subscribers.mapping', ['customFields' => $customFields, 'dataFile' => $dataFile, 'list_id' => $listId]);
        }

        return redirect('admin/subscribers/' . $listId . '/add');
    }

    public function importCSV($listId) {
        $result = $this->subscriberService->importFileCSV($this->request);
        if(!!$result->status) {
            $data = (array)$result->data;
            Session::flash ( 'success', Lang::get ( 'notify.import_success' ));
            return redirect('admin/subscribers/' . $listId . '/imported')->with('dataImport', $data);
        }

        Session::flash ( 'error', Lang::get ( 'notify.import_error' ) );
        return redirect ()->back ();
    }
    public function imported($listId) {
        $data = Session::get('dataImport');
        if(isset($data)) {
            Session::forget('dataImport');
            return view ( 'admins.subscribers.imported', ['data' => $data, 'list_id' => $listId]);
        }

        return redirect('admin/subscribers/' . $listId . '/add');
    }
    public function exportSubscribers($listId){
        $result = $this->subscriberService->exportSubscribers($listId, $this->request);
        if(!$result) {
            return [
                "message" => Lang::get ( 'notify.export_error' )
            ];
        }

        return response()->download($result)->deleteFileAfterSend(true);
    }
    public function destroy($list_id) {

        try {
            $result = $this->subscriberService->viewExport($list_id);
            if(!$result->status) {
                Session::flash ( 'error', Lang::get ( 'notify.subscriber_list_not_found' ));
                return redirect ()->back ();
            }

            $result = $this->subscriberService->deleteSubscribers($list_id, $this->request);

            if(!$result) {
                throw new Exception();
            }

            return Response::json([
                    "message" => Lang::get ( 'notify.delete_success' )
            ], 200);
        }catch (\Exception $e) {
            return Response::json([
                    "message" => Lang::get ( 'notify.delete_error' )
            ], 404);
        }
    }
    public function downloadCSV($key, $hash) {
        try {
            $file = $this->subscriberService->downloadCSV($hash);
            if(!!$file) {
                $headers = array(
                        'Content-Type: text/csv',
                );
                $fileName = "";
                if($key == "duplicate") {
                    $fileName = "import_duplicate_subscriber.csv";
                }else if($key == 'invalid'){
                    $fileName = "import_invalid_subscriber.csv";
                }else if($key == 'skip') {
                    $fileName = "skip_subscriber.csv";
                }else if($key == 'remove') {
                    $fileName = "remove_subscriber.csv";
                }
                return Response::download($file, $fileName, $headers)->deleteFileAfterSend(true);
            }

            return "Download Error!";
        }catch(\Exception $e){
            return $e;
        }
    }
    /**
     * FN view UI update status
     * @return view
     */
    public function viewUpdate($listId) {
        $result = $this->subscriberService->viewExport($listId);
        if(!$result->status) {
            Session::flash ( 'error', Lang::get ( 'notify.subscriber_list_not_found' ));
            return redirect ()->back ();
        }
        return  view("admins.subscribers.update-status.index", ['list_id' => $listId]);
    }
    /**
     * Fn view UI upload csv
     * @param $listId
     * @return View
     */
    public function viewUpdateUploadCSV($listId) {
        $result = $this->subscriberService->viewExport($listId);
        if(!$result->status) {
            Session::flash ( 'error', Lang::get ( 'notify.subscriber_list_not_found' ));
            return redirect ()->back ();
        }
        return  view("admins.subscribers.update-status.upload-csv", ['list_id' => $listId]);
    }
    /**
     * FN view UI copy paste
     * @param  $listId
     * @return View
     */
    public function viewUpdateCopyPaste($listId) {
        $result = $this->subscriberService->viewExport($listId);
        if(!$result->status) {
            Session::flash ( 'error', Lang::get ( 'notify.subscriber_list_not_found' ));
            return redirect ()->back ();
        }
        return  view("admins.subscribers.update-status.copy-paste", ['list_id' => $listId]);
    }
    public function updateUploadCSV($listId) {
        $result = $this->subscriberService->uploadCSV($this->request, 'update');

        if(!!$result->status) {
            Session::flash ( 'success', Lang::get ( 'notify.upload_csv' ) );
            return redirect('admin/subscribers/' . $listId . '/update/mapping');
        }

        if (count ( $result->error ) > 0) {
            $message = ( object ) $result->error->toArray ();
            $error = count ( $message->file ) > 0 ? $message->file[0] : '';
            if (isset ( $error )) {
                Session::flash ( 'error', $error );
            }
        }

        return redirect ()->back ();
    }
    public function updateCopyPaste($listId) {
        $result = $this->subscriberService->copyPaste($this->request, 'update');
        if(!!$result->status) {
            $data = $this->subscriberService->updateStatus($this->request, 'copy-paste');
            if(!!$data->status) {
                $value = (array)$data->data;
                Session::flash ( 'success', Lang::get ( 'notify.import_success' ));
                return redirect('admin/subscribers/' . $listId . '/update/imported')->with('updateDataImport', $value);
            }

            return redirect ()->back ();
        }

        if (count ( $result->error ) > 0) {
            $message = ( object ) $result->error->toArray ();
            $error = count ( $message->content ) > 0 ? $message->content[0] : '';
            if (isset ( $error )) {
                Session::flash ( 'error', $error );
            }
        }

        return redirect ()->back ();
    }
    public function updateMapping($listId) {
        $fileName = Session::get('fileNameUpdate');
        if(isset($fileName)) {
            $customFields = $this->subscriberService->getCustomFieldOfUser($listId);
            $dataFile = $this->subscriberService->readFileCSV('update');

            return view('admins.subscribers.update-status.mapping', ['customFields' => $customFields, 'dataFile' => $dataFile, 'list_id' => $listId]);
        }

        return redirect('admin/subscribers/' . $listId . '/update');
    }
    public function updateImportCSV($listId) {
        $result = $this->subscriberService->updateStatus($this->request);
        if(!!$result->status) {
            $data = (array)$result->data;
            Session::flash ( 'success', Lang::get ( 'notify.import_success' ));
            return redirect('admin/subscribers/' . $listId . '/update/imported')->with('updateDataImport', $data);
        }

        Session::flash ( 'error', Lang::get ( 'notify.import_error' ) );
        return redirect ()->back ();
    }
    public function updateImported($listId) {
        $data = Session::get('updateDataImport');
        if(isset($data)) {
            Session::forget('updateDataImport');
            return view ( 'admins.subscribers.update-status.imported', ['data' => $data, 'list_id' => $listId]);
        }

        return redirect('admin/subscribers/' . $listId . '/update');
    }

    public function viewRemove($listId) {
        try {
            $result = $this->subscriberListService->getSubscriberList($listId);
            if(!$result->status) {
                Session::flash ( 'error', Lang::get ( 'notify.subscriber_list_not_found' ));
                return redirect ()->back ();
            }

            return view("admins.subscribers.removes.index", ['subscriber_list' => $result->data, "list_id" => $listId]);
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function cronDetectSubscribers() {
        // set time out limit
        set_time_limit(0);
        $subscriberList = $this->subscriberListService->getDetectSubscriberList();
        if ( $subscriberList ) {
            try {
                // Update status to PROCESSING
                $this->subscriberListService->updateSubscriberListViaModel($subscriberList->id, [
                        'detect_status' => 'PROCESSING',
                        'detect_updated_at' => date('Y-m-d H:i:s')
                ]);

                // Detect
                $detectedTotals = $this->subscriberService->detectSubscribers( $subscriberList->id );
                if ( $detectedTotals['PENDING'] > 0 ) {
                    // Update status to PENDING
                    $this->subscriberListService->updateSubscriberListViaModel($subscriberList->id, [
                            'detect_status' => 'PENDING',
                            'detect_updated_at' => date('Y-m-d H:i:s')
                    ]);
                } else {
                    // Update status to PROCESSED
                    $this->subscriberListService->updateSubscriberListViaModel($subscriberList->id, [
                            'detect_status' => 'PROCESSED',
                            'detect_updated_at' => date('Y-m-d H:i:s')
                    ]);
                }

                return "List {$subscriberList->id} is PROCESSED";
            } catch (Exception $e) {
                // Update status to PENDING
                $this->subscriberListService->updateSubscriberListViaModel($subscriberList->id, [
                        'detect_status' => 'PENDING',
                        'detect_updated_at' => date('Y-m-d H:i:s')
                ]);
                return $e->getMessage();
            }
        } else {
            return "Do have not subscribed list which needs to detect";
        }
    }

    public function destroyWithStatus($listID) {
        try {
            $result = $this->subscriberService->deleteSubscribersWithStatus($listID, $this->request);

            if(!$result->status) {
                throw new Exception();
            }

            Session::flash ( 'success', Lang::get ( 'notify.import_success' ));
            return redirect('admin/subscribers/' . $listID. '/remove/return')->with('removeData', $result->data);
        }catch (\Exception $e) {
            Session::flash ( 'error', Lang::get ( 'notify.delete_error' ));
            return redirect ()->back ();
        }
    }

    public function returnRemove($listID) {
        $data = Session::get('removeData');
        if(isset($data)) {
            //Session::forget('removeData');
            return view ( 'admins.subscribers.removes.result', ['data' => $data, 'list_id' => $listID]);
        }

        return redirect('admin/subscribers/' . $listID. '/remove');
    }

    public function viewExport($listId) {
        $result = $this->subscriberService->viewExport($listId);
        if(!$result->status) {
            Session::flash ( 'error', Lang::get ( 'notify.subscriber_list_not_found' ));
            return redirect ()->back ();
        }
        $data = $result->data;
        return view("admins.subscribers.exports.index", ['subscriber_list' => $data->subscriberList, "list_id" => $listId, 'fields' => $data->fields]);
    }

    public function exportSubsriberWithStatus($listID) {
        $result = $this->subscriberService->exportSubscriberWithStatus($listID, $this->request);

        if(!$result->status) {
            Session::flash ( 'error', Lang::get ( 'notify.export_subscribers_error' ));
            return redirect ()->back ();
        }

        $file = $result->data;
        $content = file_get_contents($file);
        $content = str_replace("\xEF\xBB\xBF", '', $content);
        file_put_contents($file, $content);
        $fileName = "Export_Subscribers_" . time() . ".csv";
        $headers = array(
                'Content-Type: text/csv; charset=utf-8'
        );

        return Response::download($file, $fileName, $headers)->deleteFileAfterSend(true);
    }

    public function viewUnsubscribe() {
        $campaignID = str_replace($this->subscriberService->keyBase64(), "", base64_decode($this->request->get('c')));
        $userID = str_replace($this->subscriberService->keyBase64(), "", base64_decode($this->request->get('u')));
        $campaign = $this->campaignService->getCampaignInfo($campaignID, $userID);
        if(empty($campaign)) {
            return redirect('/login');
        }

        return view('/unsubscribe', ['campaign_id' => $this->request->get('c'), 'user_id' => $this->request->get('u')]);
    }

    public function unsubscribe() {
        $campaignID = str_replace($this->subscriberService->keyBase64(), "", base64_decode($this->request->get('campaign_id')));
        $userID = str_replace($this->subscriberService->keyBase64(), "", base64_decode($this->request->get('user_id')));
        $phone = $this->request->get('phone');

        if($phone & $campaignID) {
            $queue = $this->queueService->getQueueByPhone($phone, $userID, $campaignID);

            if(!$queue) {
                $data = (object) [
                        "phone" => $phone,
                        "country" => "",
                        "network" => "",
                        "campaign_id" => $campaignID,
                        "unsubscription_date" => Carbon::now()->toDateTimeString(),
                ];
                $result = $this->subscriberService->addPhoneInvalid($data, $userID);
                Session::flash ( 'error', Lang::get ( 'notify.number_invalid' ));
            }else {
                $data = (object) [
                        "phone" => $queue->phone,
                        "country" => $queue->country,
                        "network" => $queue->network,
                        "unsubscription_date" => Carbon::now()->toDateTimeString(),
                        "campaign_id" => $campaignID
                ];
                $result = $this->subscriberService->addUnsubscriber($data, $userID);
                Session::flash ( 'success', Lang::get ( 'notify.unsubscribe_success' ));
            }
        }else {
            Session::flash ( 'error', Lang::get ( 'notify.unsubscribe_error' ));
        }

        return redirect()->back();
    }
}
