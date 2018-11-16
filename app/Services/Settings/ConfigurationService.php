<?php

namespace App\Services\Settings;

use App\Services\BaseService;
use App\Repositories\Settings\IConfigurationRepository;
use Carbon\Carbon;
use App\Services\UploadService;
use App\Services\MailServices\MailService;
use App\Mail\ExportCenter;
use App\Services\SMS\ISMSService;
use File;
use Auth;
use App\Services\Auth\IAuthenticationService;

class ConfigurationService extends BaseService implements IConfigurationService {
    /**
     */
    protected $configurationRepo;
    protected $uploadServer;
    protected $mailService;
    protected $smsService;
    protected $serviceProviderService;
    protected $authService;

    /**
     *
     * @param IConfigurationRepository $IConfigurationRepo
     */
    public function __construct(IConfigurationRepository $IConfigurationRepo, UploadService $uploadServer, MailService $mailService, ISMSService $smsService, IServiceProviderService $serviceProviderService, IAuthenticationService $authService) {
        $this->configurationRepo = $IConfigurationRepo;
        $this->uploadServer= $uploadServer;
        $this->mailService = $mailService;
        $this->smsService = $smsService;
        $this->serviceProviderService = $serviceProviderService;
        $this->authService = $authService;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\IConfigurationService::fetchConfiguration()
     */
    public function fetchConfiguration() {
        $result = $this->configurationRepo->fetchConfiguration();
        if (!empty($result)) {
            $json_object = json_decode($result->value);

            return [
                'email' => $json_object->email,
                'time' => $json_object->time,
                'reseller' => $json_object->reseller,
                'detail' => $json_object->detail,
            ];
        }

        return [
                'email' => null,
                'time' => null,
                'reseller' => null,
                'detail' => null,
        ];
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\IConfigurationService::updateConfiguration()
     */
    public function updateConfiguration(array $attributes) {
        if (isset($attributes['_token'])) {
            unset($attributes['_token']);
        }
        $user = Auth::user();
        $attributes['user_id'] = $user->id;
        $attributes['timezone'] = $user->time_zone;
        $attributes['detail'] = !empty($attributes['detail']) ? $attributes['detail'] : null;
        $attributes['reseller'] = !empty($attributes['reseller']) ? $attributes['reseller'] : null;

        // encode json
        $json_data = json_encode($attributes);
        $result = $this->configurationRepo->updateConfiguration($json_data);
        return $this->success();
    }

    public function autoTriggerReport() {
        $configuration = $this->configurationRepo->fetchConfiguration();

        if(!empty($configuration)) {
            $value = json_decode($configuration->value);

            if ($value->detail != null) {
                $yesterday = Carbon::yesterday('UTC');
                $startDate = $yesterday->startOfDay()->format('Y-m-d H:i:s');
                $endDate = $yesterday->endOfDay()->format('Y-m-d H:i:s');
                $headerCSV = "'Service Provider' AS service_provider, 'Country' AS country, 'Network' AS network, 'Message Count' AS message_count, 'Total Cost' AS total_cost, 'Currency' AS currency";
                $path = config("constants.path_file_auto_trigger_report");
                $this->uploadServer->makeForder($path);
                $fileName = "Deatail_Report_" . $yesterday->format('d_m_Y'). ".csv";
                $pathFile = public_path($path) . $fileName;
                //$pathFile = '/var/www/html/abc/' . $fileName;
                $this->uploadServer->removeFile($path, $fileName);
                $this->configurationRepo->exportCSVDetail($startDate, $endDate, $headerCSV, $pathFile);
            }

            if($value->reseller != null) {
                $contentCSV = "Service Profile,Date,Account Balance";
                $yesterday = Carbon::yesterday('UTC');
                $serviceProviders = $this->serviceProviderService->fetchAll();
                $serviceProviders->each(function($item, $key) use(&$contentCSV, $yesterday) {
                    $arr_item_code = ["INFOBIP", "MESSAGEBIRD"];
                    if(in_array($item->code, $arr_item_code)) {
                        $data = $this->smsService->getBalance($item->code);
                        $contentCSV .= PHP_EOL . $item->name ."," . $yesterday->format('d-M-Y'). ",". $data->getBalance() . " " . $data->getCurrency();
                    }
                });
                $pathFile= config("constants.path_file_auto_trigger_report");
                $this->uploadServer->makeForder($pathFile);
                $fileName = "Reseller_Report_" . $yesterday->format('d_m_Y');
                $this->uploadServer->removeFile($pathFile, $fileName);
                $this->uploadServer->saveFileCSV($pathFile, "", $contentCSV, $fileName);
            }

            return true;
        }

        return false;
    }

    public function sendEmailReport() {
        $configuration = $this->configurationRepo->fetchConfiguration();

        if(!empty($configuration)) {
            $value = json_decode($configuration->value);
            $now = Carbon::now($value->timezone)->format("H:i");

            if($now == $value->time) {
                $yesterday = Carbon::yesterday('UTC');
                $lastFileDate = clone $yesterday;
                $arrFile = [];
                $path = config("constants.path_file_auto_trigger_report");
                //$pathFile = '/var/www/html/abc/';
                $fileDetailOld = "Deatail_Report_" . $lastFileDate->subDays(1)->format('d_m_Y') . ".csv";
                $fileResellerOld = "Reseller_Report_" . $lastFileDate->subDays(1)->format('d_m_Y') . ".csv";

                $fileDetail = "Deatail_Report_" . $yesterday->format('d_m_Y') . ".csv";
                $fileReseller = "Reseller_Report_" . $yesterday->format('d_m_Y') . ".csv";
                
                // remove file old
                $this->uploadServer->removeFile($path, $fileDetailOld);
                $this->uploadServer->removeFile($path, $fileResellerOld);

                if(!!$this->uploadServer->checkFile($path . $fileDetail)) {
                    array_push($arrFile, public_path($path . $fileDetail));
                }

                if(!!$this->uploadServer->checkFile($path . $fileReseller)) {
                    array_push($arrFile, public_path($path . $fileReseller));
                }
                
                if(count($arrFile) > 0) {
                    $title = 'Auto Report';
                    $content = "Auto report";
                    $objectContent = (object) array("content" => $content);
                    $templateEmailObj = new ExportCenter( $title, $objectContent, $arrFile);

                    $emailsList = explode( ';', $value->email );
                    $this->mailService->notifyMail( $emailsList, $templateEmailObj);

                    return true;
                }
            }

        }

        return false;
    }
}
