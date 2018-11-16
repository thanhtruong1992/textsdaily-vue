<?php
namespace App\Services\SMS;

use App\Services\SMS\API\InfoBipProviderService;
use App\Services\SMS\API\MessageBirdProviderService;
use App\Services\SMS\API\TmSmsProviderService;
use App\Entities\SMSReportResponse;
use App\Services\Settings\IServiceProviderService;
use App\Entities\BalanceResponse;
use App\Entities\InboundMessageResponse;
use Illuminate\Support\Facades\App;
use App\Services\Reports\ITMSMSReportService;
use App\Services\SMS\API\RouteMobileProviderService;
use App\Services\Reports\IRouterMobileReportService;
use App\Services\Reports\IInfobipReportService;
use App\Repositories\Campaign\IQueueRepository;

class SMSService implements ISMSService
{
    protected $infoBip;
    protected $messageBird;
    protected $tmSms;
    protected $routeMobile;
    protected $serviceProviderService;
    protected $tmSMSReportService;
    protected $routeMobileReportService;
    protected $infobipReportService;
    protected $handleStatusSms;

    public function __construct( 
        IServiceProviderService $IServiceProviderService, 
        ITMSMSReportService $tmSMSReportService, 
        IRouterMobileReportService $routeMobileReportService,
        IInfobipReportService $infobipReportService,
        IHandleStatusSms $handleStatusSms
    )
    {
        $this->serviceProviderService = $IServiceProviderService;
        $this->routeMobileReportService = $routeMobileReportService;
        // Get config service provider
        $allServiceProvider = $this->serviceProviderService->fetchAllConfig();
        //
        \App::singleton(ISMSServiceProvider::class, InfoBipProviderService::class);
        $this->infoBip = \App::Make(ISMSServiceProvider::class);
        $this->infoBip->authorization( $allServiceProvider['INFOBIP']['config_username'], $allServiceProvider['INFOBIP']['config_password'] );
        \App::singleton(ISMSServiceProvider::class, MessageBirdProviderService::class);
        $this->messageBird = \App::Make(ISMSServiceProvider::class);
        $this->messageBird->authorization( $allServiceProvider['MESSAGEBIRD']['config_access_key'] );
        \App::singleton(ISMSServiceProvider::class, TmSmsProviderService::class);
        $this->tmSms = \App::Make(ISMSServiceProvider::class);
        $this->tmSms->authorization($allServiceProvider['TMSMS']['config_username'], $allServiceProvider['TMSMS']['config_password'], $allServiceProvider['TMSMS']['config_url']);
        $this->tmSMSReportService = $tmSMSReportService;
        \App::singleton(ISMSServiceProvider::class, RouteMobileProviderService::class);
        $this->routeMobile = \App::Make(ISMSServiceProvider::class);
        $this->routeMobile->authorization($allServiceProvider['ROUTEMOBILE']['config_username'], $allServiceProvider['ROUTEMOBILE']['config_password'], $allServiceProvider['ROUTEMOBILE']['config_url']);
        $this->infobipReportService = $infobipReportService;

        $this->handleStatusSms = $handleStatusSms;
    }

    public static function checkValidPhoneNumber( $phoneNumber )
    {
        if(!preg_match('/^\(?\+?([0-9]{1,4})\)?[-\. ]?(\d{3})[-\. ]?([0-9]{1,7})$/', trim($phoneNumber))) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * fn send sms async
     * @param string $sender
     * @param array|string $phone
     * @param string $message
     * @param string $serviderProvider
     * @param int $validityPerioHours
     * @return null
     */
    public function sendSMSAsync($sender, $phone = array(), $message, $serviceProvider = null, $validityPeriodHours = 48, $queue = null) {
        switch ( $serviceProvider ) {
            case 'MESSAGEBIRD' :
                return $this->messageBird->sendMessageAsync( $sender, $phone, $message, $validityPeriodHours, $queue);
                break;
            case 'INFOBIP' :
                return $this->infoBip->sendMessageAsync( $sender, $phone, $message, $validityPeriodHours, $queue);
                break;
            case 'TMSMS' :
                return $this->tmSms->sendMessageAsync( $sender, $phone, $message, $validityPeriodHours, $queue);
                break;
            case 'ROUTEMOBILE' :
                return $this->routeMobile->sendMessageAsync($sender, $phone, $message, $validityPeriodHours, $queue);
                break;
            default:
                return $this->infoBip->sendMessageAsync( $sender, $phone, $message, $validityPeriodHours, $queue);
                break;
        }
        return null;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\SMS\ISMSService::sendSMS()
     */
    public function sendSMS( $sender, $phone = array(), $message, $serviceProvider = null, $validityPeriodHours = 48 )
    {
        switch ( $serviceProvider ) {
            case 'MESSAGEBIRD' :
                $response = $this->messageBird->sendMessage( $sender, $phone, $message, $validityPeriodHours );
                // Write Log
                \CustomLog::info ( 'Send SMS Via MESSAGEBIRD', 'SendSMS-' . date ( 'Y-m-d' ), json_decode(json_encode($response), true) );
                //
                return $this->handleStatusSms->formatResultMessageBird($response);
                break;
            case 'INFOBIP' :
                $response = $this->infoBip->sendMessage( $sender, $phone, $message, $validityPeriodHours );
                // Write Log
                \CustomLog::info ( 'Send SMS Via INFOBIP', 'SendSMS-' . date ( 'Y-m-d' ), json_decode(json_encode($response), true) );
                //
                return $this->handleStatusSms->formatResultInfoBip($response);
                break;
            case 'TMSMS' :
                $response = $this->tmSms->sendMessage( $sender, $phone, $message, $validityPeriodHours );
                // Write Log
                \CustomLog::info ( 'Send SMS Via TM SMS', 'SendSMS-' . date ( 'Y-m-d' ), json_decode(json_encode($response), true) );
                //
                return $this->handleStatusSms->formatResultTMSMS($response);
                break;
            case 'ROUTEMOBILE' :
                $response = $this->routeMobile->sendMessage($sender, $phone, $message, $validityPeriodHours);
                // Write Log
                \CustomLog::info ( 'Send SMS Via Route Mobile', 'SendSMS-' . date ( 'Y-m-d' ), json_decode(json_encode($response), true) );
                //
                return $this->handleStatusSms->formatResultRouteMobile($response);
                break;
            default:
                $response = $this->infoBip->sendMessage( $sender, $phone, $message, $validityPeriodHours );
                // Write Log
                \CustomLog::info ( 'Send SMS Via Default Gateway', 'SendSMS-' . date ( 'Y-m-d' ), json_decode(json_encode($response), true) );
                //
                return $this->handleStatusSms->formatResultInfoBip($response);
                break;
        }
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\SMS\ISMSService::getSMSInfo()
     */
    public function getSMSInfo( $id, $serviceProvider = null )
    {
        switch ( $serviceProvider ) {
            case 'MESSAGEBIRD' :
                $response = $this->messageBird->getMessageInfo( $id );
                //
                $results = array();
                if ( !empty($response) && $response->recipients ) {
                    if ( $response->recipients->items ) {
                        foreach ( $response->recipients->items as $item ) {
                            $smsReportResponse = new SMSReportResponse();
                            $smsReportResponse->setId( $response->getId());
                            $smsReportResponse->setFrom( $response->originator);
                            $smsReportResponse->setTo( $item->recipient);
                            $smsReportResponse->setBody( $response->body);
                            $smsReportResponse->setStatus( $this->handleStatusSms->handleConvertStatus( $item->status ) );
                            $smsReportResponse->setDataJson( json_encode($response) );
                            //
                            $results = $smsReportResponse;
                        }
                    }
                    // Write Log
                    \CustomLog::info ( 'Tracking Delivery Status SMS Via MESSAGEBIRD', 'TrackingDeliveryStatusSMS-' . date ( 'Y-m-d' ), json_decode(json_encode($response), true) );
                }
                //
                return $results;
                break;
            case 'INFOBIP' :
                $response = $this->infoBip->getMessageInfo( $id );
                $results = array();
                if ( !empty($response) && $response->getResults()) {
                    foreach ( $response->getResults() as $result ) {
                        $smsReportResponse = new SMSReportResponse();
                        $smsReportResponse->setId( $result->getMessageId() );
                        $smsReportResponse->setFrom( $result->getFrom() );
                        $smsReportResponse->setTo( $result->getTo() );
                        $smsReportResponse->setBody( $result->getText() );
                        if ( $result->getSmsCount() ) {
                            $smsReportResponse->setSmsCount( $result->getSmsCount() );
                        }
                        if ( $result->getPrice() ) {
                            $smsReportResponse->setPrice( $result->getPrice()->getPricePerMessage() );
                            $smsReportResponse->setCurrency( $result->getPrice()->getCurrency() );
                        }
                        if ( $result->getMccMnc() ) {
                            $smsReportResponse->setMccMnc( $result->getMccMnc() );
                        }
                        if ( $result->getStatus() ) {
                            $smsReportResponse->setStatus( $this->handleStatusSms->handleConvertStatus($result->getStatus()->getGroupName()) );
                            $smsReportResponse->setStatusMessage($result->getStatus()->getDescription());
                        }
                        $smsReportResponse->setDataJson( json_encode($response) );
                        //
                        $results = $smsReportResponse;
                    }
                    // Write Log
                    \CustomLog::info ( 'Tracking Delivery Status SMS Via INFOBIP', 'TrackingDeliveryStatusSMS-' . date ( 'Y-m-d' ), json_decode(json_encode($response), true) );
                }
                
                
                //
                return $results;
                break;
            case 'TMSMS' : 
                $response = $this->tmSMSReportService->getTMSMSReport($id);
                $results = array();
                if (!empty($response)) {
                    $result = (object) $response;
                    $smsReportResponse = new SMSReportResponse();
                    $smsReportResponse->setId( $result->return_message_id );
                    $smsReportResponse->setFrom( $result->return_from );
                    $smsReportResponse->setTo( $result->return_to );
                    if ( !empty($result->return_sms_count) ) {
                        $smsReportResponse->setSmsCount( $result->return_sms_count );
                    }
                    if ( !empty($result->return_price) ) {
                        $smsReportResponse->setPrice( $result->return_price );
                    }
                    if ( !empty($result->return_currency) ) {
                        $smsReportResponse->setCurrency( $result->return_currency );
                    }
                    if ( !empty($result->return_mccmnc) ) {
                        $smsReportResponse->setMccMnc( $result->return_mccmnc );
                    }
                    $smsReportResponse->setStatus( $result->return_status );
                    $smsReportResponse->setStatusMessage($this->handleStatusSms->getErrorCodeTMSMS($result->return_error_code));
                    $smsReportResponse->setDataJson( $result->return_message );
                    //
                    $results = $smsReportResponse;
                    // delete data report
                    $this->tmSMSReportService->deleteTMSMSReport($result->id);

                    // Write Log
                    \CustomLog::info ( 'Tracking Delivery Status SMS Via TMSMS', 'TrackingDeliveryStatusSMS-' . date ( 'Y-m-d' ), json_decode(json_encode($response), true) );
                }
                //
                return $results;
                break;
            case 'ROUTEMOBILE' : 
                $response = $this->routeMobileReportService->getRouterMobileReport($id);
                $results = array();
                if (!empty($response)) {
                    $result = (object) $response;
                    $smsReportResponse = new SMSReportResponse();
                    $smsReportResponse->setId( $result->return_message_id );
                    $smsReportResponse->setFrom( $result->return_from );
                    $smsReportResponse->setTo( $result->return_to );
                    if ( !empty($result->return_sms_count) ) {
                        $smsReportResponse->setSmsCount( $result->return_sms_count );
                    }
                    if ( !empty($result->return_price) ) {
                        $smsReportResponse->setPrice( $result->return_price );
                    }
                    if ( !empty($result->return_currency) ) {
                        $smsReportResponse->setCurrency( $result->return_currency );
                    }
                    if ( !empty($result->return_mccmnc) ) {
                        $smsReportResponse->setMccMnc( $result->return_mccmnc );
                    }
                    $smsReportResponse->setStatus( $result->return_status );
                    $smsReportResponse->setStatusMessage($this->handleStatusSms->getErrorCodeRouteMobile($result->return_error_code));
                    $smsReportResponse->setDataJson( $result->return_message );
                    //
                    $results = $smsReportResponse;
                    // delete data report
                    $this->routeMobileReportService->deleteRouterMobileReport($result->id);

                    // Write Log
                    \CustomLog::info ( 'Tracking Delivery Status SMS Via Route Mobile ', 'TrackingDeliveryStatusSMS-' . date ( 'Y-m-d' ), json_decode(json_encode($response), true) );
                }
                //
                return $results;
                break;
            default:
                return false;
                break;
        }
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\SMS\ISMSService::getBalance()
     */
    public function getBalance($serviceProvider)
    {
        switch ( $serviceProvider ) {
            case 'MESSAGEBIRD' :
                $response = $this->messageBird->getBalance();
                //
                return new BalanceResponse($response->amount, $response->type);
                break;
            case 'INFOBIP' :
                $response = $this->infoBip->getBalance();
                //
                return new BalanceResponse($response->getBalance(), $response->getCurrency());
                break;
            case 'TMSMS' :
                $response = $this->tmSms->getBalance();
                //
                return new BalanceResponse($response->amount, $response->type);
                break;
            default:
                break;
        }
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\SMS\ISMSService::getReceivedMessages()
     */
    public function getReceivedMessages($serviceProvider = null)
    {
        switch ( $serviceProvider ) {
            case 'MESSAGEBIRD' :
                $response = $this->messageBird->getReceivedMessages();
                //
                return $response;
                break;
            case 'TMSMS' : 
                $response = $this->tmSms->getReceivedMessages();
                return $response;
                break;
            case 'INFOBIP' :
            default:
                $response = $this->infoBip->getReceivedMessages();
                $inboundMessages = [];
                foreach ( $response->getResults() as $item ) {
                    $inboundMessage = new InboundMessageResponse();
                    $inboundMessage->setFrom($item->getFrom());
                    $inboundMessage->setTo($item->getTo());
                    $inboundMessage->setText($item->getText());
                    $inboundMessage->setReceivedAt($item->getReceivedAt()->format('Y-m-d H:i:s'));
                    $inboundMessage->setMessageId($item->getMessageId());
                    $inboundMessage->setKeyword($item->getKeyword());
                    $inboundMessage->setJsonData(json_encode($item));
                    //
                    $inboundMessages[] = $inboundMessage;
                }
                //
                return $inboundMessages;
                break;
        }
    }

     /**
     *
     * {@inheritDoc}
     * @see \App\Services\SMS\ISMSService::getSMSInfo()
     */
    public function getSMSInfoNew( $id, $serviceProvider = null )
    {
        switch ( $serviceProvider ) {
            case 'MESSAGEBIRD' :
                $response = $this->messageBird->getMessageInfo( $id );
                //
                $results = array();
                if ( !empty($response) && !empty($response->recipients) ) {
                    if ( $response->recipients->items ) {
                        foreach ( $response->recipients->items as $item ) {
                            $smsReportResponse = new SMSReportResponse();
                            $smsReportResponse->setId( $response->getId());
                            $smsReportResponse->setFrom( $response->originator);
                            $smsReportResponse->setTo( $item->recipient);
                            $smsReportResponse->setBody( $response->body);
                            $smsReportResponse->setStatus( $this->handleStatusSms->handleConvertStatus( $item->status ) );
                            $smsReportResponse->setDataJson( json_encode($response) );
                            //
                            $results = $smsReportResponse;
                        }
                    }

                    // Write Log
                    \CustomLog::info ( 'Tracking Delivery Status SMS Via MESSAGEBIRD', 'TrackingDeliveryStatusSMS-' . date ( 'Y-m-d' ), json_decode(json_encode($response), true) );
                }
                
                //
                return $results;
                break;
            case 'INFOBIP' :
                $response = $this->infobipReportService->getMessageInfoNew( $id );
                $results = array();
                if ( !empty($response) && !empty($response->return_json) ) {
                    $result = json_decode($response->return_json);
                    $smsReportResponse = new SMSReportResponse();
                    $smsReportResponse->setId( $result->messageId );
                    if( !empty($result->from)) {
                        $smsReportResponse->setFrom( $result->from );
                    }
                    $smsReportResponse->setTo( $result->to );
                    if( !empty($result->text) ) {
                        $smsReportResponse->setBody( $result->text );
                    }
                    if ( !empty($result->smsCount) ) {
                        $smsReportResponse->setSmsCount( $result->smsCount );
                    }
                    if ( !empty($result->price)  ) {
                        $smsReportResponse->setPrice( $result->price->pricePerMessage );
                        $smsReportResponse->setCurrency( $result->price->currency );
                    }
                    if ( !empty($result->mccMnc) ) {
                        $smsReportResponse->setMccMnc( $result->mccMnc );
                    }
                    if ( !empty($result->status) ) {
                        $smsReportResponse->setStatus( $this->handleStatusSms->handleConvertStatus($result->status->groupName) );
                        $smsReportResponse->setStatusMessage( $result->status->description);
                    }
                    $smsReportResponse->setDataJson( json_encode($response) );
                    //
                    $results = $smsReportResponse;

                    // Write Log
                    \CustomLog::info ( 'Tracking Delivery Status SMS Via INFOBIP', 'TrackingDeliveryStatusSMS-' . date ( 'Y-m-d' ), json_decode(json_encode($response), true) );
                }
                
                //
                return $results;
                break;
            case 'TMSMS' : 
                $response = $this->tmSMSReportService->getTMSMSReport($id);
                $results = array();
                if (!empty($response)) {
                    $result = (object) $response;
                    $smsReportResponse = new SMSReportResponse();
                    $smsReportResponse->setId( $result->return_message_id );
                    $smsReportResponse->setFrom( $result->return_from );
                    $smsReportResponse->setTo( $result->return_to );
                    if ( !empty($result->return_sms_count) ) {
                        $smsReportResponse->setSmsCount( $result->return_sms_count );
                    }
                    if ( !empty($result->return_price) ) {
                        $smsReportResponse->setPrice( $result->return_price );
                    }
                    if ( !empty($result->return_currency) ) {
                        $smsReportResponse->setCurrency( $result->return_currency );
                    }
                    if ( !empty($result->return_mccmnc) ) {
                        $smsReportResponse->setMccMnc( $result->return_mccmnc );
                    }
                    $smsReportResponse->setStatus( $result->return_status );
                    $smsReportResponse->setStatusMessage($this->handleStatusSms->getErrorCodeTMSMS($result->return_error_code));
                    $smsReportResponse->setDataJson( $result->return_message );
                    //
                    $results = $smsReportResponse;
                    // delete data report
                    // $this->tmSMSReportService->deleteTMSMSReport($result->id);

                    // Write Log
                    \CustomLog::info ( 'Tracking Delivery Status SMS Via TMSMS', 'TrackingDeliveryStatusSMS-' . date ( 'Y-m-d' ), json_decode(json_encode($response), true) );
                }
                
                //
                return $results;
                break;
            case 'ROUTEMOBILE' : 
                $response = $this->routeMobileReportService->getRouterMobileReport($id);
                $results = array();
                if (!empty($response)) {
                    $result = (object) $response;
                    $smsReportResponse = new SMSReportResponse();
                    $smsReportResponse->setId( $result->return_message_id );
                    $smsReportResponse->setFrom( $result->return_from );
                    $smsReportResponse->setTo( $result->return_to );
                    if ( !empty($result->return_sms_count) ) {
                        $smsReportResponse->setSmsCount( $result->return_sms_count );
                    }
                    if ( !empty($result->return_price) ) {
                        $smsReportResponse->setPrice( $result->return_price );
                    }
                    if ( !empty($result->return_currency) ) {
                        $smsReportResponse->setCurrency( $result->return_currency );
                    }
                    if ( !empty($result->return_mccmnc) ) {
                        $smsReportResponse->setMccMnc( $result->return_mccmnc );
                    }
                    $smsReportResponse->setStatus( $result->return_status );
                    $smsReportResponse->setStatusMessage($this->handleStatusSms->getErrorCodeRouteMobile($result->return_error_code));
                    $smsReportResponse->setDataJson( $result->return_message );
                    //
                    $results = $smsReportResponse;
                    // delete data report
                    // $this->routeMobileReportService->deleteRouterMobileReport($result->id);

                    // Write Log
                \CustomLog::info ( 'Tracking Delivery Status SMS Via Route Mobile ', 'TrackingDeliveryStatusSMS-' . date ( 'Y-m-d' ), json_decode(json_encode($response), true) );
                }
                
                //
                return $results;
                break;
            default:
                return false;
                break;
        }
    }


}
?>