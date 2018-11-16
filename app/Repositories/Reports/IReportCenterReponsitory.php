<?php

namespace App\Repositories\Reports;

interface IReportCenterReponsitory {
    public function reportCenter($listUser, $dateFrom, $dateTo, $campaignName, $headers, $fields, $typeUser, $timezone, $pathFile);
}