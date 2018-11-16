<?php

namespace App\Services\Reports;

use App\Services\BaseService;
use App\Repositories\Reports\IRouteMobileReportReponsitory;
use Mockery\CountValidator\Exception;


class RouterMobileReportService extends BaseService implements IRouterMobileReportService {
    protected $routerMobileRepo;

    public function __construct(IRouteMobileReportReponsitory $routerMobileRepo)
    {
        $this->routerMobileRepo = $routerMobileRepo;
    }

    /**
     * fn create report
     * return object
     */
    public function createRouterMobileReport($data) {
        try {
            $report = $this->routerMobileRepo->create($data);

            return $report;
        }catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * fn get report
     * param id report
     * return boolean | object
     */
    public function getRouterMobileReport($idReport) {
        try {
            $report = $this->routerMobileRepo->findByField('return_message_id', $idReport)->first();
            if(empty($report)) {
                return false;
            }

            return $report->toArray();
        }catch(Exception $e) {
            return false;
        }
    }

    /**
     * FN delete report
     * param in report
     * return boolean
     */
    public function deleteRouterMobileReport($idReport) {
        try {
            $this->routerMobileRepo->delete($idReport);
        }catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function updateRouterMobileReport($attrubutes, $id) {
        try {
            return $this->routerMobileRepo->update($attrubutes, $id);
        }catch(\Exception $e) {
            return null;
        }
    }
}