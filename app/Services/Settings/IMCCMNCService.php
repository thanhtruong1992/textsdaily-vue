<?php

namespace App\Services\Settings;

interface IMCCMNCService {
    public function fetchAll();
    public function fetchAllOptions();
    public function getAllDataFormatDataTable($request);
    public function getMCCMNC($id);
    public function updateMCCMNC( array $attributes, $id );
    public function importMCCMNC( $file );
    public function deleteMCCMNC( $id );
    public function getCountryNetworkByMCCMNC( $mccmnc );
}