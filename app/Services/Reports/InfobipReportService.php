<?php

namespace App\Services\Reports;

use App\Services\BaseService;
use App\Repositories\Reports\IInfobipReportReponsitory;

class InfobipReportService extends BaseService implements IInfobipReportService {
    protected $infobipReportRepo;

    public function __construct(IInfobipReportReponsitory $infobipReportRepo) {
        $this->infobipReportRepo = $infobipReportRepo;
    }

    /**
     * fn report infobip 
     */
    public function getMessageInfoNew($messageID) {
        try {
            return $this->infobipReportRepo->getMessageInfoNew($messageID);
        }catch(\Exception $e) {
            return null;
        }
    }

    /**
     * fn create report into infobip_reports table
     */
    public function createInfobipReport($data) {
        return $this->infobipReportRepo->createInfobipReport($data);
    }

    /**
     * fn update report into infobip_reports table
     */
    public function updateInfobipReport($data , $messageID) {
        return $this->infobipReportRepo->updateInfobipReport($data, $messageID);
    }

    /**
     * fn delete infobip report
     * @param string $messageID
     * @return boolean
     */
    public function deleteInfoBipReport($messageID) {
        return $this->infobipReportRepo->deleteInfoBipReport($messageID);
    }
}
