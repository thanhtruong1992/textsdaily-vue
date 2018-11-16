<?php

namespace App\Repositories\Reports;

use App\Repositories\BaseRepository;
use DB;

class ReportCenterReponsitory extends BaseRepository implements IReportCenterReponsitory {
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return "App\\Models\\ReportCenter";
    }

    public function reportCenter($listUser, $dateFrom, $dateTo, $campaignName, $headers, $fields, $typeUser, $timezone, $pathFile) {
        return DB::statement("call report_centers(?,?,?,?,?,?,?,?,?)", array($listUser, $dateFrom, $dateTo, $campaignName, $headers, $fields, $typeUser, $timezone, $pathFile));
    }
}