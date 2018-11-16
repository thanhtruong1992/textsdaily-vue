<?php
namespace App\Repositories\Clients;

interface IClientRepository {
    public function createNewClient(array $attribute, bool $isGroup3 = false);
    public function getAllClient(bool $for_reader_select = false);
    public function getClientById($id);
    public function deleteClientById($list_id);
    public function updateStatusClient($list_id, $status);
    public function addCredit($id, $credits, $description, $isWithdraw = false);
    public function transferCreditMonthly($user);
    public function updateCreditsLimitForMonthlyType($clientId, $creditChange, $description, $isDescrease = false);
    public function getMaxTotalLimitedOfChild($client);
    public function removeAllQueueTableByUser($userId);
    public function updateCredits( $idUser, $credits, $isMinus = true );
}
?>