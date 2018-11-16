<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Campaign\ICampaignService;
use App\Services\TransactionHistories\ITransactionHistoryService;
use Illuminate\Support\Facades\Response;

class TransactionHistoryController extends Controller
{
    protected $request;
    protected $campaignService;
    protected $transactionService;
    public function __construct(Request $request, ICampaignService $campaignService, ITransactionHistoryService $transactionService) {
        $this->request = $request;
        $this->campaignService = $campaignService;
        $this->transactionService = $transactionService;
    }

    public function viewClient() {
        $user = Auth::user();
        $timeZone = $this->campaignService->getTimeZone ();
        $userTimeZone = Auth::user()->time_zone;
        return view("admins.transaction-histories.clients.index", [
                'user' => $user,
                'timeZone' => $timeZone,
                'user_timeZone' => $userTimeZone
        ]);
    }


    public function viewServiceProvider() {
        $timeZone = $this->campaignService->getTimeZone ();
        $userTimeZone = Auth::user()->time_zone;
        return view("admins.transaction-histories.campaigns.index", [
                'timeZone' => $timeZone,
                'user_timeZone' => $userTimeZone
        ]);
    }

    public function getDataClient() {
         try {
            $timezone = $this->request->timezone;
            $from = $this->request->from;
            $to = $this->request->to;
            $sort_column = $this->request->field;
            $order_by = $this->request->orderBy;
            $page = $this->request->page;
            $result = $this->transactionService->getTransactionByQuery($from, $to, $timezone, $sort_column, $order_by, $page);
            return response()->json($result, 200);
         }catch(\Exception $e) {
             return response()->json([], 500);
         }
    }

    public function getDataCampaigns() {
        try {
            $result = $this->transactionService->getTransactionHistoryCampains($this->request);
            return response()->json($result, 200);
        }catch(\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function exportCampaignsCSV() {
        $result = $this->transactionService->exportCSVCampaigns($this->request);
        //$path = config("constants.path_file_transaction_history");
        //$file = public_path($path) . $hash . ".csv";
        //$file= '/var/www/html/abc/' . $hash . ".csv";

        if(!!$result->status) {
            $fileName = "Transaction_History_Campaigns_" . uniqid() . ".csv";
            $headers = array(
                    "Content-Type: application/csv"
            );

            return Response::download($result->data, $fileName, $headers)->deleteFileAfterSend(true);
        }

        return "Export CSV was error!";
    }

    public function getCampaign($userID, $campaignID) {
        $result = $this->transactionService->getCampaign($userID, $campaignID);

        return view("admins.transaction-histories.campaigns.detail", ["campaign" => $result->campaign, "reports" => $result->reports, "user_id" => $userID, "campaign_id" => $campaignID]);
    }

    public function exportCSVReportCampaign($userID, $campaignID) {
        $result = $this->transactionService->exportCSVReportCamapign($userID, $campaignID);
        if(!!$result->status) {
            $fileName = "Transaction_History_Campaign_Reports_" . uniqid() . ".csv";
            $headers = array(
                    "Content-Type: application/csv"
            );

            return Response::download($result->data, $fileName, $headers)->deleteFileAfterSend(true);
        }

        return "Export CSV was error!";
    }

    public function exportClient(){
        $timezone = $this->request->timezone;
        $from = $this->request->from;
        $to = $this->request->to;

        $result = $this->transactionService->exportCSVClient($from, $to, $timezone);
        if(!!$result->status) {
            $fileName = "Transaction_History_Clients_" . uniqid() . ".csv";
            $headers = array(
                    "Content-Type: application/csv"
            );

            return Response::download($result->data, $fileName, $headers)->deleteFileAfterSend(true);
        }

        return "Export CSV was error!";
    }
}
