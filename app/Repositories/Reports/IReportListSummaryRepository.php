<?php

namespace App\Repositories\Reports;

interface IReportListSummaryRepository {
    public function create(array $attributes);
    public function getListSummary($listID);
}