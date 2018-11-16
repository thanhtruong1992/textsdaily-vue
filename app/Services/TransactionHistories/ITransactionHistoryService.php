<?php

namespace App\Services\TransactionHistories;

interface ITransactionHistoryService {
    public function getTransactionHistoryCampains($request);
    public function exportCSVCampaigns($request);
    public function getCampaign($userID, $campaignID);
    public function exportCSVReportCamapign($userID, $campaignID);
    public function getTransactionByQuery($from, $to, $timezone, $sort_column, $order_by, $page);
    public function exportCSVClient($from, $to, $timezone);
}