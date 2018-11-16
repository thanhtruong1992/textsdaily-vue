<?php

namespace App\Services\Reports;

use App\Services\BaseService;
use App\Repositories\Reports\ITMSMSReportReponsitory;
use Mockery\CountValidator\Exception;


class TMSMSReportService extends BaseService implements ITMSMSReportService {
    protected $tmSMSRepo;

    public function __construct(ITMSMSReportReponsitory $tmSMSRepo)
    {
        $this->tmSMSRepo = $tmSMSRepo;
    }

    /**
     * fn create report
     * return object
     */
    public function createTMSMSReport($data) {
        try {
            $report = $this->tmSMSRepo->create($data);

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
    public function getTMSMSReport($idReport) {
        try {
            $report = $this->tmSMSRepo->findByField('return_message_id', $idReport)->first()->toArray();
            if(empty($report)) {
                return false;
            }

            return $report;
        }catch(Exception $e) {
            return false;
        }
    }

    /**
     * FN delete report
     * param in report
     * return boolean
     */
    public function deleteTMSMSReport($idReport) {
        try {
            $this->tmSMSRepo->delete($idReport);
        }catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}