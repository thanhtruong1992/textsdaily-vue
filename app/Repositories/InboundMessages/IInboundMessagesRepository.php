<?php

namespace App\Repositories\InboundMessages;

interface IInboundMessagesRepository {
    public function getDataTableList($orderBy = 'created_at', $orderDirection = 'DESC', $request = null, $idUser = null);
    public function createMessage(array $attributes, $idUser = null);
    public function exportCSV($request, $pathFile, $userID = null);
}