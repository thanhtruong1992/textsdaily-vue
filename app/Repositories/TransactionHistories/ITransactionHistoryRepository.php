<?php

namespace App\Repositories\TransactionHistories;

interface ITransactionHistoryRepository {
    public function getTransactionHistoryCampains($listUser, $from, $to, $timezone, $typeUser, $defaultCurrency, $page);
    public function exportCSVCampaigns($listUser, $from, $to, $timezone, $typeUser, $defaultCurrency, $headerCSV, $pathFile);
    public function getReportCampaign($campaignID, $userID, $typeUser, $defaultCurrency);
    public function exportCSVReportCampaign($campaignID, $userID, $typeUser, $defaultCurrency, $headerCSV, $pathFile);
    public function getTransactionByQuery($from, $to, $timezone, $sort_column, $order_by, $page);
    public function exportCSVClient($from, $to, $timezone, $headerCSV, $pathFile);
}