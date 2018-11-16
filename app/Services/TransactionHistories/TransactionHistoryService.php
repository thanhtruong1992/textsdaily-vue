<?php

namespace App\Services\TransactionHistories;

use App\Services\BaseService;
use App\Repositories\TransactionHistories\ITransactionHistoryRepository;
use Auth;
use App\Services\Auth\IAuthenticationService;
use App\Services\UploadService;
use App\Services\Campaign\ICampaignService;
use Carbon\Carbon;

class TransactionHistoryService extends BaseService implements ITransactionHistoryService {
    protected $transactionRepo;
    protected $authService;
    protected $uploadService;
    protected $campaignService;
    public function __construct(ITransactionHistoryRepository $transactionRepo, IAuthenticationService $authService, UploadService $uploadService, ICampaignService $campaignService) {
        $this->transactionRepo = $transactionRepo;
        $this->authService = $authService;
        $this->uploadService = $uploadService;
        $this->campaignService = $campaignService;
    }

    /**
     * fn get all campaign
     * @param object $request
     * @return array data
     */
    public function getTransactionHistoryCampains($request) {
        $page = $request->get("page") - 1;
        $user = Auth::user();
        $users = "";
        if($user->type == "GROUP2") {
            $users = $this->authService->getAllUserGroup3($user->id, false);
        }else {
            $users = $this->authService->getAllUserGroup3(null, false);
        }
        $listUser = implode(",", $users->pluck("id")->all());
        $dateFrom= $request->get("from", "");
        $dateTo = $request->get("to", "");
        $timezone = $request->get("timezone");
        $from = $dateFrom != "" ? Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($dateFrom), $timezone)->setTimezone("UTC")->toDateTimeString(): '';
        $to = $dateTo != "" ? Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($dateTo), $timezone)->setTimezone("UTC")->toDateTimeString() : '';
        $result = $this->transactionRepo->getTransactionHistoryCampains($listUser, $from, $to, $timezone, $user->type, $user->currency, $page);

        return (object)[
                "data" => $result,
                "recordsFiltered" => count($result) > 0 ? $result[0]->totalData : 0
        ];
    }

    public function exportCSVCampaigns($request) {
        $user = Auth::user();
        $users = "";
        if($user->type == "GROUP2") {
            $users = $this->authService->getAllUserGroup3($user->id, false);
        }else {
            $users = $this->authService->getAllUserGroup3(null, false);
        }
        $listUser = implode(",", $users->pluck("id")->all());
        $dateFrom = $request->get("from", "");
        $dateTo = $request->get("to", "");
        $timezone = $request->get("timezone");
        $from = $dateFrom != "" ? Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($dateFrom), $timezone)->setTimezone("UTC")->toDateTimeString(): '';
        $to = $dateTo != "" ? Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($dateTo), $timezone)->setTimezone("UTC")->toDateTimeString() : '';
        $headerCSV = "'Date' AS date_send, 'Campaign' AS campaign_name, 'Client' AS client_name, 'Client Type' AS client_type, 'Total Charge' AS total, 'Currency' AS currency";
        $path = config("constants.path_file_transaction_history") . $user->id . "/";
        $this->uploadService->makeForder($path);
        $hashFile = md5($user->id . uniqid() . time());
        $pathFile = public_path($path) . $hashFile. ".csv";
        //$pathFile = '/var/www/html/abc/' . $hashFile. ".csv";
        $result = $this->transactionRepo->exportCSVCampaigns($listUser, $from, $to, $timezone, $user->type, $user->currency, $headerCSV, $pathFile);
        if(!$result) {
            return $this->fail();
        }

        return $this->success($pathFile);
    }

    public function getCampaign($userID, $campaignID) {
        $user = Auth::user();
        $campaign = $this->campaignService->getCampaign($userID, $campaignID, $user->type);
        $campaign = $campaign[0];
        $campaign->send_time = Carbon::createFromFormat('Y-m-d H:i:s', $campaign->send_time, $user->time_zone);
        $campaign->send_time = $campaign->send_time->setTimezone("UTC")->format("d-M-Y H:i");
        $reports = $this->transactionRepo->getReportCampaign($campaignID, $userID, $user->type, $user->currency);
        return (object) [
                "campaign" => $campaign,
                "reports" => $reports
        ];
    }

    public function exportCSVReportCamapign($userID, $campaignID) {
        $user = Auth::user();
        $headerCSV = "'Number of sent SMS' AS totals, 'Service Provider' AS service_provider, 'Country' AS country, 'Network' AS network, 'Unit Price' AS unit_price, 'Total Charge' AS total_charge, 'Currency' AS currency";
        $path = config("constants.path_file_transaction_history") . $user->id . "/";
        $this->uploadService->makeForder($path);
        $hashFile = md5($user->id . uniqid() . time());
        $pathFile = public_path($path) . $hashFile. ".csv";
        //$pathFile = '/var/www/html/abc/' . $hashFile. ".csv";
        $result = $this->transactionRepo->exportCSVReportCampaign($campaignID, $userID, $user->type, $user->currency, $headerCSV, $pathFile);
        if(!$result) {
            return $this->fail();
        }

        return $this->success($pathFile);
    }

    public function getTransactionByQuery($from, $to, $timezone, $sort_column, $order_by, $page) {
        $results = $this->transactionRepo->getTransactionByQuery($from, $to, $timezone, $sort_column, $order_by, $page);
        $dataList = $results->items();
        foreach ($dataList as $item) {
            $item->created_at = date(config('app.datetime_format'), strtotime($item->created_at));
        }
        return (object)[
                'data' => $dataList,
                'recordsTotal' => $results->total(),
                'recordsFiltered' => $results->total(),
                'total' => $results->total()
        ];
    }

    public function exportCSVClient($from, $to, $timezone) {
        $user = Auth::user();
        $headerCSV = "'Date' AS created_at, 'Description' AS description, 'Action' AS type, 'Client' AS name, 'Client Type' AS billing_type, 'Charge' AS credits, 'Currency' AS currency";
        $path = config("constants.path_file_transaction_history") . $user->id . "/";
        $this->uploadService->makeForder($path);
        $hashFile = md5($user->id . uniqid() . time());
        $pathFile = public_path($path) . $hashFile. ".csv";
        //$pathFile = '/var/www/html/abc/' . $hashFile. ".csv";
        $result = $this->transactionRepo->exportCSVClient($from, $to, $timezone, $headerCSV, $pathFile);

        return $this->success($pathFile);
    }
}
