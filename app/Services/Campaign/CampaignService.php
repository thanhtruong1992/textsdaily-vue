<?php
namespace App\Services\Campaign;

use App\Services\BaseService;
use App\Repositories\Campaign\ICampaignRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use App\Http\Requests\CreateCampaignRequest;
use App\Services\Campaign\ICampaignRecipientsService;
use Auth;
use App\Repositories\SubscriberLists\ISubscriberListRepository;
use App\Services\MailServices\IMailService;
use App\Mail\CampaignStart;
use App\Mail\CampaignEnd;
use Carbon\Carbon;
use App\Services\Auth\IAuthenticationService;
use App\Services\CustomFields\ICustomFieldService;
use App\Services\SMS\ISMSService;
use App\Services\Clients\IClientService;
use App\Models\Queue;
use App\Services\NotificationSettings\INotificationSettingService;
use App\Repositories\Campaign\ICampaignPausedRepository;
use App\Services\ShortLinks\IShortLinkService;
use App\Repositories\Campaign\IQueueRepository;
use App\Repositories\Subscribers\ISubscriberRepository;
use Illuminate\Support\Facades\Log;

class CampaignService extends BaseService implements ICampaignService {

    //
    protected $campaignRepo;
    protected $campaignRecipientsService;
    protected $queueService;
    protected $subscriberListRepo;
    protected $mailService;
    protected $campaignLinksService;
    protected $authService;
    protected $customFieldService;
    protected $smsService;
    protected $clientService;
    protected $notificationSettingService;
    protected $campaignPausedRepo;
    protected $shortLinkService;
    protected $queueRepo;
    protected $subscriberRepo;
    protected $sentCampaignService;
    /**
     */
    public function __construct( ICampaignRepository $ICampaignRepo,
            ICampaignRecipientsService $ICampaignRecipientsService,
            IQueueService $IQueueService,
            ISubscriberListRepository $SubscriberListRepo,
            IMailService $IMailService,
            ICampaignLinkService $ICampaignLinksService,
            IAuthenticationService $authService,
            ICustomFieldService $customFieldService,
            ISMSService $smsService,
            IClientService $clientService,
            INotificationSettingService $notificationSettingService,
            ICampaignPausedRepository $campaignPausedRepo,
            IShortLinkService $shortLinkService,
            IQueueRepository $IQueueRepo,
            ISubscriberRepository $subscriberRepo,
            ISentCampaignService $sentCampaignService
            ) {
                $this->campaignRepo = $ICampaignRepo;
                $this->campaignRecipientsService = $ICampaignRecipientsService;
                $this->queueService = $IQueueService;
                $this->subscriberListRepo = $SubscriberListRepo;
                $this->mailService = $IMailService;
                $this->campaignLinksService = $ICampaignLinksService;
                $this->authService = $authService;
                $this->customFieldService = $customFieldService;
                $this->smsService = $smsService;
                $this->clientService = $clientService;
                $this->notificationSettingService = $notificationSettingService;
                $this->campaignPausedRepo = $campaignPausedRepo;
                $this->shortLinkService = $shortLinkService;
                $this->queueRepo = $IQueueRepo;
                $this->subscriberRepo = $subscriberRepo;
                $this->sentCampaignService = $sentCampaignService;
    }

    /**
     *
     * @param Request $request
     */
    public function getCampaignByQuery(Request $request) {
        $paging = $request->get ( 'page' );
        $search_key = $request->get ( 'search' );
        $column_sort = $request->get ( 'field' );
        $orderBy = $request->get ( 'orderBy' );
        $results = $this->campaignRepo->getCampaignByQuery ( $search_key, $column_sort, $orderBy, $paging );
        $dataList = $results->items ();
        foreach ( $dataList as $item ) {
            $time_remaining = 0;
            if ($item->status == "ready" && $item->schedule_type == 'IMMEDIATE') {
                $time_send = strtotime ( $item->send_time );
                $timezone_remaining = $this->getCurrentTimeByTimeZone($item->send_timezone);
                $time_remaining = $time_send - $timezone_remaining;
                $item->status = "Now";
                $item->time_count_down = $time_remaining;
            } elseif ($item->status == "ready" && $item->schedule_type == 'FUTURE') {
                $item->time_count_down = 0;
                $item->status = "scheduled";
            } else {
                $item->time_count_down = 0;
            }
            if ($item->send_time != null) {
                $item->send_time = date(config('app.datetime_format'), strtotime($item->send_time));
            } else {
                $item->send_time = "";
            }
        }
        return [
                'data' => $dataList,
                'recordsTotal' => $results->total (),
                'recordsFiltered' => $results->total (),
                'total' => $results->total ()
        ];
    }

    /**
     * Delete campaign item
     *
     * {@inheritdoc}
     * @see \App\Services\Campaign\ICampaignService::deleteCampaign()
     */
    public function deleteCampaign($campaign_id) {
        if (isset ( $campaign_id )) {
            $result = $this->campaignRepo->deleteCampaignItem ( $campaign_id );
            return $result;
        }
    }

    /**
     * Amend campaign item
     *
     * {@inheritdoc}
     * @see \App\Services\Campaign\ICampaignService::deleteCampaign()
     */
    public function amendCampaign($campaign_id) {
        if (isset ( $campaign_id )) {
            $result = $this->campaignRepo->amendCampaignItem ( $campaign_id );
            return $result;
        }
    }

    /**
     * Update campaign item status
     *
     * {@inheritdoc}
     * @see \App\Services\Campaign\ICampaignService::deleteCampaign()
     */
    public function updateCampaignStatus($campaign_id) {
        if (isset ( $campaign_id )) {
            $result = $this->campaignRepo->updateStatusCampaign( $campaign_id );
            return $result;
        }
    }

    /**
     *
     * @param Request $request
     */
    public function createCampaign(CreateCampaignRequest $request) {
        $currentUser = Auth::user ();
        // check if credit of user > 0
        if ($currentUser->billing_type != "UNLIMITED" && $currentUser->getBalance() <= 0 && $request ['schedule_type'] != 'NOT_SCHEDULED') {
            return [
                    'status' => false,
                    'message' => Lang::get ( $request->get('isPersonalize') == "true" ? 'notify.not_enough_balance_with_personalize' : 'notify.not_enough_balnce')
            ];
        }

        $request ['user_id'] = $currentUser->id;
        $request ['created_by'] = $currentUser->id;
        $campaign_link_id = $request->get('campaign_link_id');
        if ($request ['schedule_type'] == 'NOT_SCHEDULED') {
            $request ['status'] = 'DRAFT';
        } elseif ($request ['schedule_type'] == 'IMMEDIATE') {
            $currentTimeByTimezone = $this->getCurrentTimeByTimeZone ( $currentUser->time_zone );
            $request ['status'] = 'READY';
            $request ['send_time'] = date ( 'Y-m-d H:i', $currentTimeByTimezone + (5 * 60));
            $request ['send_timezone'] = $currentUser->time_zone;
        } elseif ($request ['schedule_type'] == 'FUTURE') {
            $request ['status'] = 'READY';
        }

        $message = str_replace('\r\n', '\n', $request['message']);
        $request['message'] = $message;

        $campaign = $this->campaignRepo->create ( $request->toArray () );
        //
        if ($campaign) {
            // update unsubscribe message in campaign
            if(strpos($campaign['message'], '%%unsubscribe%%') != -1) {
                $link = $this->addLinkUnsubscriber($campaign['id']);
                $message = str_replace('%%unsubscribe%%', $link, $campaign['message']);
                $this->campaignRepo->update([
                    "message" => $message
                ], $campaign['id']);
            }

            $totalSub = 0;
            // Store campagain recipients
            foreach ( $request ['list_id'] as $listID ) {
                $campaignRecipients = array ();
                $campaignRecipients ['user_id'] = $currentUser->id;
                $campaignRecipients ['created_by'] = $currentUser->id;
                $campaignRecipients ['campaign_id'] = $campaign->id;
                $campaignRecipients ['list_id'] = $listID;
                $this->campaignRecipientsService->createCampaignRecipients ( $campaignRecipients );

                if($totalSub <= 10 && $request ['schedule_type'] == 'IMMEDIATE') {
                    // get total sub of list sub
                    $resultTotal = $this->subscriberRepo->getTotalSubscriberByListID( $listID );
                    if(!empty($resultTotal)) {
                        $totalSub += $resultTotal->totals;
                    }
                }
            }

            if($totalSub <= 10 && $request ['schedule_type'] == 'IMMEDIATE') {
                $this->campaignRepo->update([
                    "send_time" => date ( 'Y-m-d H:i', $currentTimeByTimezone + (0.5 * 60))
                ], $campaign['id']);
            }
            // Store campaign links
            if ( isset($request['campaign_link_id']) && $campaign->id ) {
                $arrIDLinks = explode( ',', $request['campaign_link_id'] );
                $arrID = $this->removeCampaignLink($message,$arrIDLinks);
                $this->campaignLinksService->updateCampaignID( array( 'campaign_id' => $campaign->id ), $arrID );
            }

            if($campaign->status != "DRAFT" && $campaign->schedule_type == 'FUTURE') {
                // send notification email
                $notificationSetting = $this->notificationSettingService->getNotificationOfUser($currentUser->id);
                try {
                    $subjectEmail = 'Campaign scheduled.';
                    $object = (object) [
                            "name" => $campaign->name,
                            "content" => "Campaign scheduled."
                    ];
                    $this->sentCampaignService->sendEmailNotificationCampaign($subjectEmail, $object, $currentUser->email, $notificationSetting->notification->progress);
                }catch(\Exception $e){

                }
            }

            return [
                    'status' => true,
                    'message' => Lang::get ( 'campaign.create_campaign' )
            ];
        } else {
            return [
                    'status' => false,
                    'message' => Lang::get ( 'campaign.create_failed' )
            ];
        }
    }

    /**
     *
     * @param int $id
     */
    public function getCampaignInfo($id, $userID = null) {
        return $this->campaignRepo->find ( $id , $userID);
    }

    /**
     *
     * @param int $id
     * @param Request $request
     */
    public function updateCampaign($id, CreateCampaignRequest $request) {
        $item = $this->campaignRepo->find( $id );

        if ($item->status == "DRAFT" || ($item->status == "READY" && $item->schedule_type == "FUTURE")) {
        } else {
            return [
                    'status' => false,
                    'message' => Lang::get ( 'notify.updated_failed' )
            ];
        }

        $currentUser = Auth::user ();

        if ($currentUser->billing_type != "UNLIMITED" && $currentUser->getBalance() <= 0 && $request ['schedule_type'] != 'NOT_SCHEDULED') {
            return [
                    'status' => false,
                    'message' => Lang::get ( $request->get('isPersonalize') == "true" ? 'notify.not_enough_balance_with_personalize' : 'notify.not_enough_balnce')
            ];
        }

        //
        $request ['user_id'] = $currentUser->id;
        $request ['updated_by'] = $currentUser->id;
        if ($request ['schedule_type'] == 'NOT_SCHEDULED') {
            $request ['status'] = 'DRAFT';
            $request ['send_time'] = null;
            $request ['send_timezone'] = null;
        } elseif ($request ['schedule_type'] == 'IMMEDIATE') {
            $currentTimeByTimezone = $this->getCurrentTimeByTimeZone ( $currentUser->time_zone );
            $request ['status'] = 'READY';
            $request ['send_time'] = date ( 'Y-m-d H:i', $currentTimeByTimezone + (5 * 60) );
            $request ['send_timezone'] = $currentUser->time_zone;
        } elseif ($request ['schedule_type'] == 'FUTURE') {
            $request ['status'] = 'READY';
        }

        $message = str_replace('\r\n', '\n', $request['message']);
        $request['message'] = $message;

        $campaign = $this->campaignRepo->update ( $request->toArray (), $id );

        if ($campaign) {
            // update campaign recipients
            $old_list_campaign_recipient = $this->campaignRecipientsService->getCampaignRecipientsInfoByCampaignId ( $id , $currentUser->isGroup4() ? $currentUser->reader_id : null);
            //             dd($old_list_campaign_recipient);
            // Remove old campaign recipient
            foreach ( $old_list_campaign_recipient as $item ) {
                $this->campaignRecipientsService->deleteCampaignRecipientsByListId ( $item->list_id, $item->campaign_id );
            }

            $totalSub = 0;

            // Add new campaign recipients
            foreach ( $request ['list_id'] as $listID ) {
                $campaignRecipients = array ();
                $campaignRecipients ['user_id'] = $currentUser->id;
                $campaignRecipients ['created_by'] = $currentUser->id;
                $campaignRecipients ['campaign_id'] = $campaign->id;
                $campaignRecipients ['list_id'] = $listID;
                $this->campaignRecipientsService->createCampaignRecipients ( $campaignRecipients );

                if($totalSub <= 10 && $request ['schedule_type'] == 'IMMEDIATE') {
                    // get total sub of list sub
                    $resultTotal = $this->subscriberRepo->getTotalSubscriberByListID( $listID );
                    if(!empty($resultTotal)) {
                        $totalSub += $resultTotal->totals;
                    }
                }
            }

            if($totalSub <= 10 && $request ['schedule_type'] == 'IMMEDIATE') {
                $this->campaignRepo->update([
                    "send_time" => date ( 'Y-m-d H:i', $currentTimeByTimezone + (0.5 * 60))
                ], $campaign['id']);
            }

            // update campaign
            $campaignLinks = $this->campaignLinksService->findCampaignLinkWithCampaign($campaign->id, $currentUser->id);
            $campaignIds = $campaignLinks->pluck('id')->all();
            $this->removeCampaignLink($message, $campaignIds);

            if($campaign->status != "DRAFT" && $campaign->schedule_type == 'FUTURE') {
                // send notification email
                $notificationSetting = $this->notificationSettingService->getNotificationOfUser($currentUser->id);
                try {
                    $subjectEmail = 'Campaign scheduled.';
                    $object = (object) [
                            "name" => $campaign->name,
                            "content" => "Campaign scheduled."
                    ];
                    $this->sentCampaignService->sendEmailNotificationCampaign($subjectEmail, $object, $currentUser->email, $notificationSetting->notification->scheduled);
                }catch(\Exception $e){

                }
            }

            return [
                    'status' => true,
                    'message' => Lang::get ( 'notify.updated_success' )
            ];
        } else {
            return [
                    'status' => false,
                    'message' => Lang::get ( 'notify.updated_failed' )
            ];
        }
    }

    /**
     *
     * @param array $campaigns
     * @return array
     */
    protected function __checkCampaignSendValid( $campaign ) {
        // Check send time by timezone
        $currentTimeByTimezone = $this->getCurrentTimeByTimeZone ( $campaign->send_timezone );
        if ($currentTimeByTimezone >= strtotime ( $campaign->send_time )) {
            return true;
        }
        return false;
    }

    /**
     * fn create queue job of send campaign
     * @param int $position
     */
    public function campaignNeedSend($position = 1) {
        $campaign = $this->campaignRepo->getReadyCampaign();

        if (!empty($campaign)) {
            // ID user
            $idUser = $campaign->user_id;
            $user = $this->authService->getUserInfo($idUser);
            $notificationSetting = $this->notificationSettingService->getNotificationOfUser($idUser);

            try {
                // Re-check valid campaign
                if (!$this->__checkCampaignSendValid($campaign)){
                    return false;
                }

                $statusCampaign = $campaign->status;
                if(!in_array($statusCampaign, ['READY', 'SENDING'])) {
                    return (object) [
                        'status' => false
                    ];
                }
                
                if($statusCampaign == 'READY') {
                    // Update campaign status to SENDING
                    $campaign = $this->campaignRepo->updateByUser( array( 'status' => 'SENDING', 'send_process_started_on' => date('Y-m-d H:i:s') ), $campaign->id, $idUser );

                    try {
                        // Send email notification sending
                        $subjectEmail = 'Campaign ' . $campaign->name . ' in proccess.';
                        $object = (object) [
                                "name" => $campaign->name,
                                "content" => "Campaign in proccess."
                        ];
                        $this->sentCampaignService->sendEmailNotificationCampaign($subjectEmail, $object, $user->email, $notificationSetting->notification->progress);
                    }catch(\Exception $e) {
                        Log::error('Error Send email proccess campaign ' . json_encode($e->getMessage()));
                    }
                }

                // Add recipients to campaign
                $recipients = $this->campaignRecipientsService->getRecipientsIDByCampaignId( $campaign->id, $idUser );
                $campaign->recipients = $recipients;
                // Call queue send
                $resultSend = $this->queueService->pushQueueSendSMS( $campaign , $position);

                if(!!$resultSend->status) {
                    // detect loop 10
                    if($resultSend->position < config("constants.limit_queue")) {
                        // callback create
                        return $this->campaignNeedSend($resultSend->position);
                    }else {
                        return (object) [
                            'status' => true
                        ];
                    }
                }

                return (object) [
                    'status' => false
                ];
            } catch (\Exception $e) {
                // Send email notification
                $campaignName = $campaign && $campaign->name ? $campaign->name : "";
                $subjectEmail = 'Campaign ' . $campaignName . ' was paused.';
                $object = (object) [
                        "name" => $campaignName,
                        "content" => "Camapign was paused."
                ];
                $this->sentCampaignService->sendEmailNotificationCampaign($subjectEmail, $object, $user->email, $notificationSetting->notification->paused);
                $campaignParams['status'] = 'PAUSED';
                $campaignParams['send_process_finished_on'] = date('Y-m-d H:i:s');
                $campaign =  $this->campaignRepo->updateByUser( $campaignParams, $campaign->id, $campaign->user_id );
                \CustomLog::error ( "Send Campaign #{$campaign->id} of user #{$campaign->user_id} was PAUSED", 'SendSMS-' . date ( 'Y-m-d' ), ['error' => $e->getMessage()] );
                return false;
            }
        }
        return false;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Campaign\ICampaignService::sendCampaigns()
     */
    public function sendCampaigns($campaign = null) {
        if($campaign == null) {
            //
            $campaign = $this->campaignRepo->getReadyCampaign();
        }
        //
        if ( $campaign ) {
            // ID user
            $idUser = $campaign->user_id;
            $user = $this->authService->getUserInfo($idUser);
            $notificationSetting = $this->notificationSettingService->getNotificationOfUser($idUser);

            try {
                // Re-check valid campaign
                if (!$this->__checkCampaignSendValid($campaign)){
                    return false;
                }

                $statusCampaign = $campaign->status;
                if($statusCampaign == 'READY') {
                    // Update campaign status to SENDING
                    $campaign = $this->campaignRepo->updateByUser( array( 'status' => 'SENDING', 'send_process_started_on' => date('Y-m-d H:i:s') ), $campaign->id, $idUser );
                }
                

                if($statusCampaign != "PAUSED") {
                    try {
                        // Send email notification sending
                        $subjectEmail = 'Campaign ' . $campaign->name . ' in proccess.';
                        $object = (object) [
                                "name" => $campaign->name,
                                "content" => "Campaign in proccess."
                        ];
                        $this->sendEmailNotificationCampaign($subjectEmail, $object, $user->email, $notificationSetting->notification->progress);
                    }catch(\Exception $e) {

                    }

                }

                //
                if ( $campaign ) {
                    // Add recipients to campaign
                    $recipients = $this->campaignRecipientsService->getRecipientsIDByCampaignId( $campaign->id, $idUser );
                    $campaign->recipients = $recipients;
                    // Call queue send
                    $resultSent = $this->queueService->sendSMS( $campaign );
                    $campaignParams = array();
                    if ( $resultSent->status == "success") {
                        // send campaign success
                        $resultSent = $resultSent->result;
                        $campaignParams['status'] = 'SENT';
                        $campaignParams['total_recipients'] = $resultSent['TOTALS'];
                        $campaignParams['total_sent'] = $resultSent['SENT'];
                        $campaignParams['total_failed'] = $resultSent['FAILED'];
                        $campaignParams['send_process_finished_on'] = date('Y-m-d H:i:s');
                        if ( $campaignParams['total_recipients'] ) {
                            $campaignParams['benchmark_per_second'] = ( strtotime( $campaignParams['send_process_finished_on'] ) - strtotime( $campaign->send_process_started_on ) ) / $campaignParams['total_recipients'];
                        }
                        $campaign =  $this->campaignRepo->updateByUser( $campaignParams, $campaign->id, $idUser );

                        // Send email notification
                        $subjectEmail = 'Campaign ' . $campaign->name . ' sent.';
                        $object = (object) [
                                "name" => $campaign->name,
                                "content" => "Campaign sent."
                        ];
                        $this->sendEmailNotificationCampaign($subjectEmail, $object, $user->email, $notificationSetting->notification->finished);

                    } else if ($resultSent->status == 'ready') {
                        $resultSent = $resultSent->result;
                        $campaignParams['status'] = 'READY';
                        $campaign =  $this->campaignRepo->updateByUser( $campaignParams, $campaign->id, $idUser );
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
                            $campaign =  $this->campaignRepo->updateByUser( $campaignParams, $campaign->id, $idUser );
                            $this->sendEmailNotificationCampaign($subjectEmail, $object, $user->email, $notificationEmail);
                        }else {
                            if($resultSent->count == 1) {
                                // Send email notification paused campaign
                                $campaignParams['status'] = 'PAUSED';
                                $campaign =  $this->campaignRepo->updateByUser( $campaignParams, $campaign->id, $idUser );
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
                        if ( $campaignParams['total_recipients'] ) {
                            $campaignParams['benchmark_per_second'] = ( strtotime( $campaignParams['send_process_finished_on'] ) - strtotime( $campaign->send_process_started_on ) ) / $campaignParams['total_recipients'];
                        }
                        $campaign =  $this->campaignRepo->updateByUser( $campaignParams, $campaign->id, $idUser );

                        // Send email notification
                        $subjectEmail = 'Campaign ' . $campaign->name . ' failed.';
                        $object = (object) [
                                "name" => $campaign->name,
                                "content" => "Campaign encountered an error while sending. Please contact your account manager for more information. Thank you."
                        ];
                        $this->sendEmailNotificationCampaign($subjectEmail, $object, $user->email, $notificationSetting->notification->failed);
                    }
                    //
                    return $campaign;
                }
            } catch (\Exception $e) {
                // Send email notification
                $campaignName = $campaign && $campaign->name ? $campaign->name : "";
                $subjectEmail = 'Campaign ' . $campaignName . ' was paused.';
                $object = (object) [
                        "name" => $campaignName,
                        "content" => "Camapign was paused."
                ];
                $this->sendEmailNotificationCampaign($subjectEmail, $object, $user->email, $notificationSetting->notification->paused);
                $campaignParams['status'] = 'PAUSED';
                $campaignParams['send_process_finished_on'] = date('Y-m-d H:i:s');
                $campaign =  $this->campaignRepo->updateByUser( $campaignParams, $campaign->id, $campaign->user_id );
                \CustomLog::error ( "Send Campaign #{$campaign->id} of user #{$campaign->user_id} was PAUSED", 'SendSMS-' . date ( 'Y-m-d' ), ['error' => $e->getMessage()] );
                return $e->getMessage();
            }
        }
        return false;
    }

    public function pushQueueReport($position = 1, $sentCampaign = null) {
        try {
            if(empty($sentCampaign)) {
                // 1. Get campaign status = SENT
                $sentCampaign = $this->campaignRepo->getSentCampaign();
            }
            
            if ( !empty($sentCampaign) ) {
                try {
                    if($sentCampaign->tracking_delivery_report == 'PENDING') {
                        // Update PROCESSING Status
                        $sentCampaign = $this->campaignRepo->updateByUser([
                            'tracking_delivery_report' => 'PROCESSING',
                            'tracking_delivery_report_update_at' => date('Y-m-d H:i:s')
                        ], $sentCampaign->id, $sentCampaign->user_id );
                    }

                    // Call queue service to tracking delivery status
                    $result = $this->queueService->pushQueueDeliveryReport( $sentCampaign->user_id, $sentCampaign->id, $position);
            
                    if($result->report_status == 'processed' && $result->position < config('constants.limit_queue_report')) {
                        return $this->pushQueueReport($result->position);
                    }

                    return true;
                } catch (\Exception $e) {
                    return $e->getMessage();
                }
            }
            return false;
        }catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function finsihedQueueReport($result) {
        $sentCampaign = $result->campaign;

        // finished get report campaign
        if($result->report_status == 'processed') {
            $campaignParams = [ 'tracking_delivery_report_update_at' => date('Y-m-d H:i:s') ];
            $campaignParams['tracking_delivery_report'] = 'PROCESSED';
            return $this->campaignRepo->updateByUser($campaignParams, $sentCampaign->id, $sentCampaign->user_id );
        }

        return false;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Campaign\ICampaignService::getDeliveryReports()
     */
    public function getDeliveryReports()
    {
        // 1. Get campaign status = SENT
        $sentCampaign = $this->campaignRepo->getSentCampaign();
        if ( $sentCampaign ) {
            // get campaign
            $campaign = $this->getCampaignInfo($sentCampaign->id, $sentCampaign->user_id);
            if(!Carbon::parse('2018-11-02 08:00:00')->gt(Carbon::parse($campaign['send_process_started_on']))) {
                return $this->pushQueueReport(1, $campaign);
            }
            try {
                // 2. Update PROCESSING Status
                $sentCampaign = $this->campaignRepo->updateByUser([
                        'tracking_delivery_report' => 'PROCESSING',
                        'tracking_delivery_report_update_at' => date('Y-m-d H:i:s')
                ], $sentCampaign->id, $sentCampaign->user_id );
                // 3. Call queue service to tracking delivery status
                $status = $this->queueService->trackingDeliveryReports( $sentCampaign );
                // 4. Update campaign tracking_delivery_report
                $campaignParams = [ 'tracking_delivery_report_update_at' => date('Y-m-d H:i:s') ];
                if ( $status['PENDING'] == 0 ) {
                    $campaignParams['tracking_delivery_report'] = 'PROCESSED';
                } else {
                    $campaignParams['tracking_delivery_report'] = 'PENDING';
                }
                return $this->campaignRepo->updateByUser($campaignParams, $sentCampaign->id, $sentCampaign->user_id );
            } catch (\Exception $e) {
                $this->campaignRepo->updateByUser([
                        'tracking_delivery_report' => 'PENDING',
                        'tracking_delivery_report_update_at' => date('Y-m-d H:i:s')
                ], $sentCampaign->id, $sentCampaign->user_id );
                try {
                    \CustomLog::error ( "Tracking Delivery Status campaign #{$sentCampaign->id} of user #{$sentCampaign->user_id} ERROR", 'TrackingDeliveryStatusSMS-' . date ( 'Y-m-d' ), ['error' => $e->getMessage()] );
                }catch(\Exception $e) {
                    //
                }
                
                return $e->getMessage();
            }
        }
        return false;
    }

    /**
     * FN estimate campaign summary
     * {@inheritDoc}
     * @see \App\Services\Campaign\ICampaignService::summarySubscribers()
     */
    public function summarySubscribers($request) {
        $user = Auth::user();
        $listId = $request->get("list_id");
        $subscriberLists = $this->subscriberListRepo->getListSubscribers($listId, $user->id);
        $subscrber_list_name = "";
        $totalSMS = $request->get("total_sms", 1);
        $defaultPriceSMS = $user->default_price_sms;
        $currency = $this->getCurrency($user->currency);
        $balance = $user->credits - $user->credits_usage;
        $billing_type = $user->billing_type;

        foreach($subscriberLists->toArray() as $item) {
            $subscrber_list_name .= $subscrber_list_name != "" ? ", " . $item['name'] : $item['name'];
        }
        $result = $this->campaignRepo->summarySubscribers($listId, $user->id, $totalSMS, $defaultPriceSMS);
        if(count($result->data) > 0 && count($result->total) > 0) {
            $total = $result->total;
            $total->total_subscriber = $total->TotalSubscriber;
            unset($total->TotalSubscriber);
            $total->total_duplicate = $total->TotalDuplicate != "" ? $total->TotalDuplicate : 0;
            unset($total->TotalDuplicate);
            $total->total_price = $total->TotalPrice;
            unset($total->TotalPrice);
            $total->subscrber_list_name = $subscrber_list_name;
            $total->total_sms = $total->total_subscriber * $totalSMS;
            $total->currency = $currency->code;
            $total->data = $result->data;
            $total->balance = $balance;
            $total->billing_type = $billing_type;

            foreach($total->data as $item) {
                $country = $item->country == "Unknown" ? $item->country : $this->getCountry(strtoupper($item->country));
                $country = count($country) > 1 ? "Unknown" : $country;
                $item->country = $country;
            }
            return $this->success($total);
        }

        return $this->fail([
            "message" => trans('campaign.no_subscriber')
        ]);
    }

    /**
     * FN clone campaign
     * {@inheritDoc}
     * @see \App\Services\Campaign\ICampaignService::cloneCampaign()
     */
    public function cloneCampaign($request) {
        $userID = Auth::user()->id;
        $campaign_id = $request->get('id');
        $campaignLinks = $this->campaignLinksService->findCampaignLinkWithCampaign($campaign_id, $userID)->toArray();
        $result = $this->campaignRepo->cloneCampaign($campaign_id, $userID);

        if($result == true) {
            $data = [];
            if(count($campaignLinks) > 0) {
                $data = $campaignLinks;
            }
            return $this->success($data);
        }

        return $this->fail();
    }

    /**
     * FN total send campaign of user
     * {@inheritDoc}
     * @see \App\Services\Campaign\ICampaignService::totalSendCampaignOfUsers()
     */
    public function totalSendCampaignOfUsers($request) {
        $user = Auth::user();
        $users = [];
        $userIDs = "";
        $currency = $user->type == 'GROUP4' ? $user->parentUser->currency: $user->currency;

        if(!!$user->isGroup3()) {
            array_push($users, $user);
            $users = collect($users);
        }elseif(!!$user->isGroup2()) {
            $users = $this->authService->getAllUserGroup3($user->id, false);
        }elseif(!!$user->isGroup4()) {
            $users = $this->authService->getAllUserGroup3($user->parent_id, false);
        } else {
            $users = $this->authService->getAllUserGroup3(null, false);
        }
        $users = $users->pluck('id');
        $userIDs = implode(",", $users->all());

        $year = $request->get('year', '');
        $month = $request->get('month', '');
        $date = $request->get('date', '');
        $filter = $request->get('filter', '');
        $timezone = $request->get('timezone', 'UTC');

        $dateFilter = Carbon::parse($year . "-" . $month . "-" .$date);
        $startDate = $dateFilter;
        $endDate = $dateFilter;
        $data = [];
        switch($filter) {
            case 'month':
                $startDate = $startDate->startOfYear()->startOfDay()->toDateTimeString();
                $endDate = $endDate->endOfYear()->endOfDay()->toDateTimeString();
                $result = $this->campaignRepo->totalSendCampaignOfUsers($userIDs, $startDate, $endDate, $filter, $timezone, $user->type, $currency);
                $result = count($result) > 0 ? $result[0]->total_message != null ? $result : [] : [];
                // data usage
                $dataSum = clone (object)$result;
                $dataSum = collect($dataSum)->groupBy('currency');
                // data chart
                $result = collect($result)->groupBy('keyValue');
                $data = $this->formatDataChartDashboard($dataSum, $result, 1, 12, $filter);
                $data['dateFilter'] = $dateFilter->year == Carbon::now()->year ? 'this year (' . $dateFilter->year . ')' : 'in ' . $dateFilter->year;
                break;
            case 'day':
                $startDate = $startDate->startOfMonth()->startOfDay()->toDateTimeString();
                $endDate = $endDate->endOfMonth()->endOfDay()->toDateTimeString();
                $result = $this->campaignRepo->totalSendCampaignOfUsers($userIDs, $startDate, $endDate, $filter, $timezone, $user->type, $currency);
                $result = count($result) > 0 ? $result[0]->total_message != null ? $result : [] : [];
                // data usage
                $dataSum = clone (object)$result;
                $dataSum = collect($dataSum)->groupBy('currency');
                $totalDate = $dateFilter->endOfMonth()->day;
                // data chart
                $result = collect($result)->groupBy('keyValue');
                $data = $this->formatDataChartDashboard($dataSum, $result, 1, $totalDate, $filter, $year, $month);
                $data['dateFilter'] = $dateFilter->format('Y m') == Carbon::now()->format('Y m') ? 'this month (' . $dateFilter->format('M, Y') . ')' : 'in ' . Carbon::parse($startDate)->format('M, Y');
                break;
            default:
                $startDate = $startDate->startOfDay()->toDateTimeString();
                $endDate = $endDate->endOfDay()->toDateTimeString();
                $result = $this->campaignRepo->totalSendCampaignOfUsers($userIDs, $startDate, $endDate, $filter, $timezone, $user->type, $currency);
                $result = count($result) > 0 ? $result[0]->total_message != null ? $result : [] : [];
                // data usage
                $dataSum = clone (object)$result;
                $dataSum = collect($dataSum)->groupBy('currency');
                // data chart
                $result = collect($result)->groupBy('keyValue');
                $data = $this->formatDataChartDashboard($dataSum, $result, 0, 23, 'hour');
                $data['dateFilter'] = $dateFilter->format('Y m d') == Carbon::now()->format('Y m d') ? 'today' : 'on '  . Carbon::parse($startDate)->format('l jS M, Y');
                break;
        }
        return $data;
    }

    /**
     * get all campaign pending report
     * @param int $userID
     * @return array
     */
    public function getAllCampaignPendingReport($userID) {
        return $this->campaignRepo->getAllCampaignPendingReport($userID)->toArray();
    }

    /**
     * FN update statistic repotr of campaign
     * @param int $userID
     * @param int $campaignID
     * @param array $attributes
     * @return array
     */
    public function updateStatisticReportCampaign($userID, $campaignID, $attributes) {
        return $this->campaignRepo->updateStatisticReportCampagin($userID, $campaignID, $attributes);
    }

    /**
     * fn get campaign with subscriber list
     * {@inheritDoc}
     * @see \App\Services\Campaign\ICampaignService::getCampaignWithSubscriberList()
     */
    public function getCampaignWithSubscriberList($userID, $campaignID) {
        return $this->campaignRepo->getCampaignWithSubscriberList($userID, $campaignID);
    }

    /**
     * get data campaign of user
     * {@inheritDoc}
     * @see \App\Services\Campaign\ICampaignService::getCampaign()
     */
    public function getCampaign($userID, $campaignID, $userType) {
        return $this->campaignRepo->getCampaign($userID, $campaignID, $userType);
    }

    public function sendSMSTest($request) {
        if (isset($request->sender) && isset($request->message) && isset($request->phone_number)) {
            $sender = $request->sender;
            $message = $request->message;
            $phone_number = $request->phone_number;
            $phone_number_array = explode(';', $phone_number);
            $phone_number_valid = array();
            $phone_number_invalid = array();
            $result_array = array();
            $currentUser = Auth::user();
            $parentUser = $this->authService->getUserInfo($currentUser->parent_id);
            foreach ($phone_number_array as $phone_number_item) {
                $phone_number_item = trim($phone_number_item);
                $phone_number_validation = $this->smsService->checkValidPhoneNumber($phone_number_item);

                if ($phone_number_validation) {
                    $phone_number_valid[] = $phone_number_item;
                    // detect country, network, service_provider
                    $detectPhone = $this->campaignRepo->detectCountryNetworkServiceProviderOfPhone($phone_number_item, $currentUser->id, $currentUser->default_price_sms);
                    $parentPhone = $this->campaignRepo->detectCountryNetworkServiceProviderOfPhone($phone_number_item, $parentUser->id, $parentUser->default_price_sms);
                    $detectPhone = count($detectPhone) >= 1 ? $detectPhone[0] : "";
                    $parentPhone = count($parentPhone) >= 1 ? $parentPhone[0] : "";
                    // $queueModel = new Queue();
                    // $queueModel->phone = $phone_number_item;
                    // $personalize_message = $this->queueService->personalizeHandler($message, [], $queueModel);
                    // total message
                    $messageCount = $this->queueService->calculateMessageCount($message);

                    // total price message
                    $clientPriceTotals = (!empty($detectPhone) ? $detectPhone->price : $currentUser->default_price_sms) * $messageCount;
                    $agencyPriceTotals = (!empty($parentPhone) ? $parentPhone->price : $parentUser->default_price_sms) * $messageCount;

                    $canBeSend = $this->queueService->checkUserCanBeAddOrWithdrawCredit($currentUser, $clientPriceTotals);

                    if(!$canBeSend) {
                        return $this->fail([
                                "status" => false,
                                "message" => Lang::get('notify.can_be_send')
                        ]);
                    }

                    $result = [];
                    if(!empty($detectPhone)) {
                        $smsResults = $this->smsService->sendSMS($sender, $phone_number_item, $personalize_message, $detectPhone->service_provider);
                    }else{
                        $smsResults = $this->smsService->sendSMS($sender, $phone_number_item, $personalize_message);
                    }


                    $result_array[$phone_number_item] = array_shift ( $smsResults );
                    if($result_array[$phone_number_item]) {
                        // Charged credits for group3
                        $this->clientService->chargeCredits( $currentUser->id, $clientPriceTotals );
                        // Charged credits for group2 if group3 billing type is UNLIMITED
                        if ($currentUser->billing_type == 'UNLIMITED') {
                            $this->clientService->chargeCredits( $currentUser->parent_id, $agencyPriceTotals );
                        }
                        // Charged credits for group2
                        //$this->clientService->chargeCredits( $currentUser->parent_id, $agencyPriceTotals );
                    }
                } else {
                    array_push($phone_number_invalid, $phone_number_item);
                }
            }

            if (count($phone_number_valid) <= 0) {
                return $this->fail(["status" => false,
                        "message" => "All phone number is invalid. Please try again."
                ]);
            }

            $pending_array = array();
            $rejected_array = array();
            $expired_array = array();
            foreach ($phone_number_valid as $key => $item) {
                $status = $result_array[$item]['return_status'];
                if ($status == "PENDING") {
                    $pending_array[] = $item;
                } else if ($status == "FAILED") {
                    $rejected_array[] = $item;
                } elseif ($status == "EXPIRED") {
                    $expired_array[] = $item;
                }
                unset($phone_number_valid[$key]);
            }
            return $this->success(["status" => true,
                    "phone_number_sent" => $phone_number_valid,
                    "phone_number_pending" => $pending_array,
                    "phone_number_rejected" => $rejected_array,
                    "phone_number_expired" => $expired_array,
                    "phone_number_invalid" => $phone_number_invalid
            ]);
        } else {
            return $this->fail([
                    "status" => false,
                    "message" => "Sender or message or phone testing cannot be empty."
            ]);
        }
    }

    /**
     * fn format data chart dashboard
     * @param unknown $dataSum
     * @param unknown $data
     * @param unknown $start
     * @param unknown $end
     * @param unknown $type
     * @param unknown $year
     * @param unknown $month
     * @return array[]|number[]
     */
    private function formatDataChartDashboard($dataSum, $data, $start, $end, $type = null, $year = null, $month = null) {
        $result = [];
        $months = config("constants.data_month");
        $colorChart = config ( "constants.color_chart_pdf" );
        $maxValue = 0;
        $dataUsage = [];
        // format data chart
        for($i = $start; $i <= $end; $i++) {
            $flag = false;
            foreach ($data as $key => $item) {
                if($i == $key) {
                    $total_message = $item->sum('total_message');
                    $maxValue = $total_message > $maxValue ? $total_message : $maxValue;
                    $message = 'Total Price: ';
                    $item = $item->groupBy('currency');
                    $dataPrice = [];
                    foreach ($item as $value) {
                        array_push($dataPrice, [
                                'total_price' => number_format($value->sum('total_price'), 2),
                                'currency' => $value[0]->currency
                        ]);
                    }
                    array_push($result, [
                            'key' => $type == 'month' ? $months[$i-1] : ($type == 'day' ? (string) Carbon::parse($year . '-' . $month . '-' . $i)->format('M d, Y') : (string)$i),
                            'value' => intval($total_message),
                            'data' => $dataPrice
                    ]);
                    //(string) Carbon::parse($year . '-' . $month . '-' . $i)->format('M d, Y')
                    $flag= true;
                    break;
                }
            }

            if(!$flag) {
                array_push($result, [
                        'key' => $type == 'month' ? $months[$i-1] : ($type == 'day' ? (string) Carbon::parse($year . '-' . $month . '-' . $i)->format('M d, Y') : (string)$i),
                        'value' => 0,
                        'data' => []
                ]);
            }
        }

        // format data usage
        $dataSum->each(function($item, $key) use (&$dataUsage) {
            array_push($dataUsage, [
                    'currency' => $key,
                    'total_price' => number_format($item->sum('total_price'), 2)
            ]);
        });

            return [
                    "data" => $result,
                    "maxValue" => intval($maxValue),
                    "dataUsage" => $dataUsage
            ];
    }

    public function sendAgainCampaignPause() {
        try {
            // get first campaign with status pending
            $campaignPaused = $this->campaignPausedRepo->scopeQuery(function($query) {
                return $query->orderBy('tracking_updated_at','asc');
            })->findWhere([
                'tracking_status' => 'PENDING',
                ['tracking_updated_at', '<=', Carbon::now()->toDateTimeString()]
            ])->first();

            if(!empty($campaignPaused)) {
                // update campaign paused
                $this->campaignPausedRepo->update([
                        "tracking_status" => "PROCESSING",
                        "tracking_updated_at" => Carbon::now()->addMinute(2)->toDateTimeString()
                ], $campaignPaused->id);

                // get camapgin
                $campaign = $this->getCampaignInfo($campaignPaused->campaign_id, $campaignPaused->user_id);
                $result = $this->sendCampaigns((object)$campaign->toArray());

                if(!in_array($result->status, ["SENT", "FAILED"])) {
                    $this->campaignPausedRepo->update([
                            "tracking_status" => "PENDING",
                            "tracking_updated_at" => Carbon::now()->addMinute(2)->toDateTimeString()
                    ], $campaignPaused->id);
                }
                return 1;
            }

            return 0;
        }catch (\Exception $e) {
            return $e->getMessage();
        }

    }

    public function addLinkUnsubscriber($campaignID) {
        try {
            $userID = Auth::user()->id;
            $linkUnsubscriber = url('/') . "/unsubscribe?u=" . base64_encode($userID . $this->keyBase64()) . "&c=" . base64_encode($campaignID . $this->keyBase64());
            $data = (object) [
                "long_url" =>  $linkUnsubscriber,
                "campaign_id" => 0,
                "campaign_link_id" => 0,
                "user_id" => 0
            ];
            $result = $this->shortLinkService->shortLinkDCT($data);

            if(!$result->status) {
                return false;
            }

            return $result->data['short_link'];
        }catch (\Exception $e) {
            return false;
        }
    }

    /**
     * fn remove short link in message
     */
    public function removeCampaignLink($message, $listCampaignLinkId) {
        try {
            preg_match_all('/%%https:\/\/t1.sg_(.+?)%%/', $message, $data);
            $listID = [];
            $listRemove = [];
            foreach($listCampaignLinkId as $link) {
                $flag = false;
                foreach($data[1] as $item) {
                    if($item == $link) {
                        array_push($listID, $item);
                        $flag = true;
                        break;
                    }
                }

                if(!$flag) {
                    array_push($listRemove, $link);
                }
            }

            if(count($listRemove) > 0) {
                // remove campaign link
                $this->campaignLinksService->deleteListCampaignLink($listRemove);
            }

            return $listID;
        }catch(\Exception $e) {
            return $e->getMessage();
        }
    }


    /**
     * fn get first campaign
     */
    public function getFirstCampaign() {
        try {
            $result = $this->campaignRepo->getFirstCampaign();
            if(empty($result)) {
                return $this->fail();
            }

            return $this->success($result);
        }catch(\Exception $e) {
            return $this->fail();
        }
    }

    /**
     * fn create campaign of account api
     */
    public function createCampaignApiAccount($id) {
        $request ['user_id'] = $id;
        $request ['name'] = 'Test Campaign Api Account';
        $request ['status'] = 'SENT';
        $request ['is_api'] = 1;
        $request ['sender'] = 'Verify';
        $request ['send_time'] = Carbon::now()->toDateTimeString();
        $request ['created_by'] = $id;
        $request ['message'] = 'Test Message';
        $request ['schedule_type'] = 'FUTURE';

        $campaign = $this->campaignRepo->createCampaignApiAccount ($request);
        //
        if ($campaign) {
            $this->queueRepo->generateQueueTable ( $id, $campaign->id, [] , 0 );
            return true;
        } else {
            return false;
        }
    }
}
