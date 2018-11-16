<?php
namespace App\Repositories\Reports;

use DB;
use App\Models\InfobipReport;

class InfobipReportReponsitory implements IInfobipReportReponsitory{

    public function getMessageInfoNew( $messageID ) {
        return InfobipReport::findOrFail($messageID);
    }

    /**
     * create or update report infobip
     */
    public function createInfobipReport($data) {
        return $infobip =InfobipReport::updateOrCreate(
            [ "return_message_id" => $data['return_message_id'] ],
            [ "return_json" => $data['return_json'] ]
        );
    }

    /**
     * update report infobip
     */
    public function updateInfobipReport($data , $messageID) {
        return InfobipReport::where('return_message_id', $messageID )
                ->update([
                    'return_message_id' => $messageID,
                    'return_json' => $data->return_json
                ]);
    }

    /**
     * fn delete infobip report
     * @param string $messageID
     * @return boolean
     */
    public function deleteInfoBipReport($messageID) {
        return InfobipReport::destroy($messageID);
    }
}