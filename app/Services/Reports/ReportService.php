<?php

namespace App\Services\Reports;

use App\Repositories\Reports\IReportCampaignReponsitory;
use App\Services\BaseService;
use Auth;
use App\Services\Auth\IAuthenticationService;
use App\Services\Campaign\ICampaignService;
use Exception;
use Carbon\Carbon;
use App\Services\Campaign\IQueueService;
use App\Services\CustomFields\ICustomFieldService;
use App\Services\Campaign\ICampaignRecipientsService;
use App\Services\UploadService;
use App\Repositories\Reports\IReportCenterReponsitory;
use PhpParser\Node\Stmt\Throw_;
use App\Mail\ExportCenter;
use App\Services\MailServices\IMailService;
use DB;

use GuzzleHttp\Client;

class ReportService extends BaseService implements IReportService {
    protected $reportCampaignRepo;
    protected $authService;
    protected $campaignService;
    protected $queueService;
    protected $customFieldService;
    protected $campaignRecipientService;
    protected $uploadServer;
    protected $reportCenterRepo;
    protected $mailService;
    public function __construct(IReportCampaignReponsitory $reportCampaignRepo, IAuthenticationService $authService, ICampaignService $campaignService, IQueueService $queuService, ICustomFieldService $customFieldService, ICampaignRecipientsService $campaignRecipientService, UploadService $uploadService, IReportCenterReponsitory $reportCenterRepo, IMailService $mailService) {
        $this->reportCampaignRepo = $reportCampaignRepo;
        $this->authService = $authService;
        $this->campaignService = $campaignService;
        $this->queueService = $queuService;
        $this->customFieldService = $customFieldService;
        $this->campaignRecipientService = $campaignRecipientService;
        $this->uploadServer = $uploadService;
        $this->reportCenterRepo = $reportCenterRepo;
        $this->mailService = $mailService;
    }

    /**
     * get list campgin report
     *
     * {@inheritdoc}
     *
     * @see \App\Services\Reports\IReportService::getListReportCampaign()
     */
    public function getListReportCampaign($request) {
        $user = clone (Auth::user ());
        $page = $request->get ( 'page' ) - 1;
        $search = $request->get("search", "");
        $search = $search == null ? '' : $search;
        $listUser = "";
        $users = [ ];
        if (! ! $user->isGroup4 ()) {
            $user->id = $user->reader_id;
            array_push ( $users, $user );
            $users = collect ( $users );
        } else if (! ! $user->isGroup3 ()) {
            array_push ( $users, $user );
            $users = collect ( $users );
        } else if (! ! $user->isGroup2 ()) {
            $users = $this->authService->getAllUserGroup3 ( $user->id, false );
        } else {
            $users = $this->authService->getAllUserGroup3 ( null, false );
        }
        $listUser = implode ( ",", $users->pluck ( 'id' )->all () );
        $result = $this->reportCampaignRepo->getListReportCampaign ( $listUser, Auth::user ()->type, $search, $page );
        $data = [
                "data" => $result,
                "recordsFiltered" => count ( $result ) > 0 ? $result [0]->total_data : 0
        ];
        return $this->success ( $data );
    }

    /**
     * fn get detail campaign report
     *
     * {@inheritdoc}
     *
     * @see \App\Services\Reports\IReportService::getReportCampaign()
     */
    public function getReportCampaign($userID, $campaignID) {
        $currencyUser = $this->authService->getUserInfo ( $userID )->currency;
        $result = $this->reportCampaignRepo->getReportCampaign ( $userID, $campaignID );
        $dataShortLink = $result->dataShortLink;
        $resultChart = $result->dataChart;
        $resultData = $result->data;
        $dataChart = [ ];
        $dataMap = [ ];
        $data = [ ];
        $arrColor = config ( "constants.array_color_chart" );
        $colorDefault = config ( "constants.color_chart_default" );
        foreach ( $resultChart as $key => $item ) {
            $country = $this->getCountry ( strtoupper ( $item->country ) );
            $country = count ( $country ) > 1 ? "Unknown" : $country;

            array_push ( $dataChart, [
                    'country' => $country,
                    'total' => floatval(str_replace(",", "", number_format ( $item->total, 2 ))),
                    'color' => $key < 5 ? $arrColor [$key] : $colorDefault
            ] );

            array_push ( $dataMap, [
                    'country' => $country,
                    'total' => number_format ( $item->total, 2 ),
            ] );
        }

        foreach ( $result->data as $key => $item ) {
            $country = $this->getCountry ( strtoupper ( $item->country ) );
            $country = count ( $country ) > 1 ? "Unknown" : $country;
            $currency = $this->getCurrency ( strtoupper ( $currencyUser ) );
            $item->country = $country;
            $item->network = $item->network != "" ? strtoupper ( $item->network ) : "Unknown";
            $item->delivery_rate = $item->delivered == 0 || $item->totals == 0 ? 0 : round ( $item->delivered / $item->totals * 100 ) . "%";
            $item->total_price = number_format ( $item->total_price, 2 ) . " " . $currency->code;
            array_push ( $data, $item );
        }
        
        $totalClicks = $this->getTotalClickOfShortLink($userID, $campaignID);
        if(!!$totalClicks->status) {
            foreach($result->dataShortLink as $key => $link) {
                foreach($totalClicks->data as $total) {
                    if($link->id == $total->campaign_link_id && $campaignID == $total->campaign_id) {
                        $link->total_clicks = $total->total_clicks;
                    }
                }
            }
        }

        return ( object ) [
                "chartColunm" => $dataChart,
                "chartMap" => $dataMap,
                "data" => $data,
                "dataShortLink" => $result->dataShortLink
        ];
    }
    /**
     * FN move data table queue into table report
     *
     * {@inheritdoc}
     *
     * @see \App\Services\Reports\IReportService::moveDataQueueToReportListSummary()
     */
    public function moveDataQueueToReportListSummary() {
        // get all table campaign
        $camapigns = $this->reportCampaignRepo->getAllDataCampaignAllUser ();
        foreach ( $camapigns as $camapign ) {
            $userID = $camapign->user_id;
            $campaignID = $camapign->id;
            try {
                $attributes = [
                        "backend_statistic_report" => "PROCESSING",
                        "backend_statistic_report_updated_at" => Carbon::now ()
                ];

                // update backend_statistic_report = PROCESSING
                $result = $this->campaignService->updateStatisticReportCampaign ( $userID, $campaignID, $attributes );

                // count campaign pedding
                $pending = $this->reportCampaignRepo->totalPendingQueueCampaign ( $userID, $campaignID );
                $pending = $pending [0] ? $pending [0] : [
                        "TotalPending" => 0
                ];
                // delete report
                $delete = $this->reportCampaignRepo->removeReportCampaign ( $userID, $campaignID );
                // move data table queue into report
                $result = $this->reportCampaignRepo->moveDataQueueIntoReport ( $userID, $campaignID );
                if (! ! $result && $pending->TotalPending == 0) {
                    $attributes = [
                            "backend_statistic_report" => "PROCESSED",
                            "backend_statistic_report_updated_at" => Carbon::now ()
                    ];
                    // update backend_statistic_report = PROCESSED
                    $camapign = $this->campaignService->updateStatisticReportCampaign ( $userID, $campaignID, $attributes );
                    return true;
                } else {
                    throw new Exception ();
                }
            } catch ( \Exception $e ) {
                $attributes = [
                        "backend_statistic_report" => "PENDING",
                        "backend_statistic_report_updated_at" => Carbon::now ()
                ];
                // update backend_statistic_report = PEDDING
                $result = $this->campaignService->updateStatisticReportCampaign ( $userID, $campaignID, $attributes );
                return false;
            }
        }

        return true;
    }

    /**
     * export campaign csv of report campaign
     *
     * {@inheritdoc}
     *
     * @see \App\Services\Reports\IReportService::exportCSVCampaign()
     */
    public function exportCSVCampaign($userID, $campaignID, $request) {
        $userLogin = Auth::user ();
        $user = $this->authService->getUserInfo ( $userID );
        // $pathFile = '/var/www/html/abc/';
        $path = 'uploads/export-campaigns/' . md5 ( Auth::user ()->id ) . '/' . md5 ( $campaignID ) . '/csv/';
        $pathFile = public_path ( $path );
        // make forder
        $this->uploadServer->makeForder ( $path );
        //clear forder
        $this->uploadServer->clearFolder($path);
        $flagFile = ( object ) array (
                "detailed" => $request->get ( "detailed", false ),
                "pending" => $request->get ( "pending", false ),
                "delivered" => $request->get ( "delivered", false ),
                "expired" => $request->get ( "expired", false ),
                "failed" => $request->get ( "failed", false )
        );
        $result = $this->reportCampaignRepo->exportCSVCampaign ( $campaignID, $user->id, $pathFile, $user->currency, $flagFile, $userLogin->encrypted, $user->time_zone, $userLogin->type);
        $fileName = md5 ( $user->id . $campaignID ) . ".zip";
        // zip file
        $fileZip = $this->uploadServer->zipForderCSV ( $pathFile, $pathFile, $fileName );
        // remove file csv
        foreach ( $result [0] as $item ) {
            $this->uploadServer->removeFileCSV ( $item );
        }
        return $fileZip;
    }

    /**
     * export PDF campaign
     *
     * {@inheritdoc}
     *
     * @see \App\Services\Reports\IReportService::exportPDFCampaign()
     */
    public function exportPDFCampaign($userID, $campaignID, $request) {
        $user = $this->authService->getUserInfo ( $userID );
        $listCountry = $request->get ( "list_country", "" );
        $path = "/uploads/export-campaigns/" . md5 ( Auth::user ()->id ) . "/" . md5 ( $campaignID ) . "/pdf/";
        // make forder
        $this->uploadServer->makeForder ( $path );
        //clear forder
        $this->uploadServer->clearFolder($path);
        $fileName = public_path ( $path . "template-pdf.pdf" );
        //$url = "http://127.0.0.1/template-pdf?user_id={$user->id}&currency={$user->currency}&campaign_id={$campaignID}&list_country={$listCountry}";
        $url = url ( '/' ) . "/template-pdf?user_id={$user->id}&currency={$user->currency}&campaign_id={$campaignID}&list_country={$listCountry}";
        exec ( "xvfb-run wkhtmltopdf -O Landscape  --javascript-delay 1000 --enable-local-file-access -T 0 -B 0 -L 0 -R 0 --encoding UTF-8 '{$url}' {$fileName}" );
        return $fileName;
    }

    /**
     * FN get data export campaign
     *
     * {@inheritdoc}
     *
     * @see \App\Services\Reports\IReportService::getDataExportPDFCampaign()
     */
    public function getDataExportPDFCampaign($request) {
        $listCountry = $request->get ( "list_country", "" );
        $campaignID = $request->get ( "campaign_id" );
        $userID = $request->get ( "user_id" );
        $currency = $request->get ( "currency" );
        $result = $this->reportCampaignRepo->exportPDFCampaign ( $campaignID, $userID, $listCountry );
        foreach ( $result->data_table as $item ) {
            $country = $this->getCountry ( strtoupper ( $item->country ) );
            $country = count ( $country ) > 1 ? "Unknown" : $country;
            $currency = $this->getCurrency ( strtoupper ( $currency ) )->code;
            $item->country = $country;
            $item->network = $item->network != "" ? strtoupper ( $item->network ) : "Unknown";
            $item->delivery_rate = $item->delivered == 0 || $item->totals == 0 ? 0 . "%" : round ( $item->delivered / $item->totals * 100, 0 ) . "%";
        }
        $valueStatus = [['', '']];
        $valueTotalExpenses = [['', '']];
        $valueDeliveredRate = [[
            'Element',
            'Density',
            ( object ) [
                    "role" => 'style'
            ],
            ( object ) [
                    "role" => 'annotation'
            ]
        ]];
        $colorChart = config ( "constants.color_chart_pdf" );
        foreach ( $result->data_status as $item ) {
            array_push ( $valueStatus, [
                    $item->return_status,
                    ( int ) $item->total
            ] );
        }

        foreach ( $result->data_total_expenses as $item ) {
            $country = $item->country == 'Unknown' ? $item->country : $this->getCountry ( strtoupper ( $item->country ) );
            $country = count ( $country ) > 1 ? "Unknown" : $country;
            $item->country = $country;
            array_push ( $valueTotalExpenses, [
                    $item->country,
                    floatval ( number_format ( $item->expenses, 2 ) )
            ] );
        }

        foreach ( $result->data_delivered_rate as $item ) {
            $country = $item->country == 'Unknown' ? $item->country : $this->getCountry ( strtoupper ( $item->country ) );
            $country = count ( $country ) > 1 ? "Unknown" : $country;
            $item->country = $country;
            array_push ( $valueDeliveredRate, [
                    $item->country,
                    floatval ( round ( $item->delivered_rate, 0 ) ),
                    $colorChart,
                    round ( $item->delivered_rate, 0 ) . "%"
            ] );
        }

        $campaign = $this->campaignService->getCampaignWithSubscriberList ( $userID, $campaignID );
        $campaign = $campaign [0];
        $data = Carbon::parse ( $campaign->send_time );
        $campaign->send_time = $data->format ( "h:i A d F Y" );

        return ( object ) array (
                "data_status" => $valueStatus,
                "data_total_expenses" => $valueTotalExpenses,
                "data_delivered_rate" => $valueDeliveredRate,
                "data_table" => $result->data_table,
                "currency" => $currency,
                "campaign" => $campaign
        );
    }

    /**
     * create export center
     *
     * {@inheritdoc}
     *
     * @see \App\Services\Reports\IReportService::reportCenter()
     */
    public function reportCenter($request) {
        $user = Auth::user ();

        if (! ! $user->isGroup4 ()) {
            return $this->fail ();
        }
        $from = $request->get ( "from", null );
        $to = $request->get ( "to", null );
        $time_zone = $request->get ( "timezone", null );
        $notification_emails = $request->get ( "emails", null );
        // remove field
        $request = $request->except ( [
                'from',
                'to',
                'timezone',
                'emails'
        ] );
        $request ["currency"] = "on";
        $attributes = [
                "user_id" => $user->id,
                "from" => $from != "" ? Carbon::parse ( $from )->format ( 'Y-m-d H:i:s' ) : null,
                "to" => $to != "" ? Carbon::parse ( $to )->format ( 'Y-m-d H:i:s' ) : null,
                "time_zone" => $time_zone,
                "params" => $request,
                "notification_emails" => $notification_emails
        ];
        $client = $this->reportCenterRepo->create ( $attributes );
        if (empty ( $client )) {
            return $this->fail ();
        }
        return $this->success ( $client->toArray () );
    }

    /**
     * get data export center
     *
     * {@inheritdoc}
     *
     * @see \App\Services\Reports\IReportService::getDataReportCenter()
     */
    public function getDataReportCenter($request) {
        $user = Auth::user ();
        $str_query = $this->reportCenterRepo->scopeQuery ( function ($query) use ($request, $user) {
            $query = $query->where ( 'user_id', ! ! $user->isGroup4 () ? $user->reader_id : $user->id );

            if (! ! $request->has ( "field" )) {
                $query = $query->orderBy ( $request->get ( "field" ), $request->get ( "orderBy", "DESC" ) );
            }
            return $query;
        } );

        $results = $str_query->paginate ( 10 )->toArray ();

        return $this->success ( [
                "data" => $results ['data'],
                "recordsFiltered" => $results ['total']
        ] );
    }

    /**
     * cron job export csv of report center
     *
     * {@inheritdoc}
     *
     * @see \App\Services\Reports\IReportService::cronJobReportCenter()
     */
    public function cronJobReportCenter() {
        $arrParams = [
            "client_name" => "Account Name",
            "campaign_id" => "Campaign ID",
            "sender" => "From",
            "phone" => "To",
            "return_message_id" => "MessageId",
            "updated_at" => "Sent time",
            "country" => "Country Name",
            "network" => "Network Name",
            "return_status" => "Status",
            "return_status_message" => "Status Message",
            "report_updated_at" => "Done at",
            "sum_price_client" => "Purchase Price Per Message",
            "currency" => "Purchase Currency",
            "service_provider" => "Service Name",
            "message_count" => "Message count",
            "message" => "Message text"
        ];

        $reportCenters = $this->reportCenterRepo->scopeQuery ( function ($query) {
            return $query->orderBy ( 'updated_at', 'asc' );
        } )->with ( [
                'user'
        ] )->findByField ( "status", "PENDING" )->toArray ();
        if (count ( $reportCenters ) > 0) {
            foreach ( $reportCenters as $item ) {
                try {
                    $item = ( object ) $item;
                    // get user
                    $userReport = ( object ) $item->user;
                    // setup header and field export
                    $headers = [ ];
                    $fields = [ ];
                    $params = ( array ) $item->params;
                    foreach ( $arrParams as $key => $value ) {
                        if (isset ( $params [$key] ) && $params [$key] == "on") {
                            if ($params [$key] == "on") {
                                array_push ( $headers, "'" . $value . "' AS " . $key );
                                if ($key == 'phone' && !! $userReport->encrypted) {
                                    array_push ( $fields, "ENCRYPT_PHONE(phone) AS phone" );
                                }else {
                                    array_push ( $fields, "IFNULL(" . $key . ", '') AS " . $key );
                                }
                            }
                        }
                    }

                    // update status PROCESSING
                    $attributes = [
                        "status" => "PROCESSING"
                    ];
                    $this->reportCenterRepo->update ( $attributes, $item->id );

                    $listUser = "";
                    if ($userReport->type == "GROUP4") {
                        $listUser = $userReport->reader_id;
                    } else if ($userReport->type == "GROUP3") {
                        $listUser = $userReport->id;
                    } else if ($userReport->type == "GROUP2") {
                        $users = $this->authService->getAllUserGroup3 ( $userReport->id, false );
                        $listUser = implode ( ",", $users->pluck ( "id" )->all () );
                    } else {
                        $users = $this->authService->getAllUserGroup3 ( null, false );
                        $listUser = implode ( ",", $users->pluck ( "id" )->all () );
                    }
                    $typeUser = $userReport->type;
                    $dateFrom = $item->from != "" ? Carbon::createFromFormat ( 'Y-m-d H:i:s', Carbon::parse ( $item->from ), $item->time_zone )->setTimezone ( "UTC" )->toDateTimeString () : '';
                    $dateTo = $item->to != "" ? Carbon::createFromFormat ( 'Y-m-d H:i:s', Carbon::parse ( $item->to ), $item->time_zone )->setTimezone ( "UTC" )->toDateTimeString () : '';
                    $campaignName = $item->params->campaign_name;
                    $headers = implode ( ", ", $headers );
                    $fields = implode ( ", ", $fields );
                    $path = config ( "constants.path_file_report_center" );
                    $this->uploadServer->makeForder ( $path );
                    $hashFile = md5 ( $item->id . uniqid () . time () );
                    $pathFile = public_path ( $path ) . $hashFile . ".csv";
                    //$pathFile = '/var/www/html/abc/' . $hashFile. ".csv";
                    $this->uploadServer->removeFileCSV ( $pathFile );
                    $result = $this->reportCenterRepo->reportCenter ( $listUser, $dateFrom, $dateTo, $campaignName, $headers, $fields, $typeUser, $item->time_zone, $pathFile );
                    //$result = $this->exportCenterNew( $listUser, $dateFrom, $dateTo, $campaignName, $headers, $fields, $typeUser, $item->time_zone, $path, $hashFile);
                    if (!!$result) {
                        // convert utf-8 file
                        $this->uploadServer->formatUtf8File($pathFile);

                        // update status PROCESSED
                        $attributes = [
                            "status" => "PROCESSED",
                            "result" => $hashFile
                        ];
                        $report = $this->reportCenterRepo->update ( $attributes, $item->id );
                        $report = (object) $report->toArray();
                        if ($item->notification_emails != "") {
                            // send email
                            $title = 'Your report is ready';
                            $created_at = Carbon::parse( $item->created_at )->setTimezone ( $userReport->time_zone)->toDateTimeString ();
                            $updated_at = Carbon::parse( $report->updated_at )->setTimezone ( $userReport->time_zone)->toDateTimeString ();
                            $content = "Report you have requested on " . $created_at . " was completed on " . $updated_at . ".";
                            $objectContent = ( object ) array (
                                "content" => $content
                            );
                            $templateEmailObj = new ExportCenter ( $title, $objectContent );
                            $emailsList = explode ( ';', $item->notification_emails );
                            try {
                                $this->mailService->notifyMail ( $emailsList, $templateEmailObj );
                            }catch (\Exception $e) {
                                return  true;
                            }

                        }

                        return true;
                    } else {
                        throw new \Exception ();
                    }
                } catch ( \Exception $e ) {
                    // update status PENDING
                    $attributes = [
                        "status" => "PENDING"
                    ];
                    $this->reportCenterRepo->update ( $attributes, $item->id );
                    return false;
                }
            }
        }

        return false;
    }

    public function getTotalClickOfShortLink($userID, $campaignID) {
        try {
            $client = new Client([
                'verify' => false
            ]);

            $url = env('DCT_API_GET_TOTAL_CLICK');

            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . env('DCT_TOKEN_SHORT_LINK'),
            ];

            $response = $client->get($url . "/" . $userID . "/". $campaignID, ['headers' => $headers]);
            $result = json_decode($response->getBody()->getContents());

            if (in_array($response->getStatusCode(), [200, 201])) {
                $result = $result->data;
                return $this->success($result);
            }

            return $this->fail();
        }catch(\Exception $e) {
            return $this->fail();
        }
    }
}
