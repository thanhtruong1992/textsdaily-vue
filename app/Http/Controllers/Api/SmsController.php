<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\Campaign\ICampaignService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Lang;
use Mockery\Exception;
use App\Http\Requests\Api\ApiCreateSmsRequest;
use App\Services\Campaign\IQueueService;

class SmsController extends ApiController {
    protected $campaignService;
    protected $queueService;

    public function __construct(
            ICampaignService $campaignService,
            IQueueService $IQueueService
    )
    {
        $this->campaignService = $campaignService;
        $this->queueService = $IQueueService;
    }

    /**
     * fn add sms api account
     */
    public function addSMS(ApiCreateSmsRequest $request) {
        try {
            //get first campaign 
            $campaign = $this->campaignService->getFirstCampaign();
            if(!$campaign->status) {
                return $this->badRequest();
            }
            // addd sms in queue
            $result = $this->queueService->addSMS($request, $campaign->data);
            if(!$result->status ) {
                return $this->badRequest();
            }

            return $this->success($result->data);
            
        }catch(\Exception $e) {
            return $this->badRequest($e->getMessage());
        }
    }

    /**
     * fn get report api account
     */
    public function getReport($uuid) {
        try {
            // get first campaign
            $campaign = $this->campaignService->getFirstCampaign();
            if(!$campaign->status) {
                return $this->badRequest();
            }

            // get report into queue
            $report = $this->queueService->getReportApi($uuid, $campaign->data);
            if( !$report->status ) {
                return $this->notFound(trans('report.not_found'));
            }

            return $this->success($report->data);
        } catch(\Exception $e) {
            return $this->badRequest($e->getMessage());
        }
    }
}
