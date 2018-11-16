<?php


namespace App\Repositories\Reports;

use App\Repositories\BaseRepository;
use App\Models\ReportListSummary;
use Auth;
use DB;

class ReportListSummaryRepository extends BaseRepository implements IReportListSummaryRepository{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return "App\\Models\\ReportListSummary";
    }

    private function changeTableName( $idUser ) {
        $this->__changeTableName ( array (
                'u_template' => 'u_' . $idUser
        ) );
    }

    /**
     * CUSTOM CREATE FUNCTION TO CHANGE TABLE NAME BY USER
     * {@inheritDoc}
     * @see \Prettus\Repository\Eloquent\BaseRepository::create()
     */
    public function create(array $attributes, $table_name = null) {
        $this->changeTableName( Auth::user()->id );
        //
        return parent::create($attributes, $this->model->getTable());
    }

    public function getListSummary($listID) {
        $userId = Auth::user()->id;
        $dataChart = DB::select("call report_subscriber_list_chart(?,?)", array($userId, $listID));
        $listCountry = "";
        $queryCountry = "''";

        if(count($dataChart) > 0) {
            foreach ($dataChart as $item) {
                if($item->country != "") {
                    $listCountry = $listCountry == "" ? "'" . $item->country . "'" : $listCountry . ",'" . $item->country . "'";
                }else {
                    $queryCountry = " country IS NULL ";
                }
            }
            if($listCountry != "") {
                $queryCountry = " AND (country IN (" . $listCountry . ") OR " . $queryCountry . ") ";
            }else {
                $queryCountry = " AND " . $queryCountry . " ";
            }
            $data = DB::select("call report_subscriber_list_data(?,?,?)", array($userId, $listID, $queryCountry));
            return (object) [
                    "dataChart" => $dataChart,
                    "data" => $data
            ];
        }

        return (object) [
                "dataChart" => [],
                "data" => []
        ];
    }
}
