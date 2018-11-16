<?php

namespace App\Services\Reports;

interface IInfobipReportService {
    public function getMessageInfoNew($messageID);
    public function createInfobipReport($data);
    public function updateInfobipReport($data , $messageID);
    public function deleteInfoBipReport($messageID);
}