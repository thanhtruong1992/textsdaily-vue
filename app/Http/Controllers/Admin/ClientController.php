<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\Auth\IAuthenticationService;
use App\Services\Clients\IClientService;
use App\Http\Requests\CreateClientGroup3Request;
use App\Http\Requests\CreateClientRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Lang;
use App\Http\Requests\UpdateClientRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateReaderRequest;
use App\Http\Requests\UpdateReaderRequest;
use App\Services\SubscriberLists\ISubscriberListService;
use App\Services\Settings\IPreferredServiceProviderService;
use App\Services\Clients\IPriceConfigurationService;
use App\Services\Campaign\ICampaignService;
use File;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use Validator;

class ClientController extends BaseController {

    /**
     */
    protected $request;
    protected $clientService;
    protected $subscriberListService;
    protected $preferredServiceProviderService;
    protected $priceConfigurationService;
    protected $authService;
    protected $campaignService;

    /**
     *
     * @param Request $request
     * @param IClientService $IClientService
     * @param ISubscriberListService $ISubscriberListService
     */
    public function __construct(
            Request $request,
            IClientService $IClientService,
            ISubscriberListService $ISubscriberListService,
            IAuthenticationService $IAuthService,
            IPreferredServiceProviderService $IPreferredServiceProviderService,
            IPriceConfigurationService $IPriceConfigurationService,
            ICampaignService $ICampaignService) {
        $this->request = $request;
        $this->clientService = $IClientService;
        $this->subscriberListService = $ISubscriberListService;
        $this->authService = $IAuthService;
        $this->preferredServiceProviderService = $IPreferredServiceProviderService;
        $this->priceConfigurationService = $IPriceConfigurationService;
        $this->campaignService = $ICampaignService;
    }

    // load view index client list
    public function index() {
        try {
            $clients = $this->clientService->getAllClientUser ();
            return $this->success($clients);
        } catch(\Exception $e) {
            return $this->badRequest($e->getMessage());
        }
    }

    // load view create account
    public function create() {
        $userId = Auth::user()->id;
        $country_list = $this->clientService->getCountry ();
        $time_zone = $this->clientService->getTimeZone ();
        $language = $this->clientService->getDefaultLanguage ();
        $currency = $this->clientService->getCurrency ();
        $senderObject = $this->authService->getSenderList($userId);
        $billing_type = $this->getAccountTypeByUser(Auth::user());

        return view ( 'admins.clients.accounts.account', [
                'client' => null,
                'countries' => $country_list,
                'timeZone' => $time_zone,
                'languages' => $language,
                'senderList' => array_reverse( (array)$senderObject),
                'account_type' => $billing_type,
                'currencies' => $currency
        ] );
    }

    private function getAccountTypeByUser($user) {
        switch ($user->billing_type) {
            case 'UNLIMITED':
                return ['ONE_TIME', 'MONTHLY', 'UNLIMITED'];
            case 'MONTHLY':
                return ['ONE_TIME', 'MONTHLY'];
            case 'ONE_TIME':
                return ['ONE_TIME'];
        }
    }

    // load view create account reader
    public function createReader() {
        $clients = $this->clientService->getAllClientUser ( true );
        return view ( 'admins.clients.accounts.reader', [
                'client' => $clients
        ] );
    }
    public function updateBilling() {
        return view ( 'admins.clients.accounts.billing', [
                'client' => null
        ] );
    }
    public function infoBilling($id) {
        $client = $this->clientService->getClientById ( $id );
        $max_total_limit = null;
        if ($client->billing_type == "MONTHLY" && Auth::user()->billing_type == "MONTHLY") {
            $max_total_limit = Auth::user()->credits_limit - $this->clientService->getMaxTotalLimitedOfChild($client);
        }
//         dd([Auth::user()->credits_limit, $this->clientService->getMaxTotalLimitedOfChild($client)]);
        return view ( 'admins.clients.accounts.billing', [
                'max_limit' => $max_total_limit,
                'client' => $client
        ] );
    }

    // store new account group 2
    public function store(CreateClientRequest $request) {
        $new_user = $this->clientService->createNewClient ( $request->all() );
        if ($new_user) {
            Session::flash ( 'success', Lang::get ( 'notify.new_client_success' ) );
        } else {
            Session::flash ( 'error', Lang::get ( 'notify.new_client_error' ) );
        }
        return redirect ()->route ( 'clients.info', [
                'id' => $new_user->id
        ] );
    }

    // store new account group 3
    public function storeGroup3(CreateClientGroup3Request $request) {
        $new_user = $this->clientService->createNewClientGroup3 ( $request );
        if ($new_user) {
            Session::flash ( 'success', Lang::get ( 'notify.new_client_success' ) );
            $this->subscriberListService->createGlobalSupperssionList ( $new_user->id );
            $this->subscriberListService->createInvalidEntriesList($new_user->id);
        } else {
            Session::flash ( 'error', Lang::get ( 'notify.new_client_error' ) );
        }
        return redirect ()->route ( 'clients2.info', [
                'id' => $new_user->id
        ] );
    }
    // store new account reader group 3
    public function storeReaderGroup3(CreateReaderRequest $request) {
        // dd($request);
        $new_reader = $this->clientService->createNewReader ( $request->toArray () );
        if ($new_reader) {
            Session::flash ( 'success', Lang::get ( 'notify.new_reader_success' ) );
        } else {
            Session::flash ( 'error', Lang::get ( 'notify.new_reader_error' ) );
        }
        return redirect ()->route ( 'clients2.index' );
    }

    // get information from client
    public function info($id) {
        $userId = Auth::user()->id;
        $client = $this->clientService->getClientById ( $id );
        $country_list = $this->clientService->getCountry ();
        $time_zone = $this->clientService->getTimeZone ();
        $language = $this->clientService->getDefaultLanguage ();
        $senderObject = $this->authService->getSenderList($userId);
        $currency = $this->clientService->getCurrency ();
        $billing_type = Auth::user()->getAccountTypeOption();
        return view ( 'admins.clients.accounts.account-update', [
                'client' => $client,
                'countries' => $country_list,
                'timeZone' => $time_zone,
                'languages' => $language,
                'senderList' => array_reverse( (array)$senderObject),
                'account_type' => $billing_type,
                'currencies' => $currency
        ] );
    }
    // get information from client
    public function infoReader($id) {
        $reader = $this->clientService->getClientById ( $id );
        $clients = $this->clientService->getAllClientUser ( true );
        return view ( 'admins.clients.accounts.reader-update', [
                'client' => $clients,
                'reader' => $reader
        ] );
    }

    // update client with new information
    public function update(UpdateClientRequest $request) {
        $user_updated = $this->clientService->updateClient ( $request->id, $request->toArray () );
        if ($user_updated) {
            Session::flash ( 'success', Lang::get ( 'notify.updated_success' ) );
        } else {
            Session::flash ( 'error', Lang::get ( 'notify.updated_failed' ) );
        }
        if (Auth::user ()->isGroup1 ()) {
            return redirect ()->route ( 'clients.info', [
                    'id' => $user_updated->id
            ] );
        } else {
            return redirect ()->route ( 'clients2.info', [
                    'id' => $user_updated->id
            ] );
        }
    }
    // update client with new information
    public function updateReader(UpdateReaderRequest $request) {
        $user_updated = $this->clientService->updateClient ( $request->id, $request->toArray () );
        if ($user_updated) {
            Session::flash ( 'success', Lang::get ( 'notify.updated_success' ) );
        } else {
            Session::flash ( 'error', Lang::get ( 'notify.updated_failed' ) );
        }
        return redirect ()->route ( 'clients2.info-reader', [
                'id' => $user_updated->id
        ] );
    }

    // API delete client
    public function delete(Request $request) {
        try {
            $result = $this->clientService->deleteClient($request->get('list_id'));
            if ($result['status']) {
                return $this->accept(Lang::get('notify.delete_success'));
            }

            return $this->badRequest($result['error']);
        } catch(\Exception $e) {
            return $this->badRequest($e->getMessage());
        }
    }

    // API update status of client
    public function updateStatusClient(Request $request) {
        $result = $this->clientService->updateStatusClien ( $request->list_id, $request->status );
        if ($result) {
            Session::flash ( 'success', Lang::get ( 'notify.updated_success' ) );
        } else {
            Session::flash ( 'error', Lang::get ( 'notify.updated_failed' ) );
        }
        return [
                'status' => $result
        ];
    }

    /**
     *
     * @param int $clientId
     */
    public function priceConfig($clientId) {
        $client = $this->clientService->getClientById ( $clientId );
        //
        $currentUser = Auth::user();
        if ( $currentUser->type != 'GROUP1' ){
            $enabledCountriesByCurrentUser = [];
            $enabledCountriesByCurrentUser = $this->priceConfigurationService->enabledCountries($client->parent_id);
            $preferredServiceProviderData = $this->preferredServiceProviderService->fetchAllCountryEnabled( $enabledCountriesByCurrentUser );
        }else {
            $preferredServiceProviderData = $this->preferredServiceProviderService->fetchAllGroupByCountry();
        }
        $priceConfigData = $this->priceConfigurationService->fetchAllGroupByCountry($clientId);
        $priceConfigData = collect($priceConfigData)->where('disabled', 0)->all();
        $allCountries = $this->preferredServiceProviderService->getCountry ();
        $dataEnabled = [];
        $dataDisabled = [];
        $data = [];
        foreach ($preferredServiceProviderData as $key => $item) {
            if(isset($priceConfigData[$key]) && isset($priceConfigData[$key]['disabled']) && $priceConfigData[$key]['disabled'] == 0) {
                $dataEnabled[$key] = $preferredServiceProviderData[$key];
            }else if(empty($priceConfigData[$key]) || empty($priceConfigData[$key]['disabled']) || $priceConfigData[$key]['disabled'] == 1) {
                $dataDisabled[$key] = $preferredServiceProviderData[$key];
            }

            $data[$key] = $preferredServiceProviderData[$key];
        }
        //
        return view ( 'admins.clients.price-config.index', [
                'allData' => $data,
                'client' => $client,
                'dataEnabled' => $dataEnabled,
                'dataDisabled' => $dataDisabled,
                'allCountries' => $allCountries
        ] );
    }

    /**
     */
    public function ajaxGetPriceConfiguration() {
        //
        $idClient = $this->request->input ( 'client_id' );
        $country = $this->request->input ( 'country' );
        $network = $this->request->input ( 'network', null );
        if ($country) {
            $priceConfiguration = $this->priceConfigurationService->getPriceConfigurationByCountryNetwork ( $idClient, $country, $network );
            return json_encode ( array (
                    'status' => true,
                    'data' => $priceConfiguration,
                    'msg' => null
            ) );
        } else {
            return json_encode ( array (
                    'status' => false,
                    'data' => null,
                    'msg' => trans ( 'client.CountryNetworkNotFound' )
            ) );
        }
    }

    /**
     */
    public function ajaxSavePriceConfiguration() {
        $idClient = $this->request->get ( 'client_id' );
        $country = $this->request->get ( 'country' );
        $network = $this->request->get ( 'network', null );
        $price = $this->request->get ( 'price', 0 );
        $disable = $this->request->get ( 'disable', 0 );
        if ( $idClient && isset($country) ) {
            $priceConfigurationOld = $this->priceConfigurationService->getPriceConfigurationByCountryNetwork ( $idClient, $country, $network );
            // Params data
            $params = array();
            $params['price'] = $price ? $price : 0;
            $params['disabled'] = $disable;
            // Process save data
            if ( $priceConfigurationOld ) {
                $priceConfigurationLatest = $this->priceConfigurationService->updatePriceConfiguration ( $idClient, $params, $priceConfigurationOld->id );
                return json_encode ( array (
                        'status' => true,
                        'msg' => trans ( 'client.UpdateSuccessfully' )
                ) );
            } else {
                // Add more data for create case
                $params['country'] = $country;
                $params['network'] = $network;
                //
                $priceConfigurationLatest = $this->priceConfigurationService->createPriceConfiguration ( $idClient, $params );
                return json_encode ( array (
                        'status' => true,
                        'msg' => trans ( 'client.CreateSuccessfully' )
                ) );
            }
        } else {
            return json_encode ( array (
                    'status' => false,
                    'msg' => trans ( 'client.CountryNetworkNotFound' )
            ) );
        }
    }

    // API add credit
    public function addCredit(Request $request) {
        $result = $this->clientService->addCredit($request->client_id, $request->credit, $request->description);
        if ($result['status']) {
            Session::flash ( 'success', Lang::get ( 'notify.updated_success' ) );
        } else {
            Session::flash ( 'error', $result['error'] );
        }
        return [
                'status' => $result
        ];
    }
    // API withdraw credit
    public function withdrawCredit(Request $request) {
        $result = $this->clientService->withdrawCredit($request->client_id, $request->credit, $request->description);
        if ($result['status']) {
            Session::flash ( 'success', Lang::get ( 'notify.updated_success' ) );
        } else {
            Session::flash ( 'error', $result['error'] );
        }
        return [
                'status' => $result
        ];
    }
    // API increase credit limit
    public function increaseCredit(Request $request) {
        $result = $this->clientService->updateCreditLimit($request->client_id, $request->credit, $request->description);
        if ($result['status']) {
            Session::flash ( 'success', Lang::get ( 'notify.updated_success' ) );
        } else {
            Session::flash ( 'error', $result['message'] );
        }
        return [
                'status' => $result
        ];
    }

    // API descrease credit limit
    public function descreaseCredit(Request $request) {
        $result = $this->clientService->updateCreditLimit($request->client_id, $request->credit, $request->description, true);
        if ($result['status']) {
            Session::flash ( 'success', Lang::get ( 'notify.updated_success' ) );
        } else {
            Session::flash ( 'error', $result['message'] );
        }
        return [
                'status' => $result
        ];
    }
    // API monthly transfer credit
    public function transferCreditsMonthly() {
        $allGroup = $this->authService->getAllMonthlyUserAvailable();
        $end_month = Carbon::now()->endOfMonth();
        foreach ( $allGroup as $user ) {
            $minute = Carbon::now($user->time_zone)->setTimezone('UTC')->diffInMinutes($end_month);
            if ($user->billing_type == "MONTHLY" && $minute <= 5) {
                $result = $this->clientService->transferCreditMonthly($user);
            }
        }
    }

    /**
     * show logo
     * @return unknown
     */
    public function responseLogo() {
        $user = Auth::user();
        if(!!$user->isGroup3()) {
            $userParent = $user->parentUser()->first();
            $pathFile = public_path(config("constants.path_file_logo") . md5($userParent->id) . "/" . $userParent->avatar);
        }else {
            $pathFile = public_path(config("constants.path_file_logo") . md5($user->id) . "/" . $user->avatar);
        }

        // check file
        if (!File::exists ( $pathFile)) {
            $pathFile = public_path("/images/textdaily.png");
        }

        $file = File::get($pathFile);
        $type = File::mimeType($pathFile);
        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);
        return $response;
    }

    public function downloadTemplacePriceConfig() {
        $sample_template_file = public_path('/'. config('constants.path_sample_price_config_template'));
        $headers = array(
                'Content-Type' => 'text/csv',
        );
        return Response::download($sample_template_file, 'price_config_template.csv', $headers);
    }

    /**
     * @return json
     */
    public function ajaxUploadPriceConfiguration($idClient) {
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

            $result = $this->priceConfigurationService->importPriceConfiguration( $idClient, $file );
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
     * fn check username already exist
     * @return unknown
     */
    public function checkUsername() {
        try {
            $result = $this->authService->checkUsername($this->request);
            if(!$result->status) {
                return response()->json($result->error, 400);
            }

            return response()->json('', 200);
        }catch (\Exception $e) {
            return response()->json('', 500);
        }
    }

    // load view create api account
    public function createApiAccount() {
        $userId = Auth::user()->id;
        $country_list = $this->clientService->getCountry ();
        $time_zone = $this->clientService->getTimeZone ();
        $language = $this->clientService->getDefaultLanguage ();
        $currency = $this->clientService->getCurrency ();
        $senderObject = $this->authService->getSenderList($userId);
        $billing_type = $this->getAccountTypeByUser(Auth::user());

        return view ( 'admins.clients.accounts.account-api', [
                'client' => null,
                'countries' => $country_list,
                'timeZone' => $time_zone,
                'languages' => $language,
                'senderList' => array_reverse( (array)$senderObject),
                'account_type' => $billing_type,
                'currencies' => $currency
        ] );
    }

    // store new api account group 3
    public function storeApiAccountGroup3(CreateClientGroup3Request $request){
        $new_user = $this->clientService->createApiAccount ( $request );
        if ($new_user) {
            // create campaign api account
            $result = $this->campaignService->createCampaignApiAccount ($new_user->id);
            if($result) {
                Session::flash ( 'success', Lang::get ( 'notify.new_client_success' ) );
            } else {
                Session::flash ( 'error', Lang::get ( 'notify.new_client_error' ) );
            }
        } else {
            Session::flash ( 'error', Lang::get ( 'notify.new_client_error' ) );
        }
        return redirect ()->route ( 'clients2.info', [
                'id' => $new_user->id
        ] );
    }
}
?>