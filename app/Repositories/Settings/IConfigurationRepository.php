<?php

namespace App\Repositories\Settings;

interface IConfigurationRepository {
    public function fetchConfiguration();
    public function updateConfiguration($data);
    public function exportCSVDetail($startDate, $endDate, $headerCSV, $pathFile);
}