<?php

namespace App\Repositories\Reports;

interface IReportCampaignReponsitory {
    public function getListReportCampaign($userID, $typeUser, $search, $page);
    public function getReportCampaign($userID, $campaignID);
    public function totalPendingQueueCampaign($userID, $campaignID);
    public function moveDataQueueIntoReport($userID, $campaignID);
    public function removeReportCampaign($userID, $campaignID);
    public function exportCSVCampaign($campaignID, $userID, $pathFile, $defaultCurrency, $flagFile, $encrypted, $timeZone, $typeUser = 'GROUP1');
    public function exportPDFCampaign($campaignID, $userID, $listCountry);
    public function getAllDataCampaignAllUser();
}