<?php

namespace App\Repositories\Reports;

use App\Repositories\BaseRepository;
use Auth;
use DB;

class ReportCampaignReponsitory implements IReportCampaignReponsitory {
    public function getListReportCampaign($userID, $typeUser, $search, $page) {
        return DB::select("call get_data_report_campaign(?,?,?,?)", array($userID, $typeUser, $search, $page));
    }
    public function getReportCampaign($userID, $campaignID) {
        $dataChart = DB::select("call report_campaign_chart(?,?)", array($userID, $campaignID));
        $delivered = 0;
        $listCountry = "";
        $queryCountry = "''";
        foreach ($dataChart as $item) {
            if($item->country != "") {
                $listCountry = $listCountry == "" ? "'" . $item->country . "'" : $listCountry . ",'" . $item->country . "'";
            }else {
                $queryCountry = " country = '' ";
            }
        }

        if($listCountry != "") {
            $queryCountry = " AND (country IN (" . $listCountry . ") OR " . $queryCountry . ") ";
        }else {
            $queryCountry = " AND (" . $queryCountry . ") ";
        }

        if(count($dataChart) > 0) {
            foreach ($dataChart as $item) {
                if($item->country != "") {
                    $listCountry = $listCountry == "" ? "'" . $item->country . "'" : $listCountry . ",'" . $item->country . "'";
                }
                $delivered += $item->delivered;
            }

            $dataShortLink = DB::select("call get_data_short_link(?,?,?)", array($userID, $campaignID, $delivered));
            $data = DB::select("call report_campaign_data(?,?,?)", array($userID, $campaignID, $queryCountry));
            return (object) [
                    "dataChart" => $dataChart,
                    "dataShortLink" => $dataShortLink,
                    "data" => $data
            ];
        }
        
        return (object) [
                "dataChart" => [],
                "dataShortLink" => [],
                "data" => []
        ];
    }
    /**
     * FN total campaign of table queue
     * @param INT $userID
     * @param INT $campaignID
     * @return array
     */
    public function totalPendingQueueCampaign($userID, $campaignID) {
        return DB::select("call total_pending_queue_campaign(?,?)", array($userID, $campaignID));
    }

    /**
     * FN move date table queue into table report
     * @param int $userID
     * @param int $campaignID
     * @param string $typeUser
     * @return array
     */
    public function moveDataQueueIntoReport($userID, $campaignID) {
        return DB::statement("call move_data_queue_into_report(?,?)", array($userID, $campaignID));
    }

    /**
     * FN delete report
     * @param int $userID
     * @param int $campaignID
     * @return array
     */
    public function removeReportCampaign($userID, $campaignID) {
        return DB::table('report_list_summary_u_' . $userID)->where('campaign_id', $campaignID)->delete();
    }

    /**
     * FN export campagin
     * @param int $campaignID
     * @param int $userID
     * @param string $pathFile
     * @param string $defaultCurrency
     * @return array
     */
    public function exportCSVCampaign($campaignID, $userID, $pathFile, $defaultCurrency, $flagFile, $encrypted, $timeZone, $typeUser = 'GROUP1') {
        return  DB::select("call export_campaign(?,?,?,?,?,?,?,?,?,?,?,?)", array($campaignID, $userID, $pathFile, $defaultCurrency, $flagFile->detailed, $flagFile->pending, $flagFile->delivered, $flagFile->expired, $flagFile->failed, $encrypted, $timeZone, $typeUser));
    }

    /**
     *  FN export pdf of campaign report
     * @param int $campaignID
     * @param int $userID
     * @param string $listCountry
     * @return object
     */
    public function exportPDFCampaign($campaignID, $userID, $listCountry) {
        $query_where = "";
        $query_where_delivered = "";
        $dataChart = DB::select("call report_campaign_chart(?,?)", array($userID, $campaignID));
        $listCountry = "";
        $queryCountry = "''";
        foreach ($dataChart as $item) {
            if($item->country != "") {
                $listCountry = $listCountry == "" ? "'" . $item->country . "'" : $listCountry . ",'" . $item->country . "'";
            }else {
                $queryCountry = " country IS NULL ";
            }
        }

        if($listCountry != "") {
            $query_where = " WHERE (country IN({$listCountry}) OR " . $queryCountry . ") ";
            $query_where_delivered = " AND (country IN({$listCountry}) OR " . $queryCountry . ") ";
            $queryCountry = " AND (country IN (" . $listCountry . ") OR " . $queryCountry . ") ";
        }else {
            $queryCountry = " AND " . $queryCountry . " ";
        }

        $queryStatus = "SELECT count(1) AS total, return_status FROM queue_u_{$userID}_c_{$campaignID} {$query_where} GROUP BY return_status";
        $queryTotalExpenses = "SELECT SUM(sum_price_client) AS expenses, IFNULL(country, 'Unknown') AS country FROM queue_u_{$userID}_c_{$campaignID} {$query_where} GROUP BY country";
        $queryDeliveredRate = "SELECT T1.country, (T2.total_delivered / T1.total_phone * 100) AS delivered_rate FROM " .
                              "(SELECT COUNT(1) AS total_phone, IFNULL(country, \"Unknown\") AS country FROM queue_u_{$userID}_c_{$campaignID} {$query_where} GROUP BY country) AS T1 ".
                              " LEFT JOIN (SELECT COUNT(1) AS total_delivered, IFNULL(country, \"Unknown\") AS country FROM queue_u_{$userID}_c_{$campaignID}  WHERE return_status = \"DELIVERED\" {$query_where_delivered} GROUP BY country, return_status) AS T2 ".
                              " ON T1.country = T2.country";
        $dataStatus = DB::select($queryStatus);
        $dataTotalExpenses = DB::select($queryTotalExpenses);
        $dataDeliveredRate = DB::select($queryDeliveredRate);
        $dataTable = DB::select("call report_campaign_data(?,?,?)", array($userID, $campaignID, $queryCountry));

        return (object) array(
            "data_status" => $dataStatus,
            "data_total_expenses" => $dataTotalExpenses,
            "data_delivered_rate" => $dataDeliveredRate,
            "data_table" => $dataTable
        );
    }

    /**
     * FN get all table campaign
     * @return unknown
     */
    public function getAllDataCampaignAllUser() {
        return DB::select("call get_all_data_campaign_all_user()");
    }
}