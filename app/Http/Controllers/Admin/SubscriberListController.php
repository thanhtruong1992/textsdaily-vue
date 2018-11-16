<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use App\Repositories\Subscribers\SubscriberListRepository;
use App\Http\Controllers\Controller;
use App\Repositories\Subscribers\ISubscriberListRepository;
use App\Services\SubscriberLists\ISubscriberListService;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CreateSubscriberListRequest;
use Exception;
use Auth;

class SubscriberListController extends Controller {
    protected $subscriberListService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ISubscriberListService $ISubscriberListService) {
        // $this->middleware ( 'guest' ) -> except ( 'logout' );
        $this->subscriberListService = $ISubscriberListService;
    }
    public function index(Request $request) {
        $data = $this->subscriberListService->getAllSubscriberListByUser ( $request );
        return $data;
    }
    public function create(CreateSubscriberListRequest $request) {
        $result = $this->subscriberListService->createNewSubscriberList ( $request );
        if ($result->status) {
            Session::flash ( 'success', Lang::get ( 'notify.create_new_subscriber_list' ) );
            return redirect ( 'admin/subscriber-lists' );
        }

        return redirect ()->back ();
    }
    public function delete(Request $request) {
        try {
            $result = $this->subscriberListService->deleteSubscriberList ( $request->list_id );

            if (! $result->status) {
                throw new Exception ();
            }

            return Lang::get ( 'notify.delete_subscriber_list_success' );
        } catch ( \Exception $e ) {
            return response ()->json ( [
                    "message" => Lang::get ( 'notify.delete_subscriber_list_error' )
            ], 404 );
        }
    }
    public function get($id) {
        $user = Auth::user();
        $result = $this->subscriberListService->getSubscriberList($id);
        if(!$result->status) {
            return redirect('404');
        }
        return view ( "admins.subscriber-lists.detail", [
                "list_id" => $id,
                "user" => $user,
                "subscrilerList" => (object)$result->data
        ] );
    }
    public function summarySubscribers($id) {
        $result = $this->subscriberListService->getReportListSummary ( $id );
        return array (
                "chartColunm" => $result->dataChart,
                "chartMap" => $result->dataMap,
                "data" => $result->data,
                "recordsFiltered" => count($result->data),
                "recordsTotal" => count($result->data),
        );
    }
}
