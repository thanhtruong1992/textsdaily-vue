<?php

namespace App\Services\Reports;

interface IRouterMobileReportService {
    public function createRouterMobileReport($data);
    public function getRouterMobileReport($idReport);
    public function deleteRouterMobileReport($idReport);
    public function updateRouterMobileReport($attrubutes, $id);
}