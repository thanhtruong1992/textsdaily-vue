<?php

namespace App\Services\Clients;

use App\Http\Requests\CreateClientGroup3Request;
use App\Http\Requests\CreateClientRequest;
use App\Repositories\Clients\IClientRepository;
use App\Services\BaseService;
use App\Services\UploadService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;

class ClientService extends BaseService implements IClientService {
    protected $clientRepo;
    protected $uploadFile;
    public function __construct(IClientRepository $clientRepo, UploadService $uploadFile) {
        $this->clientRepo = $clientRepo;
        $this->uploadFile = $uploadFile;
    }

    /**
     * fn get all children of user
     * {@inheritDoc}
     * @see \App\Services\Clients\IClientService::getAllClientUser()
     */
    public function getAllClientUser(bool $for_reader_select = false) {
        $result = $this->clientRepo->getAllClient ( $for_reader_select );
        foreach ( $result as $client ) {
            $country_code = $client->country;
            $time_zone_code = $client->time_zone;
            $client->country = $this->getCountry ( $country_code );
            $client->time_zone = $this->getTimeZone ( $time_zone_code );
        }
        return $result;
    }

    /**
     * create new user group 2
     * {@inheritDoc}
     * @see \App\Services\Clients\IClientService::createNewClient()
     */
    public function createNewClient($attribute) {
        $list_sender = array();
        foreach ($attribute['sender'] as $sender) {
            $list_sender[$sender] = $sender;
        }
        // encode json
        $json_data = json_encode($list_sender);
        $attribute ['sender'] = $json_data;
        $attribute ['agency_id'] = 1;
        $attribute ['parent_id'] = 1;
        $attribute ['status'] = "ENABLED";
        $attribute ['type'] = "GROUP2";
        $attribute ['encrypted'] = 0;
        $attribute ['blocked'] = 0;
        $attribute ['credits'] = 0;
        $attribute ['created_by'] = Auth::user ()->id;
        $attribute ['updated_by'] = Auth::user ()->id;
        $attribute ['default_price_sms'] = round(( float ) $attribute ['default_price_sms'], 2);
        $attribute ['password'] = Hash::make ( $attribute ['password'] );
        return $this->clientRepo->createNewClient ( $attribute );
    }

    /**
     * fn create new user group 3
     * {@inheritDoc}
     * @see \App\Services\Clients\IClientService::createNewClientGroup3()
     */
    public function createNewClientGroup3(CreateClientGroup3Request $request) {
        $list_sender = array();
        foreach ($request['sender'] as $sender) {
            $list_sender[$sender] = $sender;
        }
        // encode json
        $json_data = json_encode($list_sender);
        $request ['sender'] = $json_data;
        $request ['agency_id'] = 1;
        $request ['parent_id'] = Auth::user ()->id;
        $request ['status'] = "ENABLED";
        $request ['type'] = "GROUP3";
        $request ['blocked'] = 0;
        $request ['credits'] = 0;
        $request ['created_by'] = Auth::user ()->id;
        $request ['updated_by'] = Auth::user ()->id;
        $request ['currency'] = Auth::user ()->currency;
        $request ['default_price_sms'] = round(( float ) $request ['default_price_sms'], 2);
        $request ['password'] = Hash::make ( $request ['password'] );

        if (!isset($request ['is_tracking_link'])) {
            $request ['is_tracking_link'] = 0;
        }

        if (!isset($request ['encrypted'])) {
            $request ['encrypted'] = 0;
        }
        return $this->clientRepo->createNewClient ( $request->toArray (), true );
    }

    /**
     * create new user group 4
     * {@inheritDoc}
     * @see \App\Services\Clients\IClientService::createNewReader()
     */
    public function createNewReader(array $request) {
        $user = Auth::user ();
        $request ['agency_id'] = 1;
        $request ['parent_id'] = $user->id;
        $request ['status'] = "ENABLED";
        $request ['type'] = "GROUP4";
        $request ['country'] = $user->country;
        $request ['language'] = $user->language;
        $request ['time_zone'] = $user->time_zone;
        $request ['encrypted'] = 0;
        $request ['blocked'] = 0;
        $request ['credits'] = 0;
        $request ['currency'] = $user->currency;
        $request ['created_by'] = $user->id;
        $request ['updated_by'] = $user->id;
        $request ['password'] = Hash::make ( $request ['password'] );
        return $this->clientRepo->createNewClient ( $request );
    }

    /**
     * fn get user by id
     * {@inheritDoc}
     * @see \App\Services\Clients\IClientService::getClientById()
     */
    public function getClientById($id) {
        return $this->clientRepo->getClientById ( $id );
    }

    /**
     * fn delete user
     * {@inheritDoc}
     * @see \App\Services\Clients\IClientService::deleteClient()
     */
    public function deleteClient($list_id) {
        return $this->clientRepo->deleteClientById ( $list_id );

    }

    /**
     * fn update status of user
     * {@inheritDoc}
     * @see \App\Services\Clients\IClientService::updateStatusClien()
     */
    public function updateStatusClien($list_id, $status) {
        return $this->clientRepo->updateStatusClient ( $list_id, $status );
    }

    /**
     * fn update info of user
     * {@inheritDoc}
     * @see \App\Services\Clients\IClientService::updateClient()
     */
    public function updateClient($id, array $attributes) {
        if (isset($attributes ['username'])) { // unset email
            unset($attributes ['username']);
        }

        if (isset($attributes ['billing_type'])) { // uset billing_type, this field can be edit
            unset($attributes ['billing_type']);
        }

        if (isset ( $attributes ['currency'] )) { // unset currency
            unset ( $attributes ['currency'] );
        }
        unset ( $attributes ['password_confirmation'] ); // unset password_confirmation
        if ($attributes ['password'] == null) { // unset password if it's null
            unset ( $attributes ['password'] );
        } else {
            $attributes ['password'] = Hash::make ( $attributes ['password'] );
        }

        // Set sender only for group 1
        $list_sender = array();
        if(!empty($attributes['sender'])) {
            foreach ($attributes['sender'] as $sender) {
                $list_sender[$sender] = $sender;
            }
        }
        // encode json
        $json_data = json_encode($list_sender);
        $attributes ['sender'] = $json_data;

        if (!isset($attributes['is_tracking_link'])) {
            $attributes['is_tracking_link'] = 0;
        }

        if (!isset($attributes['encrypted'])) {
            $attributes['encrypted'] = 0;
        }

        return $this->clientRepo->update ( $attributes, $id );
    }

    /**
     * fn update account setting
     * {@inheritDoc}
     * @see \App\Services\Clients\IClientService::updateAccountSetting()
     */
    public function updateAccountSetting($id, array $attributes) {
        if (isset($attributes ['email'])) { // unset email
            unset($attributes ['email']);
        }

        unset ( $attributes ['password_confirmation'] ); // unset password_confirmation
        if ($attributes ['password'] == null) { // unset password if it's null
            unset ( $attributes ['password'] );
        } else {
            $attributes ['password'] = Hash::make ( $attributes ['password'] );
        }

        return $this->clientRepo->update ( $attributes, $id );
    }

    /**
     * fn update white label setting
     * {@inheritDoc}
     * @see \App\Services\Clients\IClientService::updateWhiteLabelSetting()
     */
    public function updateWhiteLabelSetting($id, $request) {
        $attribute = array();
        if (isset($request['avatar'])) {
            $pathFile = config("constants.path_file_logo"). md5($id);
            $file = $request->file('avatar');

            $fileName= $this->uploadFile->uploadFile($file, $pathFile);
            $line = $request->get('check_header') == "on" ? 2 : 1;
            $file_terminated = $request->get("file_terminated", ",");
            $file_enclosed = $request->get("file_enclosed", '"');
            $attribute['avatar'] = $fileName;
        }

        if (isset($request['host_name'])) {
            $attribute['host_name'] = $request['host_name'];
        }

        return $this->clientRepo->update($attribute, $id);

    }

    /**
     * fn add creadit
     * {@inheritDoc}
     * @see \App\Services\Clients\IClientService::addCredit()
     */
    public function addCredit($id, $credits, $description) {
        $user = Auth::user ();
        if ($this->checkUserCanBeAddOrWithdrawCredit ( $user, $credits )) {
            $result = $this->clientRepo->addCredit ( $id, $credits, $description );
            if ($result) {
                return [
                        'status' => true,
                        'message' => Lang::get ( 'client.add_credit_success' )
                ];
            }

            return [
                    'status' => false,
                    'error' => $result['error']
            ];
        }

        return [
                'status' => false,
                'error' => Lang::get ( 'client.credits_error.not_enough' )
        ];
    }

    /**
     * fn draw creadit
     * {@inheritDoc}
     * @see \App\Services\Clients\IClientService::withdrawCredit()
     */
    public function withdrawCredit($id, $credits, $description) {
        $client = $this->clientRepo->getClientById($id);
        if ($this->checkUserCanBeAddOrWithdrawCredit ( $client, $credits )) {
            $result = $this->clientRepo->addCredit ( $id, $credits, $description, true );
            if ($result) {
                return [
                        'status' => true,
                        'message' => Lang::get ( 'client.withdraw_credit_success' )
                ];
            } else {
                return [
                        'status' => false,
                        'error' => $result['error']
                ];
            }
        } else {
            return [
                    'status' => false,
                    'error' => Lang::get ( 'client.credits_error.not_enough' )
            ];
        }
    }

    /**
     * fn upadte creadit limit
     * {@inheritDoc}
     * @see \App\Services\Clients\IClientService::updateCreditLimit()
     */
    public function updateCreditLimit($id, $credits, $description, $isDescrease = false) {
        $client = $this->clientRepo->getClientById($id);
        return $this->clientRepo->updateCreditsLimitForMonthlyType($id, $credits, $description, $isDescrease);

    }

    /**
     * fn get total limit of child
     * {@inheritDoc}
     * @see \App\Services\Clients\IClientService::getMaxTotalLimitedOfChild()
     */
    public function getMaxTotalLimitedOfChild($client) {
        return $this->clientRepo->getMaxTotalLimitedOfChild($client);
    }

    /**
     * fn transfer creadit mounthly
     * {@inheritDoc}
     * @see \App\Services\Clients\IClientService::transferCreditMonthly()
     */
    public function transferCreditMonthly($user) {
        return $this->clientRepo->transferCreditMonthly($user);
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Clients\IClientService::chargeCredits()
     */
    public function chargeCredits( $idUser, $credits ) {
        return $this->clientRepo->updateCredits( $idUser, $credits );
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Clients\IClientService::refundCredits()
     */
    public function refundCredits( $idUser, $credits ) {
        return $this->clientRepo->updateCredits( $idUser, $credits. false );
    }

    public function createApiAccount(CreateClientGroup3Request $request) {
        $list_sender = array();
        foreach ($request['sender'] as $sender) {
            $list_sender[$sender] = $sender;
        }
        // encode json
        $json_data = json_encode($list_sender);
        $request ['sender'] = $json_data;
        $request ['agency_id'] = 1;
        $request ['parent_id'] = Auth::user ()->id;
        $request ['status'] = "ENABLED";
        $request ['type'] = "GROUP3";
        $request ['blocked'] = 0;
        $request ['credits'] = 0;
        $request ['created_by'] = Auth::user ()->id;
        $request ['updated_by'] = Auth::user ()->id;
        $request ['currency'] = Auth::user ()->currency;
        $request ['default_price_sms'] = round(( float ) $request ['default_price_sms'], 2);
        $request ['password'] = Hash::make ( $request ['password'] );
        $request ['is_api'] = 1;

        if (!isset($request ['is_tracking_link'])) {
            $request ['is_tracking_link'] = 0;
        }

        if (!isset($request ['encrypted'])) {
            $request ['encrypted'] = 0;
        }
        return $this->clientRepo->createApiAccount ( $request->toArray (), true );
    }
}
?>