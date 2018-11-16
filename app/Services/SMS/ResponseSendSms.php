<?php

namespace App\Services\SMS;


use Illuminate\Support\Facades\Log;
use App\Repositories\Campaign\IQueueRepository;
use App\Services\Campaign\ICampaignPausedService;
use App\Services\Campaign\ICampaignService;
use App\Services\Clients\IClientService;
use App\Services\Campaign\ISentCampaignService;


class ResponseSendSms implements IResponseSendSms
{
    public $queueRepo;
    public $handleStatusSms;
    public $campaignPausedService;
    public $clientService;
    public $sentCampaignService;

    public function __construct(
        IQueueRepository $queueRepo,
        IHandleStatusSms $handleStatusSms,
        ICampaignPausedService $campaignPausedService,
        IClientService $clientService,
        ISentCampaignService $sentCampaignService
    )
    {
        $this->queueRepo = $queueRepo;
        $this->handleStatusSms = $handleStatusSms;
        $this->campaignPausedService = $campaignPausedService;
        $this->clientService = $clientService;
        $this->sentCampaignService = $sentCampaignService;
    }

    /**
     * fn receive when send sms async
     * @param object|string $response
     * @return null|int
     */
    public function responseSentSMSAsync($response = null)
    {
        $data = $response->data;
        $result = null;
        if (!empty($data)) {
            switch ($response->dataQueue->service_provider) {
                case 'MESSAGEBIRD':
                        // Write Log
                    \CustomLog::info('Send SMS Via MESSAGEBIRD', 'SendSMS-' . date('Y-m-d'), json_decode(json_encode($data), true));
                    $result = $this->handleStatusSms->formatResultMessageBird($data);
                    break;
                case 'INFOBIP':
                        // Write Log
                    \CustomLog::info('Send SMS Via INFOBIP', 'SendSMS-' . date('Y-m-d'), json_decode(json_encode($data), true));
                    $result = $this->handleStatusSms->formatResultInfoBip($data);
                    break;
                case 'TMSMS':
                        // Write Log
                    \CustomLog::info('Send SMS Via TM SMS', 'SendSMS-' . date('Y-m-d'), json_decode(json_encode($data), true));
                    $result = $this->handleStatusSms->formatResultTMSMS($data);
                    break;
                case 'ROUTEMOBILE':
                        // Write Log
                    \CustomLog::info('Send SMS Via Route Mobile', 'SendSMS-' . date('Y-m-d'), json_decode(json_encode($data), true));
                    $result = $this->handleStatusSms->formatResultRouteMobile($data);
                    break;
                default:
                    // Write Log
                    \CustomLog::info('Send SMS Via Default Gateway', 'SendSMS-' . date('Y-m-d'), json_decode(json_encode($data), true));
                    $result = $this->handleStatusSms->formatResultInfoBip($data);
                    break;
            }
        }
        
        // update queue
        return $this->updateQueueAsync($result, $response->dataQueue);
    }

    /**
     * fn update queue async
     * @param object|null $smsResults
     * @param object $dataQueue
     * @return null|boolean
     */
    public function updateQueueAsync($smsResults, $dataQueue)
    {
        try {
            // --- Update queue
            $queueParams = array_shift($smsResults);
            $campaign = $dataQueue->campaign;
            $currentUser = $dataQueue->user;
            // $queueParams = [
            //     'status' => 'SENT'
            // ];
            // check send sms into service provider
            if ($queueParams) {
                // Log::error ( 'SenT SendSMS-' . date ( 'Y-m-d' ) . " " .  json_decode($queue->phone, true) );
                $queueParams['status'] = 'SENT';
                $queueParams['report_updated_at'] = date('Y-m-d H:i:s');
                $queueParams['service_provider'] = $dataQueue->service_provider;
                $queueParams['message'] = $dataQueue->message;
                $queueParams['message_count'] = $dataQueue->message_count;
                $queueParams['sum_price_agency'] = $dataQueue->sum_price_agency;
                $queueParams['sum_price_client'] = $dataQueue->sum_price_client;
                $this->queueRepo->updateQueue($queueParams, $dataQueue->id, $currentUser->id, $campaign->id);

                // 6. Count totals
                $resultCount = $this->queueRepo->getTotalsByStatus($currentUser->id, $campaign->id);
                $totalsQueue = $resultCount['PENDING'] + $resultCount['SENDING'];

                $data = (object)[
                    "status" => $totalsQueue == 0 ? "success" : 'ready',
                    "result" => $resultCount,
                    "campaign" => $campaign,
                    "flagShortLink" => false
                ];

                return $this->sentCampaignService->queueSentCampaign($data);
            } else {
                // dont connect to service provider
                // create or update campaign paused
                $attributes = [
                    "user_id" => $currentUser->id,
                    "campaign_id" => $campaign->id,
                    "queue_id" => $dataQueue->id,
                ];
                $result = $this->campaignPausedService->updateOrCreate($attributes);
                if (!empty($result)) {
                    $result = (object)$result->toArray();
                    if ($result->count >= 3) {
                        $msg = Lang::get('notify.error_detected');
                        // update all queue pending into failed
                        $this->queueRepo->updateAllFailed($currentUser->id, $campaign->id, 'Can not send SMS to Service Provider.');
                        // remove data campaign paused
                        $this->campaignPausedService->removeCampaignPaused($result->id);
                    } else {
                        $this->queueRepo->updatePendingAllQueue($currentUser->id, $campaign->id);
                    }

                    $data = (object)[
                        "status" => "paused",
                        "result" => $result,
                        "campaign" => $campaign
                    ];

                    // response campaign pause
                    return $this->sentCampaignService->queueSentCampaign($data);
                }
                return null;
            }

            return null;
        }catch(\Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
        
    }
}