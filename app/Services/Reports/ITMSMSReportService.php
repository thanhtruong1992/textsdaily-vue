<?php

namespace App\Services\Reports;

interface ITMSMSReportService {
    public function createTMSMSReport($data);
    public function getTMSMSReport($idReport);
    public function deleteTMSMSReport($idReport);
}