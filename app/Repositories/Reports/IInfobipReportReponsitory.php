<?php

namespace App\Repositories\Reports;

interface IInfobipReportReponsitory {
    public function getMessageInfoNew( $messageID );
    public function createInfobipReport( $data );
    public function updateInfobipReport( $data, $messageID );
    public function deleteInfoBipReport($messageID);
}