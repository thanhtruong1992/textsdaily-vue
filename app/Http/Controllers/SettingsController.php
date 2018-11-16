<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Settings\IServiceProviderService;
use App\Services\Settings\IPreferredServiceProviderService;
use Illuminate\Http\Request;
use Validator;
use App\Services\Clients\IClientService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Account2SettingRequest;
use App\Http\Requests\AccountSettingRequest;
use App\Http\Requests\WhiteLabelSettingRequest;
use App\Services\Settings\IMCCMNCService;
use App\Services\Settings\ICountryService;
use App\Http\Requests\UpdateMCCMNCRequest;
use App\Services\Settings\IMobilePatternService;
use App\Http\Requests\UpdateMobilePatternRequest;
use App\Http\Requests\ReportUpdateRequest;
use App\Services\Settings\IConfigurationService;
use App\Services\Settings\IInboundConfigService;
use App\Services\Auth\IAuthenticationService;
use App\Http\Requests\UpdateInboundConfigRequest;
use App\Services\InboundMessages\IInboundMessagesService;

class SettingsController extends Controller {
    /**
     */
    protected $serviceProviderService;
    protected $preferredServiceProviderService;
    protected $clientService;
    protected $request;
    protected $mccmncService;
    protected $countryService;
    protected $configurationService;
    protected $mobilePatternService;
    protected $inboundConfigService;
    protected $authService;
    protected $inboundMessageService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
            IClientService $IClientService,
            IServiceProviderService $IServiceProvider,
            IPreferredServiceProviderService $IPreferredServiceProviderService,
            Request $request,
            IMCCMNCService $IMCCMNCService,
            ICountryService $ICountryService,
            IConfigurationService $IConfigurationService,
            IMobilePatternService $IMobilePatternService,
            IInboundConfigService $IInboundConfigService,
            IAuthenticationService $IAuthenticationService,
            IInboundMessagesService $IInboundMessagesService) {

        $this->clientService = $IClientService;
        $this->serviceProviderService = $IServiceProvider;
        $this->preferredServiceProviderService = $IPreferredServiceProviderService;
        $this->request = $request;
        $this->mccmncService = $IMCCMNCService;
        $this->countryService = $ICountryService;
        $this->configurationService = $IConfigurationService;
        $this->mobilePatternService = $IMobilePatternService;
        $this->inboundConfigService = $IInboundConfigService;
        $this->authService = $IAuthenticationService;
        $this->inboundMessageService = $IInboundMessagesService;
    }

    public function settingAccount() {
        $user = Auth::user();
        $country_list = $this->clientService->getCountry ();
        $time_zone = $this->clientService->getTimeZone ();
        $language = $this->clientService->getDefaultLanguage ();
        $allServiceProvider = $this->serviceProviderService->fetchAllOptions ();
        $defaultServiceProvider = $this->serviceProviderService->getDefaultServiceProvider();
        return view('admins.settings.accounts.account',[
                'client' => $user,
                'countries' => $country_list,
                'timeZone' => $time_zone,
                'languages' => $language,
                'allServiceProvider' => $allServiceProvider,
                'defaultServiceProvider' => $defaultServiceProvider,
        ]);
    }

    public function updateAccount(AccountSettingRequest $request) {
        $result1 = $this->clientService->updateAccountSetting(Auth::user()->id, $request->all());
        $result2 = $this->serviceProviderService->setDefaultServiceProvider($request['default_service_provider']);

        if ($result1 && $result2) {
            Session::flash ( 'success', Lang::get ( 'notify.updated_success' ) );
        } else {
            Session::flash ( 'error', Lang::get ( 'notify.updated_failed' ) );
        }

        return redirect(route('setting.account'));
    }

    public function updateAccountGroup2(Account2SettingRequest $request) {
        $result1 = $this->clientService->updateAccountSetting(Auth::user()->id, $request->all());

        if ($result1) {
            Session::flash ( 'success', Lang::get ( 'notify.updated_success' ) );
        } else {
            Session::flash ( 'error', Lang::get ( 'notify.updated_failed' ) );
        }

        return redirect(route('setting2.account'));
    }

    public function settingWhiteLabel() {
        $user = Auth::user();
        return view('admins.settings.whitelabels.white-label',[
                'client' => $user,
        ]);
    }

    public function updateWhiteLabel(WhiteLabelSettingRequest $request) {
        $result = $this->clientService->updateWhiteLabelSetting(Auth::user()->id, $request);
        if ($result) {
            Session::flash ( 'success', Lang::get ( 'notify.updated_success' ) );
        } else {
            Session::flash ( 'error', Lang::get ( 'notify.updated_failed' ) );
        }
        return redirect(route(Auth::user()->isGroup1() ? 'setting.whitelabel' : 'setting2.whitelabel'));
    }

    public function settingReport() {
        $user = Auth::user();
        $attributes = $this->configurationService->fetchConfiguration();
        return view('admins.settings.reports.report-setting', [
                'client' => $user,
                'email' => $attributes['email'],
                'time' => $attributes['time'],
                'reseller' => $attributes['reseller'],
                'detail' => $attributes['detail'],
        ]);
    }

    public function settingReportUpdate(ReportUpdateRequest $request) {
        $result = $this->configurationService->updateConfiguration($request->all());
        if (!!$result->status) {
            Session::flash ( 'success', Lang::get ( 'notify.updated_success' ) );
        } else {
            Session::flash ( 'error', Lang::get ( 'notify.updated_failed' ) );
        }
        return redirect(route('settings.report'));
    }

    public function settingMCCMNC() {
        $user = Auth::user();
        return view('admins.settings.mncmccs.index', [
                'client' => $user,
        ]);
    }

    public function ajaxGetMCCMNC() {
        return $this->mccmncService->getAllDataFormatDataTable($this->request);
    }

    public function deleteMCCMNC(Request $request) {
        try {
            $result = $this->mccmncService->deleteMCCMNC ( $request->id );

            if (! $result->status) {
                throw new Exception ();
            }

            return Lang::get ( 'settings.deleteDataSuccess' );
        } catch ( \Exception $e ) {
            return response ()->json ( [
                    "message" => Lang::get ( 'settings.deleteDataError' )
            ], 404 );
        }
    }

    public function editMCCMNC( $id ) {
        $user = Auth::user();
        $mccmnc = $this->mccmncService->getMCCMNC($id);
        $country = $this->countryService->findByCode($mccmnc->country);
        return view('admins.settings.mncmccs.edit', [
                'client' => $user,
                'mccmnc' => $mccmnc,
                'country' => $country
        ]);
    }

    public function updateMCCMNC( UpdateMCCMNCRequest $request, $id ) {
        $mccmnc = $this->mccmncService->getMCCMNC($id);
        if ($mccmnc) {
            $mccmnc = $this->mccmncService->updateMCCMNC($request->all(), $id);
            // Update Country
            if ($mccmnc) {
                $this->countryService->updateCountryByCode(['name' => $request->country_name], $mccmnc->country);
                Session::flash ( 'success', Lang::get ( 'settings.UpdateSuccessfully' ) );
            } else {
                Session::flash ( 'error', Lang::get ( 'settings.updateDataError' ) );
            }
        }
        return redirect ()->route ('settings.mnc-mcc');
    }

    public function getSampleMCCMNC() {
        $sample_template_file = public_path('/'. config('constants.path_sample_mnc_mcc_template'));
        $headers = array(
                'Content-Type' => 'text/csv',
        );
        return Response::download($sample_template_file, 'mcc-mnc-table.csv', $headers);
    }

    /**
     * @return json
     */
    public function ajaxUploadMCCMNC() {
        ini_set ( 'memory_limit', '-1' );
        $file = $this->request->file ( 'fileUpload' );
        //
        if ($file) {
            // Validation
            $validator = Validator::make ( $this->request->all (), [
                    'fileUpload' => 'required|mimetypes: text/csv,text/plain'
            ] );

            if ($validator->fails ()) {
                return json_encode ( array (
                        'status' => false,
                        'msg' => trans ( 'settings.fileUploadInvalid' )
                ) );
            }

            $result = $this->mccmncService->importMCCMNC( $file );
            if ($result) {
                return json_encode ( array (
                        'status' => true,
                        'msg' => trans ( 'settings.fileUploadSuccessfully' )
                ) );
            } else {
                return json_encode ( array (
                        'status' => false,
                        'msg' => trans ( 'settings.fileUploadFailed' )
                ) );
            }
        } else {
            return json_encode ( array (
                    'status' => false,
                    'msg' => trans ( 'settings.fileUploadNotFound' )
            ) );
        }
    }

    /**
     */
    public function serviceProvider() {
        $user = Auth::user();
        $allServiceProvider = $this->serviceProviderService->fetchAllOptions ();
        $preferredServiceProviderData = $this->preferredServiceProviderService->fetchAllGroupByCountry ();

        $allCountries = $this->serviceProviderService->getCountry ();
        //
        return view ( 'admins.settings.service-provider.index', [
                'client' => $user,
                'allServiceProvider' => $allServiceProvider,
                'preferredServiceProviderData' => $preferredServiceProviderData,
                'allCountries' => $allCountries
        ] );
    }

    public function getSampleServieProvider() {
        $sample_template_file = public_path('/'. config('constants.path_sample_service_provider_template'));
        $headers = array(
                'Content-Type' => 'text/csv',
        );
        return Response::download($sample_template_file, 'preferred_service_provider.csv', $headers);
    }

    /**
     */
    public function ajaxGetPreferredServiceProvider() {
        $country = $this->request->input ( 'country' );
        $network = $this->request->input ( 'network' );
        if ($country && $network) {
            $serviceProvider = $this->preferredServiceProviderService->getServiceProviderByCountryNetwork ( $country, $network );
            return json_encode ( array (
                    'status' => true,
                    'data' => $serviceProvider,
                    'msg' => null
            ) );
        } else {
            return json_encode ( array (
                    'status' => false,
                    'data' => null,
                    'msg' => trans ( 'settings.CountryNetworkNotFound' )
            ) );
        }
    }

    /**
     */
    public function ajaxSaveServiceProvider() {
        $country = $this->request->input ( 'country' );
        $network = $this->request->input ( 'network' );
        $serviceProvider = $this->request->input ( 'service_provider' );
        if ($country && $network && $serviceProvider) {
            $serviceProviderOld = $this->preferredServiceProviderService->getServiceProviderByCountryNetwork ( $country, $network );
            $serviceProviderLatest = $this->preferredServiceProviderService->updateServiceProvider ( [
                    'service_provider' => $serviceProvider
            ], $serviceProviderOld->id );
            return json_encode ( array (
                    'status' => true,
                    'msg' => trans ( 'settings.UpdateSuccessfully' )
            ) );
        } else {
            return json_encode ( array (
                    'status' => false,
                    'msg' => trans ( 'settings.CountryNetworkServiceProviderNotFound' )
            ) );
        }
    }

    /**
     */
    public function ajaxUploadServiceProvider() {
        ini_set ( 'memory_limit', '-1' );
        $file = $this->request->file ( 'fileUpload' );
        //
        if ($file) {
            // Validation
            $validator = Validator::make ( $this->request->all (), [
                    'fileUpload' => 'required|mimetypes: text/csv,text/plain'
            ] );

            if ($validator->fails ()) {
                return json_encode ( array (
                        'status' => false,
                        'msg' => trans ( 'settings.fileUploadInvalid' )
                ) );
            }

            $result = $this->preferredServiceProviderService->importServiceProvider ( $file );
            if ($result) {
                return json_encode ( array (
                        'status' => true,
                        'msg' => trans ( 'settings.fileUploadSuccessfully' )
                ) );
            } else {
                return json_encode ( array (
                        'status' => false,
                        'msg' => trans ( 'settings.fileUploadFailed' )
                ) );
            }
        } else {
            return json_encode ( array (
                    'status' => false,
                    'msg' => trans ( 'settings.fileUploadNotFound' )
            ) );
        }
    }

    public function settingMobilePattern() {
        $user = Auth::user();
        return view('admins.settings.mobile-pattern.index', [
                'client' => $user,
        ]);
    }

    public function getSampleMobilePattern() {
        $sample_template_file = public_path('/'. config('constants.path_sample_mobile_pattern_template'));
        $headers = array(
                'Content-Type' => 'text/csv',
        );
        return Response::download($sample_template_file, 'mobile-pattern-table.csv', $headers);
    }

    public function ajaxGetMobilePattern() {
        return $this->mobilePatternService->getAllDataFormatDataTable($this->request);
    }

    public function deleteMobilePattern(Request $request) {
        try {
            $result = $this->mobilePatternService->deleteMobilePattern( $request->id );

            if (! $result->status) {
                throw new Exception ();
            }

            return Lang::get ( 'settings.deleteDataSuccess' );
        } catch ( \Exception $e ) {
            return response ()->json ( [
                    "message" => Lang::get ( 'settings.deleteDataError' )
            ], 404 );
        }
    }

    public function editMobilePattern( $id ) {
        $user = Auth::user();
        $mobilePattern = $this->mobilePatternService->getMobilePattern($id);
        $country = $this->countryService->findByCode($mobilePattern->country);
        return view('admins.settings.mobile-pattern.edit', [
                'client' => $user,
                'mobilePattern' => $mobilePattern,
                'country' => $country
        ]);
    }

    public function updateMobilePattern( UpdateMobilePatternRequest $request, $id ) {
        $mobilePattern = $this->mobilePatternService->getMobilePattern($id);
        if ($mobilePattern) {
            $mobilePattern = $this->mobilePatternService->updateMobilePattern($request->all(), $id);
            // Update Country
            if ($mobilePattern) {
                $this->countryService->updateCountryByCode(['name' => $request->country_name], $mobilePattern->country);
                Session::flash ( 'success', Lang::get ( 'settings.UpdateSuccessfully' ) );
            } else {
                Session::flash ( 'error', Lang::get ( 'settings.updateDataError' ) );
            }
        }
        return redirect ()->route ('settingMobilePattern');
    }

    /**
     * @return json
     */
    public function ajaxUploadMobilePattern() {
        ini_set ( 'memory_limit', '-1' );
        $file = $this->request->file ( 'fileUpload' );
        //
        if ($file) {
            // Validation
            $validator = Validator::make ( $this->request->all (), [
                    'fileUpload' => 'required|mimetypes: text/csv,text/plain'
            ] );

            if ($validator->fails ()) {
                return json_encode ( array (
                        'status' => false,
                        'msg' => trans ( 'settings.fileUploadInvalid' )
                ) );
            }

            $result = $this->mobilePatternService->importMobilePattern( $file );
            if ($result) {
                return json_encode ( array (
                        'status' => true,
                        'msg' => trans ( 'settings.fileUploadSuccessfully' )
                ) );
            } else {
                return json_encode ( array (
                        'status' => false,
                        'msg' => trans ( 'settings.fileUploadFailed' )
                ) );
            }
        } else {
            return json_encode ( array (
                    'status' => false,
                    'msg' => trans ( 'settings.fileUploadNotFound' )
            ) );
        }
    }

    public function settingInboundConfig() {
        $user = Auth::user();
        return view('admins.settings.inbound-config.index', [
                'client' => $user,
        ]);
    }

    public function ajaxGetInboundConfig() {
        return $this->inboundConfigService->getAllDataFormatDataTable($this->request);
    }

    public function editInboundConfig( $id ) {
        $user = Auth::user();
        $inboundConfig = $this->inboundConfigService->getInboundConfig($id);
        $allUsers = $this->authService->getChildrens($user->id);
        return view('admins.settings.inbound-config.edit', [
                'client' => $user,
                'inboundConfig' => $inboundConfig,
                'allUsers' => $allUsers
        ]);
    }

    public function updateInboundConfig( UpdateInboundConfigRequest $request, $id ) {
        $user = Auth::user();
        $inboundConfig = $this->inboundConfigService->getInboundConfig($id);
        $redirectRoute = 'inboundConfig';
        if ($inboundConfig) {
            if ( $user->type == 'GROUP1' ) {
                $request['group2_user_id'] = $request->get('user_id');
            } elseif ( $user->type == 'GROUP2' ) {
                $request['group3_user_id'] = $request->get('user_id');
                $redirectRoute = 'inboundConfigClient';
            }
            $inboundConfig = $this->inboundConfigService->updateInboundConfig($request->all(), $id);
            // Update Country
            if ($inboundConfig) {
                Session::flash ( 'success', Lang::get ( 'settings.UpdateSuccessfully' ) );
            } else {
                Session::flash ( 'error', Lang::get ( 'settings.updateDataError' ) );
            }
        } else {
            Session::flash ( 'error', Lang::get ( 'settings.updateDataError' ) );
        }
        return redirect ()->route ($redirectRoute);
    }

    public function unassignInboundConfig() {
        try {
            $user = Auth::user();
            //
            $id = $this->request->get('id');
            $inboundConfig = $this->inboundConfigService->getInboundConfig($id);
            if ($inboundConfig) {
                $params = [
                        'keyworks' => null,
                        'group3_user_id' => null
                ];
                if ( $user->type == 'GROUP1' ) {
                    $params['group2_user_id'] = null;
                }
                $inboundConfig = $this->inboundConfigService->updateInboundConfig($params, $id);
                // Update Country
                if ($inboundConfig) {
                    return Lang::get ( 'settings.UpdateSuccessfully' );
                } else {
                    throw new Exception ();
                }
            } else {
                throw new Exception ();
            }
        } catch ( \Exception $e ) {
            return response ()->json ( [
                    "message" => Lang::get ( 'settings.updateDataError' )
            ], 404 );
        }

    }

    public function inboundMessages() {
        $user = Auth::user();
        $timeZone = $this->inboundMessageService->getTimeZone();
        return view('admins.inbound-messages.index', [
                'client' => $user,
                'timeZone' => $timeZone
        ]);
    }

    public function ajaxGetInboundMessages()
    {
        return $this->inboundMessageService->getAllDataFormatDataTable($this->request);
    }

    /**
     * fn export inbound message
     * @return string|unknown
     */
    public function exportInboundMessage() {
        $result = $this->inboundMessageService->exportCSVInboundMessage($this->request);

        if(!$result->status) {
            return "Export error!";
        }

        $fileName = "Inbound_Message_" . time() . ".csv";
        $headers = array(
                "Content-Type: application/csv"
        );

        return Response::download($result->data, $fileName, $headers)->deleteFileAfterSend(true);
    }
}
