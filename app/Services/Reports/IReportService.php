<?php

namespace App\Services\Reports;

interface IReportService {
    public function getListReportCampaign($request);
    public function getReportCampaign($userID, $campaignID);
    public function moveDataQueueToReportListSummary();
    public function exportCSVCampaign($userID, $campaignID, $request);
    public function exportPDFCampaign($userID, $campaignID, $request);
    public function getDataExportPDFCampaign($request);
    public function reportCenter($request);
    public function getDataReportCenter($request);
    public function cronJobReportCenter();
}