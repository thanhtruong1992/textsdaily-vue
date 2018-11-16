<?php

namespace App\Services\Campaign;

use App\Services\Auth\IAuthenticationService;
use App\Services\NotificationSettings\INotificationSettingService;
use Illuminate\Support\Facades\Log;
use App\Mail\CampaignStart;
use App\Repositories\Campaign\ICampaignRepository;

class SentCampaignService implements ISentCampaignService {
    //
    protected $campaignRepo;
    protected $mailService;
    protected $authService;
    protected $notificationSettingService;
    /**
     */

    public function __construct(
        IAuthenticationService $authService,
        INotificationSettingService $notificationSettingService,
        ICampaignRepository $campaignRepo
    )
    {
        $this->authService = $authService;
        $this->notificationSettingService = $notificationSettingService;
        $this->campaignRepo = $campaignRepo;

    }

    /**
     * fn response when finished send campaign
     * @param object $resultSent
     */
    public function queueSentCampaign($resultSent) {
        $campaign = (object) $resultSent->campaign;
        $user = $this->authService->getUserInfo($campaign->user_id);
        $notificationSetting = $this->notificationSettingService->getNotificationOfUser($campaign->user_id);
        $campaignParams = array();
        $flagShortLink = $resultSent->flagShortLink;
        if ( $resultSent->status == "success") {
            // send campaign success
            $resultSent = (array) $resultSent->result;
            $campaignParams['status'] = 'SENT';
            $campaignParams['total_recipients'] = $resultSent['TOTALS'];
            $campaignParams['total_sent'] = $resultSent['SENT'];
            $campaignParams['total_failed'] = $resultSent['FAILED'];
            $campaignParams['send_process_finished_on'] = date('Y-m-d H:i:s');
            // update campaign
            $campaign =  $this->campaignRepo->updateByUser( $campaignParams, $campaign->id, $campaign->user_id );

            // Send email notification
            $subjectEmail = 'Campaign ' . $campaign->name . ' sent.';
            $object = (object) [
                    "name" => $campaign->name,
                    "content" => "Campaign sent."
            ];
            $this->sendEmailNotificationCampaign($subjectEmail, $object, $user->email, $notificationSetting->notification->finished);

        } else if ($resultSent->status == 'ready') {
            // send countine
        }else if( $resultSent->status == 'paused') {
            $resultSent = $resultSent->result;

            $object = (object) [
                    "name" => $campaign->name,
                    "content" => "Campaign encountered an error while sending. Please contact your account manager for more information. Thank you."
            ];
            if($resultSent->count >= 3) {
                // paused campaign
                if($resultSent->queue_id == 1) {
                    // queue first item
                    $campaignParams['status'] = 'FAILED';
                    $notificationEmail = $notificationSetting->notification->failed;
                    $subjectEmail = 'Campaign Failed.';
                }else {
                    $campaignParams['status'] = 'SENT';
                    $notificationEmail = $notificationSetting->notification->finished;
                    $subjectEmail = 'Campaign Sent.';
                    $object->content = "Campaign sent.";
                }

                $campaignParams['send_process_finished_on'] = date('Y-m-d H:i:s');
                $campaign =  $this->campaignRepo->updateByUser( $campaignParams, $campaign->id, $campaign->user_id );
                $this->sendEmailNotificationCampaign($subjectEmail, $object, $user->email, $notificationEmail);
            }else {
                if($resultSent->count == 1) {
                    // Send email notification paused campaign
                    $campaignParams['status'] = 'PAUSED';
                    $campaign =  $this->campaignRepo->updateByUser( $campaignParams, $campaign->id, $campaign->user_id );
                    $subjectEmail = 'Campaign Paused.';
                    $notificationEmail = $notificationSetting->notification->paused;
                    $this->sendEmailNotificationCampaign($subjectEmail, $object, $user->email, $notificationEmail);
                }
            }
        } else {
            $resultSent = $resultSent->result;
            $campaignParams['status'] = 'FAILED';
            $campaignParams['total_recipients'] = $resultSent['TOTALS'];
            $campaignParams['total_sent'] = $resultSent['SENT'];
            $campaignParams['total_failed'] = $resultSent['FAILED'];
            $campaignParams['send_process_finished_on'] = date('Y-m-d H:i:s');
            // update campaign
            $campaign =  $this->campaignRepo->updateByUser( $campaignParams, $campaign->id, $campaign->user_id );

            // Send email notification
            $subjectEmail = 'Campaign ' . $campaign->name . ' failed.';
            $object = (object) [
                    "name" => $campaign->name,
                    "content" => "Campaign encountered an error while sending. Please contact your account manager for more information. Thank you."
            ];
            $this->sendEmailNotificationCampaign($subjectEmail, $object, $user->email, $notificationSetting->notification->failed);
        }

        if($flagShortLink != false) {
            try {
                $subjectEmail = 'Queue Short Link.';
                $object = (object) [
                    "name" => "Campaign name: " . $campaign->name . ". Campaign id: " . $campaign->id,
                    "content" => "Queue don't replace short link: " . $flagShortLink
                ];
                $this->sendEmailNotificationCampaign($subjectEmail, $object, $user->email, config('constants.list_email_notify_queue_again'));
            }catch(\Exception $e){
                Log::error('Error Send Email Faild Short Link ' . json_encode($e->getMessage()));
            }
        }

        return;
    }

    public function sendEmailNotificationCampaign($subjectEmail, $object, $emailUser, $notificationSetting) {
        try {
            $templateEmailObj = new CampaignStart( $subjectEmail, $object);
            $emailsList = array( $emailUser);
            if(!empty($notificationEmail)) {
                $emailsList = array_merge( explode( ',', $notificationSetting), $emailsList );
            }
            $this->mailService->notifyMail( $emailsList, $templateEmailObj );
        }catch(\Exception $e) {
            //
        }
    }
}