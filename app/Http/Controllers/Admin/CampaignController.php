<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Campaign\ICampaignService;
use App\Services\SubscriberLists\ISubscriberListService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateCampaignRequest;
use App\Services\Campaign\ICampaignRecipientsService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Lang;
use App\Services\ShortLinks\IShortLinkService;
use App\Services\Campaign\ICampaignLinkService;
use Mockery\Exception;
use App\Services\SMS\ISMSService;
use App\Services\Auth\IAuthenticationService;
use App\Services\Campaign\IQueueService;
use App\Models\Queue;
use App\Services\CustomFields\ICustomFieldService;
use App\Services\InboundMessages\IInboundMessagesService;
use Log;

class CampaignController extends Controller {
    protected $request;
    protected $campaignService;
    protected $campaignRecipientService;
    protected $listService;
    protected $shortLinkService;
    protected $campaignLinkService;
    protected $smsService;
    protected $authService;
    protected $queueService;
    protected $customFieldService;
    protected $inboundMessagesService;
    protected $campaignPausedService;

    public function __construct(
            Request $request,
            ICampaignService $ICampaignService,
            ISubscriberListService $IListService,
            ISMSService $ISMSService,
            ICampaignRecipientsService $ICampaignRecipientService,
            IShortLinkService $shortLinkService,
            ICampaignLinkService $campaignLinkService,
            IAuthenticationService $IAuthService,
            IQueueService $IQueueService,
            ICustomFieldService $ICustomFieldService,
            IInboundMessagesService $IInboundMessagesService
    ) {
        $this->request = $request;
        $this->campaignService = $ICampaignService;
        $this->listService = $IListService;
        $this->campaignRecipientService = $ICampaignRecipientService;
        $this->shortLinkService = $shortLinkService;
        $this->campaignLinkService = $campaignLinkService;
        $this->smsService = $ISMSService;
        $this->authService = $IAuthService;
        $this->queueService = $IQueueService;
        $this->customFieldService = $ICustomFieldService;
        $this->inboundMessagesService = $IInboundMessagesService;
    }
    public function getCampaignByQuery(Request $request) {
        $data = $this->campaignService->getCampaignByQuery ( $request );
        return $data;
    }

    public function create() {
        $user = Auth::user ();
        $userId = $user->id;
        $timeZone = $this->campaignService->getTimeZone ();
        $userTimeZone = Auth::user()->time_zone;
        $senderObject = $this->authService->getSenderList($userId);
        $allList = $this->listService->fetchListOptionsByUser ( $userId );
        $isTrackingLink = $user->is_tracking_link ? true:false;
        return view ( 'admins.campaigns.add', [
                'timeZone' => $timeZone,
                'user_timeZone' => $userTimeZone,
                'allList' => $allList,
                'senderList' => array_reverse( (array)$senderObject),
                'isTrackingLink' => $isTrackingLink
        ] );
    }

    public function info($id) {
        $user = Auth::user ();
        $userId = $user->id;
        $balance = $user->credits - $user->credits_usage;
        $timeZone = $this->campaignService->getTimeZone ();
        $campaign_item = $this->campaignService->getCampaignInfo($id);
        $senderObject = $this->authService->getSenderList($userId);
        $allList = $this->listService->fetchListOptionsByUser ( $userId );
        $userTimeZone = Auth::user()->time_zone;
        $list_subscriber_object = $this->campaignRecipientService->getCampaignRecipientsInfoByCampaignId($id, $user->isGroup4() ? $user->reader_id : null)->ToArray();
        $list_subscriber_id = [];
        foreach ($list_subscriber_object as $list_item) {
            array_push($list_subscriber_id, $list_item->list_id);
        }
        $isTrackingLink = $user->is_tracking_link ? true:false;
        return view ( 'admins.campaigns.update', [
                'timeZone' => $timeZone,
                'user_timezone' => $userTimeZone,
                'allList' => $allList,
                'senderList' => array_reverse( (array)$senderObject),
                'item' => $campaign_item,
                'list_subscriber_id' => $list_subscriber_id,
                'isTrackingLink' => $isTrackingLink,
                'balance' => $balance
        ] );
    }

    // Handle API apis/campaigns/add-sender
    public function addSender(Request $request) {
        $userId = Auth::user ()->id;
        $sender = $request->new_sender;
        return $result = $this->authService->addNewSender($userId, $sender);
    }

    // Handle API apis/campaigns/amend
    public function amend(Request $request) {
        $result = $this->campaignService->amendCampaign ( $request->campaign_id );
        return $result;
    }

    // Handle API apis/campaigns/update-status
    public function updateStatusCampaign(Request $request) {
        $result = $this->campaignService->updateCampaignStatus ( $request->campaign_id );
        return $result;
    }

    // Handle API apis/campaigns/test-send-sms
    public function testSendSMS(Request $request) {
        // set time out limit
        set_time_limit(0);
        $result = $this->campaignService->sendSMSTest($request);
        if(!$result->status) {
            return $result->error;
        }

        return $result->data;
    }

    // fn update campaign
    public function update(CreateCampaignRequest $request, $id) {
        $result = $this->campaignService->updateCampaign ($id, $request );
        if ($result['status']) {
            Session::flash ( 'success', $result['message'] );
        } else {
            Session::flash ( 'error', $result['message'] );
        }

        return redirect()->route ( 'campaign.index' );
    }

    // fn create campaign
    public function store(CreateCampaignRequest $request) {
        $result = $this->campaignService->createCampaign ( $request );
        if ($result['status']) {
            Session::flash ( 'success', $result['message'] );
        } else {
            Session::flash ( 'error', $result['message'] );

        }
        return redirect ()->route ( 'campaign.index' );

    }

    // fn delete campaign
    public function delete(Request $request) {
        $result = $this->campaignService->deleteCampaign ( $request->campaign_id );
        if ($result['status']) {
            Session::flash ( 'success', Lang::get ( 'notify.delete_success' ) );
            return [
                    "status" => true,
                    "message" => Lang::get ( 'notify.delete_success' )
            ];
        } else {
            return [
                    "status" => false,
                    "message" => Lang::get ( 'notify.delete_error' )
            ];
        }
    }

    /**
     * FN SEND CAMPAIGN
     * @return void|string
     */
    public function cronSendCampaign()
    {
        try {
            // set time out limit
            set_time_limit(0);
            return $this->campaignService->campaignNeedSend();
        }catch(\Exception $e) {
            Log::error("Error cron job send campaign " . json_encode($e->getMessage()));
            return $e->getMessage();
        }
    }

    /**
     * FN GET STATUS SENT MESSAGE FORM SERVICE PROVISER
     * @return number
     */
    public function cronGetDeliveryReports()
    {
        try {
            // set time out limit
            set_time_limit(0);
            //
            return $this->campaignService->pushQueueReport();
        }catch(\Exception $e) {
            Log::error("Error cron job get delivery report ". json_encode($e->getMessage()));
            return $e->getMessage();
        }
    }

    // function short link
    public function shortLink() {
        try {
            $link = $this->request->get('link');
            $campaign_id = $this->request->get('campaign_id', '');
            if($link != "") {
                $campaignLink = $this->campaignLinkService->createCampaignLink($link, $campaign_id);
                $campaignLink = $campaignLink->toArray();
                if(!!$campaignLink) {
                    // $userId = Auth::user()->id;
                    // $shortLink = url('/') . "/redirect-link?user_id=" . $userId . "&campaign_link_id=" . $campaignLink['id'];
                    // $result = $this->shortLinkService->shortLink($shortLink);
                    // if(!!$result->status) {
                    //     $short_link = $result->data['shortLink'];
                    //     $this->campaignLinkService->updateCampaignLink($campaignLink['id'], ["short_link" => $short_link . "+"]);
                    //     return [
                    //             'short_link' => $result->data['id'],
                    //             'campaign_link_id' => $campaignLink['id'],
                    //             'message' => Lang::get ( 'notify.short_link_sucecss' )
                    //     ];
                    // }else {
                    //     // remove short link in db
                    //     $this->campaignLinkService->deleteCampaignLink($campaignLink['id']);
                    //     throw new Exception(Lang::get ( 'notify.short_link_error' ));
                    // }

                    return [
                        'short_link' => "%%" . env('DOMAIN_SHORT_LINK') ."_". $campaignLink['id'] ."%%",
                        'campaign_link_id' => $campaignLink['id'],
                        'message' => Lang::get ( 'notify.short_link_sucecss' )
                    ];
                }else {
                    throw new Exception(Lang::get ( 'notify.short_link_error' ));
                }
            }else {
                throw new Exception(Lang::get ( 'validationForm.link.required' ));
            }
        }catch(\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }

    }

    // fn etimation campaign
    public function summaryCampaign() {
        try {
            $result = $this->campaignService->summarySubscribers($this->request);
            if(!!$result->status) {
                return response()->json( (array)$result->data, 200);
            }

            return response()->json($result->error, 400);

        }catch(\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    // fn redirect link
    public function redirectShorLLink() {
        try {
            $result = $this->campaignLinkService->redirectLink($this->request);
            if(!!$result->status) {
                $data = $result->data;
                return redirect($data->url);
            }
            return view('test');
            // return "Error";
        }catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    // fn clone campaign
    public function cloneCampaign() {
        try {
            $result = $this->campaignService->cloneCampaign($this->request);

            if(!!$result->status) {
                return [
                        "message" => Lang::get ( 'notify.clone_campaign_success' ),
                        "warring" => count($result->data) > 0 ? Lang::get ( 'notify.warring_clone_campaign' ) : ""
                ];
            }

            throw new Exception();
        }catch(\Exception $e) {
            return [
                    "message" => Lang::get ( 'notify.clone_campaign_error' )
            ];
        }
    }

    // fn total send campaign of user
    public function totalSend() {
        try {
            $result = $this->campaignService->totalSendCampaignOfUsers($this->request);

            return $result;
        }catch (\Exception $e) {
            return [];
        }
    }

    public function cronGetReceivedMessages()
    {
        set_time_limit(0);
        $receivedMessages = $this->smsService->getReceivedMessages('INFOBIP');
        if ($receivedMessages) {
            $this->inboundMessagesService->storeInboundMessages($receivedMessages);
        }
        return count($receivedMessages) . ' messages was received at ' . date('Y-m-d H:i:s');
    }

    public function sendAgainCampaugnPaused() {
        set_time_limit(0);
        return $this->campaignService->sendAgainCampaignPause();
    }

    public function addUnsubscriber($campaignID) {
        try {
            $result = $this->campaignService->addLinkUnsubscriber($campaignID);

            if(!$result) {
                return response()->json(["message" => Lang::get ( 'notify.link_unsubscribe_error' )], 400);
            }

            return response()->json(["link" => $result, "message" => Lang::get ( 'notify.link_unsubscribe_success' )], 200);
        }catch(\Exception $e) {
            return response()->json(["message" => Lang::get ( 'notify.link_unsubscribe_error' )], 500);
        }
    }
}
