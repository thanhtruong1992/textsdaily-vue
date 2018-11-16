<?php

namespace App\Services\InboundMessages;

interface IInboundMessagesService {
    public function getAllDataFormatDataTable($request);
    public function storeInboundMessages($messages);
    public function exportCSVInboundMessage($request);
}