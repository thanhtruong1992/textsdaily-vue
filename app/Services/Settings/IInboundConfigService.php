<?php

namespace App\Services\Settings;

interface IInboundConfigService {
    public function fetchAll();
    public function getAllDataFormatDataTable($request);
    public function getInboundConfig($id);
    public function updateInboundConfig(array $attributes, $id);
    public function getInboundConfigByField($field, $value);
}