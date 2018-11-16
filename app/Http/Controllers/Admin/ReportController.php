<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Reports\IReportService;
use Illuminate\Support\Facades\Response;
use App\Services\Campaign\ICampaignService;
use Auth;
use Illuminate\Support\Facades\Lang;
use App\Services\Settings\IConfigurationService;
use App\Services\Reports\ITMSMSReportService;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RouteMobileRequest;
use App\Services\Reports\IRouterMobileReportService;
use App\Services\Reports\IInfobipReportService;
use App\Models\InfobipReport;
use Illuminate\Support\Facades\DB;


class ReportController extends Controller
{
    protected $request;
    protected $reportService;
    protected $campaignService;
    protected $configurationService;
    protected $tmSMSReportService;
    protected $routerMobileReportService;
    protected $infobipReportService;

    public function __construct(
        Request $request, 
        IReportService $reportService, 
        ICampaignService $campaignService, 
        IConfigurationService $configurationService, 
        ITMSMSReportService $tmSMSReportService, 
        IRouterMobileReportService $routerMobileReportService,
        IInfobipReportService $infobipReportService
    )
    {
        $this->request = $request;
        $this->reportService = $reportService;
        $this->campaignService = $campaignService;
        $this->configurationService = $configurationService;
        $this->tmSMSReportService = $tmSMSReportService;
        $this->routerMobileReportService = $routerMobileReportService;
        $this->infobipReportService = $infobipReportService;;
    }

    public function listReportCampaign()
    {
        try {
            $result = $this->reportService->getListReportCampaign($this->request);
            return response()->json($result->data, 200);
        } catch (\Exception $e) {
            return response()->json([
                "data" => [],
                "recordsFiltered" => 0
            ], 500);
        }
    }

    public function viewDetailReportCampaign($userID, $campaignID)
    {
        $campaign = $this->campaignService->getCampaignInfo($campaignID, $userID);
        return view("admins.reports.campaigns.detail", [
            "user_id" => $userID,
            "campaign_id" => $campaignID,
            'campaign' => $campaign
        ]);
    }

    public function viewDetailShortLinks($userID, $campaignID, $campaginLinkID) {
        $campaign = $this->campaignService->getCampaignInfo($campaignID, $userID);
        return view("admins.reports.campaigns.short-links", [
                "user_id" => $userID,
                "campaign_id" => $campaignID,
                'campaign' => $campaign,
                'campaign_link_id' => $campaginLinkID,
        ]);
    }

    public function reportCampaign($userID, $campaignID) {
        try {
            $result = $this->reportService->getReportCampaign($userID, $campaignID);

            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(["data" => []], 500);
        }
    }
    public function moveDataQueueToReportListSummary()
    {
        // set time out limit
        set_time_limit(0);
        $result = $this->reportService->moveDataQueueToReportListSummary();
        if (!!$result) {
            return 1;
        }

        return 0;
    }
    public function templatePDF()
    {
        $result = $this->reportService->getDataExportPDFCampaign($this->request);
        return view("admins.reports.campaigns.template-pdf", [
            'data' => [
                "valueStatus" => $result->data_status,
                "valueTotal" => $result->data_total_expenses,
                "valueDelivered" => $result->data_delivered_rate,
                "dataTable" => $result->data_table,
                "currency" => $result->currency,
                "campaign" => $result->campaign
            ]
        ]);
    }
    public function exportCSVCampaign($userID, $campaignID)
    {
        $file = $this->reportService->exportCSVCampaign($userID, $campaignID, $this->request);
        $fileName = "Export_Campaign_" . time() . ".zip";
        $headers = array(
            "Content-Type: application/zip"
        );

        return Response::download($file, $fileName, $headers)->deleteFileAfterSend(true);
    }
    public function exportPDFCampaign($userID, $campaignID)
    {
        $file = $this->reportService->exportPDFCampaign($userID, $campaignID, $this->request);
        $fileName = "Export_Campaign_" . time() . ".pdf";
        $headers = array(
            "Content-Type: application/octet-stream"
        );

        return Response::download($file, $fileName, $headers)->deleteFileAfterSend(true);
    }
    public function viewReportCenter()
    {
        $timeZone = $this->campaignService->getTimeZone();
        $userTimeZone = Auth::user()->time_zone;
        return view("admins.reports.centers.index", [
            'timeZone' => $timeZone,
            'user_timeZone' => $userTimeZone
        ]);
    }
    public function reportCenter()
    {
        $result = $this->reportService->reportCenter($this->request);
        if (!$result->status) {
            return response()->json(["message" => Lang::get('notify.report_center_error')], 400);
        }

        return response()->json(["data" => $result->data, "message" => Lang::get('notify.report_center_success')], 200);
    }
    public function getDataReportCenter()
    {
        $result = $this->reportService->getDataReportCenter($this->request);
        return response()->json($result->data, 200);
    }
    public function cronJobReportCenter()
    {
        // set time out limit
        set_time_limit(0);
        $result = $this->reportService->cronJobReportCenter();
        if (!$result) {
            return 0;
        }
        return 1;
    }

    public function downloadFileCsvReportCenter($hash)
    {
        $path = config("constants.path_file_report_center");
        $file = public_path($path) . $hash . ".csv";
        //$file= '/var/www/html/abc/' . $hash . ".csv";

        $fileName = "Export_Center_" . uniqid() . ".csv";
        $headers = array(
            "Cache-Control: public",
            "Content-Type: application/octet-stream",
            "Content-Encoding: UTF-8",
            "Content-Type: text/csv; charset=UTF-8",
            "Content-Transfer-Encoding: binary"
        );

        return Response::download($file, $fileName, $headers);
    }

    public function cronAutoReport()
    {
        // set time out limit
        set_time_limit(0);
        $result = $this->configurationService->autoTriggerReport();
        if (!!$result) {
            return 1;
        }

        return 0;
    }

    public function cronSendEmailReport()
    {
        // set time out limit
        set_time_limit(0);
        $result = $this->configurationService->sendEmailReport();
        if (!!$result) {
            return 1;
        }

        return 0;
    }

    public function createTMSMSReport(Request $request)
    {
        try {
            $input = (array)$request->input();
            if (empty($input['gw-from']) || empty($input['gw-to']) || empty($input['gw-dlr-status']) || empty($input['gw-msgid']) || empty($input['gw-error-code'])) {
                return response()->json([
                    'error' => 'Miss data'
                ], 400);
            }

            if (!Hash::check(env('KEY_TM_SMS'), $input['hash'])) {
                return response()->json([
                    'error' => 'Authorization'
                ], 401);
            }

            $status = "PENDING";
            if ($input['gw-dlr-status'] == 1) {
                $status = 'DELIVERED';
            } else if ($input['gw-dlr-status'] == 2) {
                $status = "EXPIRED";
            } else if ($input['gw-dlr-status'] == 3) {
                $status = "FAILED";
            }
            
            // delete param hash
            unset($input['hash']);
            $params = [
                "return_message_id" => $input['gw-msgid'],
                "return_from" => $input['gw-from'],
                "return_to" => $input['gw-to'],
                "return_message" => json_encode((object)$input),
                "return_error_code" => $input['gw-error-code'],
                "return_status" => $status
            ];
            // \CustomLog::error ( "Report", 'Report-' . date ( 'Y-m-d' ), ['error' => $params] );
            $report = $this->tmSMSReportService->createTMSMSReport($params);
            return $report;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * fn receive report route mobile
     * @param $request
     */
    public function createRouteMobileReport(RouteMobileRequest $request)
    {
        try {
            $data = $request->all();
            // \CustomLog::info('Log Route Mobile', 'LogRouteMobile-' . date('Y-m-d'), json_decode(json_encode($data), true));
            
            $status = "PENDING";
            switch ($data['sStatus']) {
                case "ENROUTE":
                case "ACCEPTED":
                case "ACKED":
                    $status = "PENDING";
                    break;
                case "DELIVRD":
                    $status = "DELIVERED";
                    break;
                case "EXPIRED":
                    $status = "EXPIRED";
                    break;
                default :
                    $status = "FAILED";
                    break;
            }
            $sms_count = NULL;
            if( $data['iCharge'] > 0 && $data['iCostPerSms'] > 0 ) {
                $sms_count = ceil($data['iCharge'] / $data['iCostPerSms']);
            }
            $params = [
                "return_message_id" => $data['sMessageId'],
                "return_from" => $data['sSender'],
                "return_to" => $data['sMobileNo'],
                "return_message" => json_encode((object)$data),
                "return_status" => $status,
                "return_sms_count" => $sms_count,
                "return_price" => $data['iCharge'] > 0 ? $data['iCharge'] : NULL,
                "return_error_code" => $data['ErrCode'] == 'NULL' ? NULL : $data['ErrCode']
            ];
            $routeMobile = $this->routerMobileReportService->getRouterMobileReport($data['sMessageId']);
            if ( !empty($routeMobile) ) {
                $report = $this->routerMobileReportService->updateRouterMobileReport($params, $routeMobile['id']);
            } else {
                $report = $this->routerMobileReportService->createRouterMobileReport($params);
            }

            return response()->json([], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * fn receive report of infobip
     * @param $request
     */
    public function createInfoBipSMSReport(Request $request){
        try {
            $data = $request->all();
            if(!empty($data) && !empty($data['results'])) {
                $params = [];
                $result = $data['results'];
                foreach($result as $res) {
                    $params = [
                        "return_message_id" => $res['messageId'],
                        "return_json" => json_encode($res)
                    ];
                    // create or update infobip report
                    $report = $this->infobipReportService->createInfobipReport($params);
                }
                
                return response()->json([], 200);
            }
            // \CustomLog::info('Log Info Bip', 'LogInfoBip-' . date('Y-m-d'), json_decode(json_encode($data), true));
            return response()->json([], 400);
        }catch(\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * fn receive report of messagebird
     * @param $request
     */
    public function createMessageBirdSMSReport(Request $request){
        try {
            $data = $request->all();

            \CustomLog::info('Log Message Bird', 'LogMessageBird-' . date('Y-m-d'), json_decode(json_encode($data), true));

            return response()->json([], 200);;
        }catch(\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function handelDataReport() {
        // $reports = InfobipReport::where('status', 1)->take(200)->get();
        // $query = "INSERT INTO infobip_reports (return_message_id, return_json, status) VALUES ";
        // $queryValue = null;
        // foreach($reports as $index => $report) {
        //     $json = json_decode($report->return_json);
        //     $data = (object)[
        //         'results' => [
        //             $json
        //         ]
        //     ];
        //     $queryValue .= " ( " . $item->messageId . ", '" . json_encode($item) . "', 0) ";
        //             $queryValue .= $key == count($data->results) - 1 ? " " : ", ";
        // }
        // $query .= $queryValue . " ON DUPLICATE KEY UPDATE return_json = VALUES(return_json), status = VALUES(status);";
        // $pdo = DB::connection()->getPdo();
        // return $pdo->exec($query);
    }
}
