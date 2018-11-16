<?php

namespace App\Services\Campaign;

use App\Services\BaseService;
use App\Repositories\Campaign\IQueueRepository;
use App\Repositories\Auth\IAuthenticationRepository;
use App\Services\SMS\ISMSService;
use App\Services\Auth\IAuthenticationService;
use App\Entities\SMSReportResponse;
use App\Services\Subscribers\ISubscriberService;
use App\Services\Settings\IServiceProviderService;
use App\Services\CustomFields\ICustomFieldService;
use App\Services\SubscriberLists\ISubscriberListService;
use App\Services\Clients\IPriceConfigurationService;
use App\Services\Clients\IClientService;
use App\Services\Settings\IMCCMNCService;
use App\Services\Settings\IPreferredServiceProviderService;
use Illuminate\Support\Facades\Lang;
use App\Services\ShortLinks\IShortLinkService;
use App\Jobs\SendCampaign;
use App\Repositories\Campaign\ICampaignRepository;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Tests\Logger;
use App\Jobs\QueueReport;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use DB;
use function GuzzleHttp\json_encode;

class QueueService extends BaseService implements IQueueService {

    //
    protected $authRepo;
    protected $queueRepo;
    protected $smsService;
    protected $authService;
    protected $subscriberService;
    protected $serviceProviderService;
    protected $customFieldService;
    protected $subscriberListService;
    protected $priceConfigurationService;
    protected $clientService;
    protected $mccmncService;
    protected $preferredServiceProviderService;
    protected $campaignPausedService;
    protected $campaignLinkService;
    protected $shortLinkService;
    protected $campaignRepo;
    protected $campaignRecipientsService;
    protected $campaignService;

    /**
     */
    public function __construct(
            IAuthenticationRepository $IAuthRepo,
            IQueueRepository $IQueueRepo,
            ISMSService $ISMSService,
            IAuthenticationService $IAuthService,
            ISubscriberService $ISubScriberService,
            IServiceProviderService $IServiceProviderService,
            ICustomFieldService $ICustomFieldService,
            ISubscriberListService $ISubscriberListService,
            IPriceConfigurationService $IPriceConfigurationService,
            IClientService $IClientService,
            IMCCMNCService $IMCCMNCService,
            IPreferredServiceProviderService $IPreferredServiceProviderService,
            ICampaignPausedService $campaignPausedService,
            ICampaignLinkService $campaignLinkService,
            IShortLinkService $shortLinkService,
            ICampaignRepository $campaignRepo,
            ICampaignRecipientsService $campaignRecipientsService
    ) {
        $this->authRepo = $IAuthRepo;
        $this->queueRepo = $IQueueRepo;
        $this->smsService = $ISMSService;
        $this->authService = $IAuthService;
        $this->subscriberService = $ISubScriberService;
        $this->serviceProviderService = $IServiceProviderService;
        $this->customFieldService = $ICustomFieldService;
        $this->subscriberListService = $ISubscriberListService;
        $this->priceConfigurationService = $IPriceConfigurationService;
        $this->clientService = $IClientService;
        $this->mccmncService = $IMCCMNCService;
        $this->preferredServiceProviderService = $IPreferredServiceProviderService;
        $this->campaignPausedService = $campaignPausedService;
        $this->campaignLinkService = $campaignLinkService;
        $this->shortLinkService = $shortLinkService;
        $this->campaignRepo = $campaignRepo;
        $this->campaignRecipientsService = $campaignRecipientsService;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \App\Services\Campaign\IQueueService::personalizeHandler()
     */
    public function personalizeHandler($message, $customFields = array(), $queue, $subscriber, $flag_ps = false) {
        //
        $patterns = array ();
        $patterns [] = '/[%][%]phone[%][%]/';
        $patterns [] = !!$flag_ps ? '/[%][%]first_name[%][%]/' : '/[%][%]firstname[%][%]/';
        $patterns [] = !!$flag_ps ? '/[%][%]last_name[%][%]/' : '/[%][%]lastname[%][%]/';
        //
        $replacements = array ();
        $replacements [] = $queue->phone;
        $replacements [] = $subscriber->first_name;
        $replacements [] = $subscriber->last_name;
        //
        foreach ( $customFields as $field ) {
            $patterns [] = "/[%][%]{$field->field_name}[%][%]/";
            $customFieldName = 'custom_field_' . $field->id;
            $replacements [] = $subscriber->$customFieldName;
        }
        // Replace multiple space to single space
        $patterns [] = '/[[:blank:]]+/';
        $replacements [] = ' ';
        //
        $message = preg_replace ( $patterns, $replacements, $message );
        return $message;
    }

    /**
     * fn replace data personalize and short link
     */
    public function replaceShortLink($userID, $message = "", $campaignLinks = [], $customFields = array(), $queue, $subscriber) {
        preg_match_all('/%%https:\/\/t1.sg_(.+?)%%/', $message, $data);

        if(empty($data[1])) {
            Log::error ( 'Error Short Link Replace SendSMS-' . date ( 'Y-m-d' ) . " ");
            return false;
        }

        foreach($data[1] as $link) {
            $campaignLink = $campaignLinks->find($link);
            if(empty($campaignLink)) {
                Log::error ( 'Error Short Link Replace 1 SendSMS-' . date ( 'Y-m-d' ) . " ");
                return false;
            }

            $long_url = $campaignLink->url;
            preg_match('/%%(.+?)%%/', $long_url, $flag);
            if(count($flag) > 0) {
                $long_url = $this->personalizeHandler($long_url, $customFields, $queue, $subscriber);
            }

            // short link
            $dataShortLink = (object)[
                "user_id" => $userID,
                "campaign_id" => $campaignLink->campaign_id,
                "campaign_link_id" => $campaignLink->id,
                "long_url" => $long_url
            ];
            $shortLink = $this->shortLinkService->shortLinkDCT($dataShortLink);

            if(!$shortLink->status) {
                Log::error ( 'Error Short Link Replace 2 SendSMS-' . date ( 'Y-m-d' ) . " ");
                return false;
            }

            $data = $shortLink->data;
            $message = str_replace("%%" . env('DOMAIN_SHORT_LINK') . "_" . $campaignLink->id . "%%", $data['short_link'], $message);
        }

        return $message;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Campaign\IQueueService::checkCreditsHandler($idUser, $priceConfiguration, $queue, $messageCount)
     */
    public function checkCreditsHandler( $user, $priceConfiguration, $queue ) {
        if(count($priceConfiguration) > 0) {
            // Price from configuration
            $price = null;
            if ( $queue->country ) {
                $network = $this->removeNonAlphanumericCharacters($queue->network);
                if ( isset($priceConfiguration[$queue->country]) ) {
                    if ( isset($priceConfiguration[$queue->country]['disabled']) && $priceConfiguration[$queue->country]['disabled'] == 1) {
                        return false;
                    } elseif ( isset($priceConfiguration[$queue->country][$network]) ) {
                        $price = $priceConfiguration[$queue->country][$network]['price'];
                    } elseif ( isset($priceConfiguration[$queue->country]['price']) && $priceConfiguration[$queue->country]['price'] > 0) {
                        $price = $priceConfiguration[$queue->country]['price'];
                    } else {
                        $price = $user->getDefaultPrice();
                    }
                } else {
                    $price = false;
                }
            } else {
                $price = $user->getDefaultPrice();
            }

            //
            return $price;
        }

        return false;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Campaign\IQueueService::calculateMessageCount()
     */
    public function calculateMessageCount( $message ) {
        // change prev line "\r\n" to "\n"
        $message = str_replace("\r\n", "\n", $message);
        $strLength = mb_strlen($message);
        //
        $mbDetectEncoding = mb_detect_encoding($message);
        $messageType = "ASCII";
        if ($mbDetectEncoding != 'ASCII') {
            $messageType = 'UNICODE';
        }
        //
        if ( $messageType == 'UNICODE' ) {
            $characterNumber = 70;
            //
            if ($strLength > $characterNumber) {
                $characterNumber = 67;
            }
        } else {
            $characterNumber = 160;
            //
            if ($strLength > $characterNumber) {
                $characterNumber = 153;
            }
        }

        return ceil( mb_strlen($message) / $characterNumber);
    }

    /**
     * create queue job send campaign
     * @param int $campaign
     * @param int $position
     * @param int $processAmount
     */
    public function pushQueueSendSMS($campaign, $position, $processAmount = 60) {
        try{
            // Get current user
            $currentUser = $this->authService->getUserInfo ( $campaign->user_id );

            // 1. Check User Credits
            if ( $currentUser->canSendCampaign() ) {
                // 2. Generate queue table + prevent subscribers which in global suppression list.
                if(!$this->queueRepo->checkExistsTableQueue($currentUser->id, $campaign->id)) {
                    $globalSuppressionList = $this->subscriberListService->getGlobalSuppressionList( $currentUser->id, ['id']);
                    $this->queueRepo->generateQueueTable ( $currentUser->id, $campaign->id, $campaign->recipients, $globalSuppressionList->id );
                }

                // create queue job
                $result = $this->getQueueSendCampaign($position, $currentUser, $campaign, $processAmount);
                return (object) [
                    'status' => true,
                    'flag' => $result->flag,
                    'position' => $result->position
                ];
            }else {
                return (object) [
                    "status" => false,
                    "flag" => "",
                    "position" => $position + 1
                ];
            }
        }catch(\Exception $e) {
            return (object) [
                "status" => false,
                "flag" => "",
                "position" => $position + 1
            ];
        }
    }

    /**
     * fn get queue and create queue job
     * @param int $position
     * @param object $user
     * @param object $campaign
     * @param int $limit
     */
    public function getQueueSendCampaign($position, $user, $campaign, $limit) {
        // --- Get queue by $processAmount limit and Update status to SENDING
        $queues = $this->queueRepo->getPendingQueues ( $user->id, $campaign->id, $limit);
        // increase $position
        $position += 1;
        // check empty $queues
        if(!$queues || count($queues) == 0) {
            return (object) [
                "status" => true,
                "flag" => 'sent',
                "position" => $position
            ];
        }

        // push queue job
        $queueIDs = collect($queues)->implode('id', ',');
        SendCampaign::dispatch($user->id, $campaign->id, $queueIDs);

        // check limit $position
        if($position > config("constants.limit_queue")) {
            return (object) [
                "status" => true,
                "flag" => 'sending',
                "position" => $position
            ];
        } else if(count($queues) < $limit) {
            // check count $queues < $limit
            return (object) [
                "status" => true,
                "flag" => 'sent',
                "position" => $position
            ];
        }
        
        // callback agaign function
        return $this->getQueueSendCampaign($position, $user, $campaign, $limit);
    }

    /**
     * fn queue jon run send sms
     * @param int $userID
     * @param int $campaignID
     * @param string $queueIDs
     * @param int $times
     */
    public function queuSendSMS($userID, $campaignID, $queueIDs, $times = 0) {

        // Log::error('Start Send ' . date("m-d-y H:i:s",explode(" ",microtime())[1]).substr((string)explode(" ",microtime())[0],1,4));
        // get user
        $currentUser = $this->authService->getUserInfo ( $userID );
        // get campaign
        $campaign = $this->campaignRepo->find( $campaignID , $userID);
        // get list queue
        $queues = $this->queueRepo->getQueueByIDs($userID, $campaignID, explode(',', $queueIDs));
        $queueAgain = [];

        if(!empty($campaign) && !empty($queues) && count($queues) > 0) {
            // Get Client price
            $clientPriceConfiguration = $this->priceConfigurationService->fetchAllGroupByCountry($currentUser->id);
            $clientPriceConfiguration = collect($clientPriceConfiguration)->where('disabled', '0')->all();

            // Get Agency price
            $agencyPriceConfiguration = array();
            if ( $currentUser->parent_id ){
                $agencyPriceConfiguration = $this->priceConfigurationService->fetchAllGroupByCountry($currentUser->parent_id);
                $agencyPriceConfiguration = collect($agencyPriceConfiguration)->where('disabled', '0')->all();
            }

            // Add recipients to campaign
            $recipients = $this->campaignRecipientsService->getRecipientsIDByCampaignId( $campaign->id, $userID );
            $campaign->recipients = $recipients;

            // Get all custom fields
            $strListId = implode ( ',', array_values ( $campaign->recipients ) );
            $customFields = $this->customFieldService->getCustomFieldOfSubscriberByColumn ( $strListId, [
                    'id',
                    'list_id',
                    'field_name'
            ], $currentUser->id );

            // Get short links
            $campaignLinks = $this->campaignLinkService->findCampaignLinkWithCampaign($campaignID, $userID);

            // get default service provider
            $serviceProviderDefault = $this->serviceProviderService->getDefaultServiceProvider ();

            // --- Loop
            $disableCountries = [];
            foreach ( $queues as $queue ) {
                // Log::error('Start loop ' . date("m-d-y H:i:s",explode(" ",microtime())[1]).substr((string)explode(" ",microtime())[0],1,4));
                // Check valid phone number
                if ( !$this->smsService->checkValidPhoneNumber($queue->phone)) {
                    $this->queueRepo->updateFailed($currentUser->id, $campaign->id, $queue->id, 'Phone number is invalid.');
                    continue;
                }

                // Update FAILED status to queue when country was disabled.
                if ( in_array($queue->country, $disableCountries) ) {
                    $this->queueRepo->updateFailed($currentUser->id, $campaign->id, $queue->id, 'The country was disabled.');
                    continue;
                }

                // If do not have service_provider then replace by the default service provider
                if (is_null ( $queue->service_provider )) {
                    $queue->service_provider = $serviceProviderDefault->code;
                }

                $subscriber = $this->subscriberService->getSubscriberInfo ( $queue->list_id, $queue->subscriber_id );
                
                // 5. Campaign template - Replace personalize
                $templateSMS = $this->personalizeHandler ( $campaign->message, $customFields, $queue, $subscriber);
                $messageCount = $this->calculateMessageCount($templateSMS);

                // 6. Short Link - Replace Personalize
                if($campaignLinks->count() > 0) {
                    $messageLink = $this->replaceShortLink($campaign->user_id, $templateSMS, $campaignLinks, $customFields, $queue, $subscriber);
                    // dont replace shortlink
                    if($messageLink == false) {
                        array_push($queueAgain, $queue->id);
                        if($times >= config("constants.linit_again_queue")){
                            $this->queueRepo->updateFailed($currentUser->id, $campaign->id, $queue->id, "Failed to get shortlink from t1.sg.");
                        }
                        continue;
                    }
                    $templateSMS = $messageLink;
                    $messageCount = $this->calculateMessageCount($templateSMS);
                }
                

                // Check Price Message
                $clientPrice = $this->checkCreditsHandler( $currentUser, $clientPriceConfiguration, $queue );
                $agencyPrice = $this->checkCreditsHandler($currentUser->parent, $agencyPriceConfiguration, $queue);

                // If false then not send to this country
                if ( $clientPrice === false) {
                    $disableCountries[] = $queue->country;
                    $this->queueRepo->updateFailed($currentUser->id, $campaign->id, $queue->id, 'The country was disabled.');
                    continue;
                }

                // Totals price
                $clientPriceTotals = $clientPrice * $messageCount;
                $agencyPriceTotals = $agencyPrice * $messageCount;
                //
                $canBeSend = $this->checkUserCanBeAddOrWithdrawCredit($currentUser, $clientPriceTotals);
                if ( !$canBeSend ) { // If true then account out of money.
                    // Update FAILED status for remaining queue.
                    $results = $this->queueRepo->updateAllFailed($currentUser->id, $campaign->id, 'Account is out of money.');
                    if ( $results ) {
                        // 6. Count totals
                        $resultCount = $this->queueRepo->getTotalsByStatus ( $currentUser->id, $campaign->id );
                        return (object) [
                                "status" => "failed",
                                "result" => $resultCount
                        ];
                    }
                }
                try {
                    $queueParams ['id'] = $queue->id;
                    $queueParams ['service_provider'] = $queue->service_provider;
                    $queueParams ['message'] = $templateSMS;
                    $queueParams ['message_count'] = $messageCount;
                    $queueParams ['sum_price_agency'] = $agencyPriceTotals;
                    $queueParams ['sum_price_client'] = $clientPriceTotals;
                    $queueParams ['campaign']   = $campaign;
                    $queueParams ['user']   = $currentUser;

                    // send SMS
                    $this->smsService->sendSMSAsync( $campaign->sender, $queue->phone, $templateSMS, $queue->service_provider, $campaign->valid_period, (object) $queueParams);

                    // 7. Charged credits for group3
                    $this->clientService->chargeCredits($currentUser->id, $clientPriceTotals);
                    // Charged credits for group2 if group3 billing type is UNLIMITED
                    if ($currentUser->billing_type == 'UNLIMITED') {
                        $this->clientService->chargeCredits($currentUser->parent_id, $agencyPriceTotals);
                    }
                } catch (\Exception $e) {
                    $this->queueRepo->updateFailed($currentUser->id, $campaign->id, $queue->id, $e->getMessage());
                }
                // Log::error('End loop ' . date("m-d-y H:i:s",explode(" ",microtime())[1]).substr((string)explode(" ",microtime())[0],1,4));
            }

            $flagShortLink = false;
            if(count($queueAgain) > 0) {
                if($times >= config("constants.linit_again_queue")) {
                    $flagShortLink = implode(',', $queueAgain);
                    return (object)[
                        "status" => 'ready',
                        "campaign" => $campaign,
                        "flagShortLink" => $flagShortLink
                    ];
                }else {
                    $times += 1;
                    // push new job of queue again
                    SendCampaign::dispatch($userID, $campaignID, implode(',', $queueAgain), $times);
                }
            }
            // Log::error('Finished send ' . date("m-d-y H:i:s",explode(" ",microtime())[1]).substr((string)explode(" ",microtime())[0],1,4));
            return null;
        }else {
            return null;
        }
    }

    /**
     * FN PROCESS SENT SMS
     *
     * {@inheritdoc}
     *
     * @see \App\Services\Campaign\IQueueService::sendSMS()
     */
    public function sendSMS($campaign, $processAmount = 60) {
        // Get current user
        $currentUser = $this->authService->getUserInfo ( $campaign->user_id );
        // Get Client price
        $clientPriceConfiguration = $this->priceConfigurationService->fetchAllGroupByCountry($currentUser->id);
        $clientPriceConfiguration = collect($clientPriceConfiguration)->where('disabled', '0')->all();
        // Get Agency price
        $agencyPriceConfiguration = array();
        if ( $currentUser->parent_id ){
            $agencyPriceConfiguration = $this->priceConfigurationService->fetchAllGroupByCountry($currentUser->parent_id);
            $agencyPriceConfiguration = collect($agencyPriceConfiguration)->where('disabled', '0')->all();
        }

        // 1. Check User Credits
        if ( $currentUser->canSendCampaign() ) {

            // Get all custom fields
            $strListId = implode ( ',', array_values ( $campaign->recipients ) );
            $customFields = $this->customFieldService->getCustomFieldOfSubscriberByColumn ( $strListId, [
                    'id',
                    'list_id',
                    'field_name'
            ], $currentUser->id );

            // Get short links
            $campaignLinks = $this->campaignLinkService->findCampaignLinkWithCampaign($campaign->id, $currentUser->id);

            // 2. Generate queue table + prevent subscribers which in global suppression list.
            $globalSuppressionList = $this->subscriberListService->getGlobalSuppressionList( $currentUser->id, ['id']);
            $this->queueRepo->generateQueueTable ( $currentUser->id, $campaign->id, $campaign->recipients, $globalSuppressionList->id );

            // 3. Caculate loop by $processAmount
            /*$totalsQueue = $this->queueRepo->getPendingTotals ( $currentUser->id, $campaign->id );
            $totalsLoop = ceil ( $totalsQueue / $processAmount );

            // 4. Loop send SMS
            for($i = 1; $i <= $totalsLoop; $i++) {*/
                // --- Get queue by $processAmount limit and Update status to SENDING
                $queues = $this->queueRepo->getPendingQueues ( $currentUser->id, $campaign->id, $processAmount );

                // --- Loop
                $disableCountries = [];
                foreach ( $queues as $queue ) {
                    // Check valid phone number
                    if ( !$this->smsService->checkValidPhoneNumber($queue->phone)) {
                        $this->queueRepo->updateFailed($currentUser->id, $campaign->id, $queue->id, 'Phone number is invalid.');
                        continue;
                    }

                    // Update FAILED status to queue when country was disabled.
                    if ( in_array($queue->country, $disableCountries) ) {
                        $this->queueRepo->updateFailed($currentUser->id, $campaign->id, $queue->id, 'The country was disabled.');
                        continue;
                    }

                    // If do not have service_provider then replace by the default service provider
                    if (is_null ( $queue->service_provider )) {
                        $serviceProviderDefault = $this->serviceProviderService->getDefaultServiceProvider ();
                        $queue->service_provider = $serviceProviderDefault->code;
                    }
                    
                    // 5. Campaign template - Replace personalize
                    $templateSMS = $this->personalizeHandler ( $campaign->message, $customFields, $queue );
                    $messageCount = $this->calculateMessageCount($templateSMS);

                    // 6. Short Link - Replace Personalize
                    if($campaignLinks->count() > 0) {
                        $templateSMS = $this->replaceShortLink($campaign->user_id, $templateSMS, $campaignLinks, $customFields, $queue);
                        $messageCount = $this->calculateMessageCount($templateSMS);
                    }
                    

                    // Check Price Message
                    $clientPrice = $this->checkCreditsHandler( $currentUser, $clientPriceConfiguration, $queue );
                    $agencyPrice = $this->checkCreditsHandler($currentUser->parent, $agencyPriceConfiguration, $queue);

                    // If false then not send to this country
                    if ( $clientPrice === false) {
                        $disableCountries[] = $queue->country;
                        $this->queueRepo->updateFailed($currentUser->id, $campaign->id, $queue->id, 'The country was disabled.');
                        continue;
                    }

                    // Totals price
                    $clientPriceTotals = $clientPrice * $messageCount;
                    $agencyPriceTotals = $agencyPrice * $messageCount;
                    //
                    $canBeSend = $this->checkUserCanBeAddOrWithdrawCredit($currentUser, $clientPriceTotals);
                    if ( !$canBeSend ) { // If true then account out of money.
                        // Update FAILED status for remaining queue.
                        $results = $this->queueRepo->updateAllFailed($currentUser->id, $campaign->id, 'Account is out of money.');
                        if ( $results ) {
                            // 6. Count totals
                            $resultCount = $this->queueRepo->getTotalsByStatus ( $currentUser->id, $campaign->id );
                            return (object) [
                                    "status" => "failed",
                                    "result" => $resultCount
                            ];
                        }
                    }
                    try {
                        // Send SMS
                        $smsResults = $this->smsService->sendSMS ( $campaign->sender, $queue->phone, $templateSMS, $queue->service_provider, $campaign->valid_period );

                        // --- Update queue
                        $queueParams = array_shift ( $smsResults );
                        // check send sms into service provider
                        if ($queueParams) {
                            $queueParams ['status'] = 'SENT';
                            $queueParams ['report_updated_at'] = date('Y-m-d H:i:s');
                            $queueParams ['service_provider'] = $queue->service_provider;
                            $queueParams ['message'] = $templateSMS;
                            $queueParams ['message_count'] = $messageCount;
                            $queueParams ['sum_price_agency'] = $agencyPriceTotals;
                            $queueParams ['sum_price_client'] = $clientPriceTotals;
                            $this->queueRepo->updateQueue ( $queueParams, $queue->id, $currentUser->id, $campaign->id );

                            // 7. Charged credits for group3
                            $this->clientService->chargeCredits( $currentUser->id, $clientPriceTotals );
                            // Charged credits for group2 if group3 billing type is UNLIMITED
                            if ($currentUser->billing_type == 'UNLIMITED') {
                                $this->clientService->chargeCredits( $currentUser->parent_id, $agencyPriceTotals );
                            }
                            //$this->clientService->chargeCredits( $currentUser->parent_id, $agencyPriceTotals );
                        } else {
                            // dont connec to service provider
                            // create or update campaign paused
                            $attributes = [
                                    "user_id" => $currentUser->id,
                                    "campaign_id" => $campaign->id,
                                    "queue_id" => $queue->id,
                            ];
                            $result = $this->campaignPausedService->updateOrCreate($attributes);
                            if(!empty($result)) {
                                $result = (object) $result->toArray();
                                if($result->count >= 3) {
                                    $msg = Lang::get('notify.error_detected');
                                    $this->queueRepo->updateAllFailed($currentUser->id, $campaign->id, 'Can not send SMS to Service Provider.');
                                    $this->campaignPausedService->removeCampaignPaused($result->id);
                                }else {
                                    $this->queueRepo->updatePendingAllQueue($currentUser->id, $campaign->id);
                                }
                                return (object) [
                                        "status" => "paused",
                                        "result" => $result
                                ];
                            }
                        }
                    } catch (\Exception $e) {
                        $this->queueRepo->updateFailed($currentUser->id, $campaign->id, $queue->id, $e->getMessage());
                    }
                // }
            }

            // 6. Count totals
            $resultCount = $this->queueRepo->getTotalsByStatus ( $currentUser->id, $campaign->id );

            // get total queue
            $totalsQueue = $this->queueRepo->getPendingTotals ( $currentUser->id, $campaign->id );

            CampaignService::queueSentCampaign((object) [
                "status" => $totalsQueue == 0 ? "success" : 'ready',
                "result" => $resultCount
            ]);
            // return (object) [
            //     "status" => $totalsQueue == 0 ? "success" : 'ready',
            //     "result" => $resultCount
            // ];
        } else {
            return (object) [
                    "status" => "error",
                    "result" => ""
            ];
        }

        return (object) [
                "status" => "error",
                "result" => ""
        ];
    }

    /**
     * fn push queue report
     * @param string $userID
     * @param string $campaignID
     * @param int $position
     * @return object
     */
    public function pushQueueDeliveryReport($userID, $campaignID, $position, $processAmount = 60) {
        $queues = $this->queueRepo->getPendingReportQueue( $userID, $campaignID, $processAmount);
        $position += 1;
        if(empty($queues) && count($queues) == 0) {
            return (object) [
                'status' => true,
                'report_status' => 'processed',
                'position'  => $position
            ];
        }

        $queueIDs = collect($queues)->implode('id', ',');
        QueueReport::dispatch($userID, $campaignID, $queueIDs)->onQueue('report');

        if($position > config("constants.limit_queue_report")) {
            return (object) [
                "status" => true,
                "report_status" => "processing",
                "position"  => $position
            ];
        }else if(count($queues) < $processAmount) {
            return (object) [
                'status' => true,
                'report_status' => 'processed',
                "position"  => $position
            ];
        }
        // $data = (object) [
        //     'results' => [
        //         (object) [
        //             'query' => $position,
        //             'listid' => $queueIDs
        //         ]
        //     ]
        // ];

        // \App\Facades\CustomLog::info ( 'Tracking Delivery Status SMS Via MESSAGEBIRD', 'TrackingDeliveryStatusSMS-' . date ( 'Y-m-d' ), json_decode(json_encode($data), true) );
        
        return $this->pushQueueDeliveryReport($userID, $campaignID,  $position, $processAmount);
    }

    /**
     * fn get delivery report of service provider
     * @param string $userID
     * @param string $campaignID
     * @param string $queueIDs
     * @return object
     */
    public function runQueueDeliveryReport($userID, $campaignID, $queueIDs) {
        try {
            
            // get queue by list id queue
            $queues = $this->queueRepo->getQueuePendingReport ( $userID, $campaignID, explode(',', $queueIDs));

            if(count($queues) > 0) {
                
                $campaign = $this->campaignRepo->find( $campaignID , $userID);
                // All MCCMNC
                $allMCCMNC = $this->mccmncService->fetchAllOptions();
                // All Preferred service provider
                $allPreferredServiceProvider = $this->preferredServiceProviderService->fetchAllPreferredGroupByCountryNetwork();

                // string query
                $query = "INSERT INTO queue_u_". $userID . "_c_" . $campaignID . " (id, list_id, subscriber_id, phone, return_sms_count, return_mccmnc, country, network, ported, return_price, return_currency, report_status, return_status, return_status_message, return_json, report_updated_at, updated_at) VALUES ";
                $arrReportID = [];
                $queryValue = "";
                $arrAgainReport = [];

                // --- Loop
                foreach ( $queues as $key => $queue ) {
                    
                    if ($queue->service_provider) {
                        $smsStatus = $this->smsService->getSMSInfoNew ( $queue->return_message_id, $queue->service_provider );
                        if ($smsStatus instanceof SMSReportResponse) {

                            $queryValue .= strlen($queryValue) > 0 ? ", " : "";

                            // 3. Update to queue
                            $queueParams = array (
                                "id" => $queue->id,
                                "list_id" => $queue->list_id,
                                "subscriber_id" => $queue->subscriber_id,
                                "phone" => $queue->phone,
                                "return_sms_count" => $queue->return_sms_count,
                                "return_mccmnc" => $queue->return_mccmnc,
                                "country"   => $queue->country,
                                "network"   => $queue->network,
                                "ported"    => $queue->ported,
                                "return_price" => $queue->return_price,
                                "return_currency" => $queue->return_currency,
                                "report_status" => "REPORTED",
                                "return_status" => $queue->return_status,
                                "return_status_message" => $queue->return_status_message,
                                "return_json" => $queue->return_json,
                                "report_updated_at" => date('Y-m-d H:i:s'),
                                "updated_at" => $queue->updated_at
                            );

                            if ($smsStatus->getSmsCount () > $queue->return_sms_count) {
                                $queueParams ['return_sms_count'] = $smsStatus->getSmsCount ();
                            }
                            if ($smsStatus->getMccMnc ()) {
                                $queueParams ['return_mccmnc'] = $smsStatus->getMccMnc ();
                                // Detect country, network
                                $mccmncInfo = $allMCCMNC[$smsStatus->getMccMnc ()];
                                if ($mccmncInfo) {
                                    $queueParams['country'] = $mccmncInfo['country'];
                                    $queueParams['network'] = $mccmncInfo['network'];
                                    // Detect Ported
                                    if ( $queue->network != '' && $queue->network != $queueParams['network'] ) {
                                        $queueParams['ported'] = 1;
                                    }
                                }
                                // Detect Preferred service provider
                                if ( isset($allPreferredServiceProvider[$queueParams['country']]) && isset($allPreferredServiceProvider[$queueParams['country']][$queueParams['network']])) {
                                    $detectServiceProvider = $allPreferredServiceProvider[$queueParams['country']][$queueParams['network']];
                                }
                            }

                            if ($smsStatus->getPrice ()) {
                                $queueParams ['return_price'] = $smsStatus->getPrice ();
                            }
                            if ($smsStatus->getCurrency ()) {
                                $queueParams ['return_currency'] = $smsStatus->getCurrency ();
                            }
                            if ($smsStatus->getStatus ()) {
                                $queueParams ['return_status'] = $smsStatus->getStatus ();

                                // detect report status pending
                                if($queueParams['return_status'] == 'PENDING') {
                                    $queueParams ['report_status'] = 'PENDING';
                                }else {
                                    $queueParams ['report_status'] = 'REPORTED';
                                }
                            }
                            if ($smsStatus->getStatusMessage ()) {
                                $queueParams ['return_status_message'] = $smsStatus->getStatusMessage();
                                $queueParams ['return_json'] = $smsStatus->getDataJson();
                                $queueParams ['report_updated_at'] = date('Y-m-d H:i:s');
                                $queueParams ['updated_at'] = $queue->updated_at;
                            }

                            $queryValue .= " ( ";
                            $temp = 0;
                            foreach($queueParams as $field => $value) {
                                $queryValue .= empty($value) ? " NULL " : " '". $value ."' ";
                                $queryValue .= $temp == count($queueParams) - 1 ? " " : ", ";
                                $temp++;
                            }
                            $queryValue .= " ) ";

                            $arrReportID[$queue->service_provider][] = $queue->return_message_id;

                            // 4. Update subscriber
                            if ($smsStatus->getMccMnc () && $queue->service_provider) {
                                $subscriberParams = array (
                                    'mccmnc' => $queueParams ['return_mccmnc'],
                                    'service_provider' => $queue->service_provider
                                );
                                if (isset($queueParams['country'])) {
                                    $subscriberParams['country'] = $queueParams['country'];
                                }
                                if (isset($queueParams['network'])) {
                                    $subscriberParams['network'] = $queueParams['network'];
                                }
                                if (isset($detectServiceProvider)) {
                                    $subscriberParams['service_provider'] = $detectServiceProvider;
                                }
                                if (isset($queueParams['ported'])) {
                                    $subscriberParams['ported'] = $queueParams['ported'];
                                }
                                $this->subscriberService->updateSubscriber ( $subscriberParams, $queue->subscriber_id, $queue->list_id );
                            }
                        }else {
                            array_push($arrAgainReport, $queue->id);
                        }

                        // // add value into query insert or update for test
                        // $query .= " ('". $queue->id ."', '". $queue->list_id ."', '". $queue->subscriber_id ."', '". $queue->phone ."', 'REPORTED', 'DELIVERED', '". date('Y-m-d H:i:s') ."', '". $queue->updated_at ."') ";
                        // $query .= $key == count($queues) - 1 ? "" : ", ";
                        // Log::error ( 'Report SMS-' . date ( 'Y-m-d' ) . " " .  json_encode($queue->phone, true) );
                        // $arrServiceProvider = config('constants.service_provider_report');
                        // if(!!in_array($queue->service_provider, $arrServiceProvider) && !empty($queue->return_message_id)) {
                        //     $arrReportID[$queue->service_provider][] = $queue->return_message_id;
                        // }
                    }
                }

                if(count($arrReportID) > 0) {
                    
                    // requery production
                    $query .= $queryValue . " ON DUPLICATE KEY UPDATE return_sms_count = VALUES(return_sms_count), return_mccmnc = VALUES(return_mccmnc), country = VALUES(country), network = VALUES(network), ported = VALUES(ported), return_price = VALUES(return_price), return_currency = VALUES(return_currency), report_status = VALUES(report_status), return_status_message = VALUES(return_status_message), return_json = VALUES(return_json), return_status = VALUES(return_status), report_updated_at = VALUES(report_updated_at), updated_at = VALUES(updated_at);";

                    // $data = (object) [
                    //     'results' => [
                    //         (object) [
                    //             'query' => $query
                    //         ]
                    //     ]
                    // ];

                    // \App\Facades\CustomLog::info ( 'Tracking Delivery Status SMS Via MESSAGEBIRD', 'TrackingDeliveryStatusSMS-' . date ( 'Y-m-d' ), json_decode(json_encode($data), true) );

                    // run query insert or update queue
                    $result = $this->queueRepo->insertOrUpdateMultipleRowQueue($query);

                    if($result == true) {
                        // remove data in table report
                        foreach($arrReportID as $key => $listID) {
                            if(count($listID) > 0) {
                                $tableReport = config("constants.table_service_provider_report");
        
                                $this->queueRepo->deleteReportByListID($tableReport[$key], 'return_message_id', $listID);
                            }
                        } 
                    }else {
                        // push again queue
                        QueueReport::dispatch($userID, $campaignID, $queueIDs)->onQueue('report');
                    }
                }

                if(count($arrAgainReport) > 0) {
                    // update agaign report
                    $result = $this->queueRepo->updateAgaignReport($userID, $campaignID, $arrAgainReport);
                    // if(!!$result) {
                    //     $data = (object) [
                    //         'results' => [
                    //             (object) [
                    //                 'userID' => $userID,
                    //                 'campaignID' => $campaignID,
                    //                 'result' => $result,
                    //                 'listID' => implode(',', $arrAgainReport)
                    //             ]
                    //         ]
                    //     ];

                    //     \App\Facades\CustomLog::info ( 'Tracking Delivery Status Push Again', 'TrackingDeliveryStatusSMS-' . date ( 'Y-m-d' ), json_decode(json_encode($data), true) );
                    // }
                }
                

                // query test
                // $query .= " ON DUPLICATE KEY UPDATE report_status = VALUES(report_status), return_status = VALUES(return_status), report_updated_at = VALUES(report_updated_at), updated_at = VALUES(updated_at);";


                // get total report status pending or reporting
                $totalReporting = $this->queueRepo->getTotalByReportStatus($campaign->user_id, $campaign->id);

                return (object) [
                    'status' => true,
                    'report_status' => $totalReporting == 0 ? 'processed' : 'processing',
                    'campaign' => $campaign
                ];
            }

            return (object) [
                'status' => false,
            ];
        }catch(\Exception $e) {
            $data = (object) [
                'results' => [
                    (object) [
                        'result' => $e->getMessage(),
                    ]
                ]
            ];

            \App\Facades\CustomLog::info ( 'Error Run Queue', 'TrackingDeliveryStatusSMS-' . date ( 'Y-m-d' ), json_decode(json_encode($data), true) );
            return (object) [
                'status' => false,
            ];
        }
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \App\Services\Campaign\IQueueService::trackingDeliveryReports()
     */
    public function trackingDeliveryReports($campaign, $processAmount = 60) {
        // All MCCMNC
        $allMCCMNC = $this->mccmncService->fetchAllOptions();
        // All Preferred service provider
        $allPreferredServiceProvider = $this->preferredServiceProviderService->fetchAllPreferredGroupByCountryNetwork();
        //
        // 1. Caculate loop by $processAmount
        /*$totalsQueue = $this->queueRepo->getTrackingTotals ( $campaign->user_id, $campaign->id );
        $totalsLoop = ceil ( $totalsQueue / $processAmount );
        // 2. Loop tracking delivery report status SMS
        for($i = 1; $i <= $totalsLoop; $i ++) {
            // --- Get queue by $processAmount limit
            $offset = $processAmount * ($i-1);*/
        $queues = $this->queueRepo->getTrackingQueues ( $campaign->user_id, $campaign->id, $processAmount/*, $offset*/);
            // --- Loop
            foreach ( $queues as $queue ) {
                if ($queue->service_provider) {
                    $smsStatus = $this->smsService->getSMSInfo ( $queue->return_message_id, $queue->service_provider );
                    if ($smsStatus instanceof SMSReportResponse) {
                        // 3. Update to queue
                        $queueParams = array ();
                        if ($smsStatus->getSmsCount () > $queue->return_sms_count) {
                            $queueParams ['return_sms_count'] = $smsStatus->getSmsCount ();
                        }
                        if ($smsStatus->getMccMnc ()) {
                            $queueParams ['return_mccmnc'] = $smsStatus->getMccMnc ();
                            // Detect country, network
                            $mccmncInfo = $allMCCMNC[$smsStatus->getMccMnc ()];
                            if ($mccmncInfo) {
                                $queueParams['country'] = $mccmncInfo['country'];
                                $queueParams['network'] = $mccmncInfo['network'];
                                // Detect Ported
                                if ( $queue->network != '' && $queue->network != $queueParams['network'] ) {
                                    $queueParams['ported'] = 1;
                                }
                            }
                            // Detect Preferred service provider
                            if ( isset($allPreferredServiceProvider[$queueParams['country']]) && isset($allPreferredServiceProvider[$queueParams['country']][$queueParams['network']])) {
                                $detectServiceProvider = $allPreferredServiceProvider[$queueParams['country']][$queueParams['network']];
                            }
                        }
                        if ($smsStatus->getPrice ()) {
                            $queueParams ['return_price'] = $smsStatus->getPrice ();
                        }
                        if ($smsStatus->getCurrency ()) {
                            $queueParams ['return_currency'] = $smsStatus->getCurrency ();
                        }
                        if ($smsStatus->getStatus ()) {
                            $queueParams ['return_status'] = $smsStatus->getStatus ();
                        }
                        if ($smsStatus->getStatusMessage ()) {
                            $queueParams ['return_status_message'] = $smsStatus->getStatusMessage();
                            $queueParams ['return_json'] = $smsStatus->getDataJson();
                            $queueParams ['report_updated_at'] = date('Y-m-d H:i:s');
                            $queueParams ['updated_at'] = $queue->updated_at;
                        }
                        $this->queueRepo->updateQueue ( $queueParams, $queue->id, $campaign->user_id, $campaign->id, true );
                        // 4. Update subscriber
                        if ($smsStatus->getMccMnc () && $queue->service_provider) {
                            $subscriberParams = array (
                                'mccmnc' => $queueParams ['return_mccmnc'],
                                'service_provider' => $queue->service_provider
                            );
                            if (isset($queueParams['country'])) {
                                $subscriberParams['country'] = $queueParams['country'];
                            }
                            if (isset($queueParams['network'])) {
                                $subscriberParams['network'] = $queueParams['network'];
                            }
                            if (isset($detectServiceProvider)) {
                                $subscriberParams['service_provider'] = $detectServiceProvider;
                            }
                            if (isset($queueParams['ported'])) {
                                $subscriberParams['ported'] = $queueParams['ported'];
                            }
                            $this->subscriberService->updateSubscriber ( $subscriberParams, $queue->subscriber_id, $queue->list_id );
                        }
                    }
                }
            // }
        }
        // Get totals by status
        return $this->queueRepo->getTotalsByReturnStatus( $campaign->user_id, $campaign->id );
    }

    /**
     * fn get queue by phone
     * @param unknown $phone
     * @param unknown $userID
     * @param unknown $campaignID
     * @return boolean|unknown
     */
    public function getQueueByPhone($phone, $userID, $campaignID) {
        try {
            $queue = $this->queueRepo->getQueueByPhone($phone, $userID, $campaignID);
            if(empty($queue)) {
                return false;
            }

            // return (object) $queue->toArray();
            return $queue;
        }catch(\Exception $e) {
            return false;
        }
    }

    /**
     * fn add queue of api campaign
     * 
     */
    public function addSMS($request, $campaign) {
        $from = $request->get('from');
        $stringPhone = is_array($request->get('to')) ? implode(",", $request->get('to')) : $request->get('to');
        // get current user
        $currentUser = Auth::user();
        // get parent user
        $parentUser = $this->authService->getUserInfo($currentUser->parent_id);
        // detect country network service provider of phone
        $results = $this->queueRepo->detectCountryNetworkServiceProviderOfPhone($stringPhone, $currentUser->id, $parentUser->id, $currentUser->default_price_sms , $parentUser->default_price_sms);
        $totalPrice = 0;
        //count sms
        $messageCount = $this->calculateMessageCount($request->get('text'));
        $queueParams = array();
        $allQueues = array();
        foreach($results as $result) {
            $queueParams = $result;
            $clientPriceTotals = $queueParams->client_price * $messageCount;
            $queueParams->queue_id = Uuid::uuid4()->toString();
            $queueParams->sender = $from;
            $queueParams->list_id = 0;
            $queueParams->subscriber_id = 0;
            $queueParams->message = $request->get('text');
            $queueParams->message_count = $messageCount;
            $queueParams->sum_price_agency = $queueParams->agency_price * $messageCount;
            $queueParams->sum_price_client = $clientPriceTotals;
            $totalPrice += $clientPriceTotals; 
            unset($queueParams->agency_price);
            unset($queueParams->client_price); 
            array_push( $allQueues, (array)$queueParams );
        }
        // check user can add or with draw credit
        $canBeSend = $this->checkUserCanBeAddOrWithdrawCredit($currentUser, $totalPrice);
        if ( !$canBeSend ) { // If true then account out of money.
            return $this->fail(trans('token.out_of_money'));
        }

        // add table queue
        $queue = $this->queueRepo->createQueueApi($allQueues, $currentUser->id, $campaign->id);
        if( !$queue ) {
            return $this->fail(trans('token.can_not_add_queue'));
        }

        // update status_api campaign
        $attributes = array();
        if( $campaign->tracking_delivery_report == 'PROCESSED' ) {
            $attributes['tracking_delivery_report'] = 'PENDING';
        }

        if( $campaign->backend_statistic_report == 'PROCESSED' ) {
            $attributes['backend_statistic_report'] = 'PENDING';
        }
        
        if( !empty($attributes) ) {
            $statusCampaign = $this->campaignRepo->updateStatusCampaignApiAccount($attributes,  $currentUser->id, $campaign->id);
        }

        $response = $this->formatResultSMS($allQueues);
        return $this->success($response);
    }

    /**
     * fn format data response add sms
     */
    public function formatResultSMS($response) {
        $results = array();
        foreach($response as $res) {
            $result[ 'from' ] = $res['sender'];
            $result[ 'to' ] = $res['phone'];
            $result[ 'message_id' ] = $res['queue_id'];
            array_push($results, $result);
        }

        return $results; 
    }

    /**
     * fn get report by uuid 
     */
    public function getReportApi($uuid, $campaign) {
        try {
            // get current user
            $currentUser = Auth::user();
            $sender = $campaign->sender;
            $report = $this->queueRepo->getReportApi($uuid, $currentUser->id, $campaign->id);

            if(empty($report)) {
               return $this->fail(); 
            }
            $report = $this->formatResponseReport($report);

            return $this->success($report);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * format data response report api
     */
    public function formatResponseReport($report) {
        $result = array();
        $result['from'] = $report->sender;
        $result['to'] = $report->phone;
        $result['message_id'] = $report->queue_id;
        switch ( $report->return_status ) {
            case "PENDING":
            case "EXPIRED":
            case "FAILED":
                break;
            case "DELIVERED":
                $result['return_mccmnc'] = $report->return_mccmnc;
                $result['message_count'] = $report->message_count;
                $result['sum_price_client'] = $report->sum_price_client;
                break;
        }
        $result['return_status'] = $report->return_status;
        return $result;
    }
}
