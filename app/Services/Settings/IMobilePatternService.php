<?php

namespace App\Services\Settings;

interface IMobilePatternService {
    public function fetchAll();
    public function getAllDataFormatDataTable($request);
    public function getMobilePattern($id);
    public function updateMobilePattern(array $attributes, $id);
    public function importMobilePattern($file, $path = '/public/settings/mobile-pattern');
    public function deleteMobilePattern( $id );
}