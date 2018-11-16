<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SMS\ISMSService;
use App\Services\MailServices\MailService;
use App\Services\Campaign\IQueueService;
use App\Mail\CampaignStart;
use Illuminate\Support\Facades\Hash;
use App\Services\Auth\IAuthenticationService;
use App\Services\SubscriberLists\ISubscriberListService;
use Illuminate\Support\Facades\Auth;
use App\Services\SMS\API\TmSmsProviderService;
use Illuminate\Support\Facades\DB;
use App\Services\Clients\IPriceConfigurationService;
use App\Entities\SMSReportResponse;
use App\Repositories\Campaign\IQueueRepository;
use Illuminate\Support\Facades\Crypt;
use App\Services\Campaign\ICampaignService;

class TestController extends Controller
{

    protected $SMSService;
    protected $mailService;
    protected $queueService;
    protected $authService;
    protected $subscriberListService;
    protected $priceConfigurationService;
    protected $queueRepo;
    protected $campaignService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        ISMSService $ISMSService, 
        MailService $mailService, 
        IQueueService $IQueueService, 
        IAuthenticationService $authService, 
        ISubscriberListService $subscriberListService,
        IPriceConfigurationService $IPriceConfigurationService,
        IQueueRepository $queueRepo,
        ICampaignService $campaignService
    )
        /*IQueueService $IQueueService,*/ 
    
    {
        $this->SMSService = $ISMSService;
        $this->mailService = $mailService;
        // $this->queueService = $IQueueService;
        $this->authService = $authService;
        $this->subscriberListService = $subscriberListService;
        $this->priceConfigurationService = $IPriceConfigurationService;
        $this->queueRepo = $queueRepo;
        $this->campaignService = $campaignService;
    }

    // public function index()
    // {
    //     $arrData = [
    //         (object) [
    //             'sender'    => 'test',
    //             'message'   => 'message 1',
    //             'phone'     => '841642378975',
    //             'service_provider'  => 'INFOBIP'
    //         ]
    //     ];

    //     $data = [];

    //     foreach($arrData as $item) {
    //         $result = $this->SMSService->sendSMSAsync($item->sender, $item->phone, $item->message, $item->service_provider, 40, $item);
    //         array_push($data, $result);
    //     }

    //     dd($data);
    // }

    public function index() {
        // $subjectEmail = 'Campaign scheduled.';
        // $object = (object) [
        //         "name" => 'test',
        //         "content" => "Campaign scheduled."
        // ];
        // return $this->campaignService->sendEmailNotificationCampaign($subjectEmail, $object, 'truongngo@success-ss.com.vn', '');
        dd($this->authService->getUserInfo(3)->parent);
    }

}
