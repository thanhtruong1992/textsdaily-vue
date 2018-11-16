<?php
namespace App\Repositories\Reports;

use App\Repositories\BaseRepository;


class TMSMSReportReponsitory extends BaseRepository implements ITMSMSReportReponsitory{
     /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return "App\\Models\\TMSMSReport";
    }
}