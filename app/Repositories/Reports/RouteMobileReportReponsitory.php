<?php
namespace App\Repositories\Reports;

use App\Repositories\BaseRepository;


class RouteMobileReportReponsitory extends BaseRepository implements IRouteMobileReportReponsitory{
     /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return "App\\Models\\RouteMobileReport";
    }
}