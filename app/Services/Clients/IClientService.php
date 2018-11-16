<?php
namespace App\Services\Clients;

use App\Http\Requests\CreateClientRequest;
use App\Http\Requests\CreateClientGroup3Request;

interface IClientService {
    /**
     * create new client
     * @param CreateClientRequest $request
     */
    public function createNewClient($attribute);
    public function createNewClientGroup3(CreateClientGroup3Request $request);
    public function createNewReader(array $request);
    /**
     * Get client by id
     * @param unknown $id
     */
    public function getClientById($id);
    public function getAllClientUser(bool $for_reader_select = false);
    public function deleteClient($list_id);
    public function updateStatusClien($list_id, $status);
    public function updateClient($id, array $attributes);
    public function updateAccountSetting($id, array $attributes);
    public function updateWhiteLabelSetting($id, $request);
    public function addCredit($id, $credits, $description);
    public function updateCreditLimit($id, $credits, $description, $isDescrease = false);
    public function getMaxTotalLimitedOfChild($client);
    public function withdrawCredit($id, $credits, $description);
    public function transferCreditMonthly($user);
    public function chargeCredits( $idUser, $credits );
    public function refundCredits( $idUser, $credits );
}
?>