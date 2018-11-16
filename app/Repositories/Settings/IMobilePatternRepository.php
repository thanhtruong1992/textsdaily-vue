<?php

namespace App\Repositories\Settings;

interface IMobilePatternRepository {
    public function getDataTableList($keyword, $orderBy = 'country', $orderDirection = 'ASC');
    public function importCSV($filePath, $lineEndCharacter);
}