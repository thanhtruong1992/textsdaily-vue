<?php

namespace App\Services\InboundMessages;

use App\Services\BaseService;
use App\Repositories\InboundMessages\IInboundMessagesRepository;
use App\Services\Settings\IInboundConfigService;
use App\Services\SubscriberLists\ISubscriberListService;
use App\Services\Subscribers\ISubscriberService;
use Illuminate\Support\Facades\Auth;
use App\Services\UploadService;

class InboundMessagesService extends BaseService implements IInboundMessagesService {
    /**
     */
    protected $inboundMessagesRepo;
    protected $inboundConfigService;
    protected $subscriberListService;
    protected $subscriberService;
    protected $uploadService;

    /**
     *
     * @param IInboundMessagesRepository $IInboundMessagesRepo
     */
    public function __construct(
            IInboundMessagesRepository $IInboundMessagesRepo,
            IInboundConfigService $IInboundConfigService,
            ISubscriberListService $ISubscriberListService,
            ISubscriberService $ISubscriberService,
            UploadService $uploadService
    ) {
        $this->inboundMessagesRepo = $IInboundMessagesRepo;
        $this->inboundConfigService = $IInboundConfigService;
        $this->subscriberListService = $ISubscriberListService;
        $this->subscriberService = $ISubscriberService;
        $this->uploadService = $uploadService;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\IInboundMessagesService::getAllDataFormatDataTable()
     */
    public function getAllDataFormatDataTable($request) {
        $orderColumn = $request->get ( 'field' );
        $orderDirection = $request->get ( 'orderBy' );
        $results = $this->inboundMessagesRepo->getDataTableList($orderColumn, $orderDirection, $request);
        //
        return [
                'data' => $results->items(),
                'recordsTotal' => $results->total(),
                'recordsFiltered' => $results->total()
        ];
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\InboundMessages\IInboundMessagesService::storeInboundMessages()
     */
    public function storeInboundMessages($messages) {
        if (count($messages)) {
            foreach ( $messages as $msg ) {
                if ($msg instanceof \App\Entities\InboundMessageResponse) {
                    // Detect inbound config
                    $inboundConfig = $this->inboundConfigService->getInboundConfigByField('number', $msg->getTo());
                    $idUser = null;
                    //
                    $params = [];
                    $params['from'] = $msg->getFrom();
                    $params['to'] = $msg->getTo();
                    $params['message'] = $msg->getText();
                    $params['keyworks'] = $msg->getKeyword();
                    $params['message_id'] = $msg->getMessageId();
                    $params['return_data'] = $msg->getJsonData();
                    if ($inboundConfig && $inboundConfig->group3_user_id) {
                        $params['user_id'] = $inboundConfig->group3_user_id;
                        $idUser = $inboundConfig->group3_user_id;
                    }
                    $inboundMessages = $this->inboundMessagesRepo->createMessage($params, $idUser);
                    // Detect UNSUBSCRIBED by keyworks
                    if ($inboundMessages && $inboundMessages->user_id) {
                        $arrKeyworks = explode(';', strtolower($inboundConfig->keyworks));
                        $arrMessages = explode(' ', strtolower($msg->getText()));
                        $detectExists = !empty(array_intersect($arrKeyworks, $arrMessages));
                        if ($detectExists) {
                            // Store phone number to Global Suppression List
                            $globalSuppressionList = $this->subscriberListService->getGlobalSuppressionList($inboundMessages->user_id, ['id']);
                            if ($globalSuppressionList) {
                                $subscriberParams = [];
                                $subscriberParams['phone'] = $inboundMessages->from;
                                $subscriberParams['status'] = 'UNSUBSCRIBED';
                                $subscriberParams['unsubscription_date'] = date('Y-m-d H:i:s');
                                $subscriberParams['detect_status'] = 'PROCESSED';
                                $this->subscriberService->createSubscriber($subscriberParams, $globalSuppressionList->id);
                            }
                        }
                    }
                }
            }
            return true;
        }
        return false;
    }

    /**
     * fn export inbound message
     * {@inheritDoc}
     * @see \App\Services\InboundMessages\IInboundMessagesService::exportCSVInboundMessage()
     */
    public function exportCSVInboundMessage($request) {
        $user = Auth::user();
        //$path = '/var/www/html/abc/';
        $path = config('constants.path_file_export_inbound_message') . "/" . md5($user->id) . "/";
        // make forder
        $this->uploadService->makeForder($path);
        //clear forder
        $this->uploadService->clearFolder($path);
        $file = "Inbound_Message.csv";
        $fileName = public_path ( $path . $file);
        //$fileName = $path . $file;
        $this->inboundMessagesRepo->exportCSV($request, $fileName);
        $result = $this->uploadService->checkFile($path . $file);
        if(!$result) {
            return $this->fail();
        }

        return $this->success($fileName);
    }
}