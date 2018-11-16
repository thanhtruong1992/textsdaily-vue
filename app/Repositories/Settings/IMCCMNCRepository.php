<?php

namespace App\Repositories\Settings;

interface IMCCMNCRepository {
    public function importCSV($filePath, $lineEndCharacter);
    public function getDataTableList($keyword, $orderBy = 'created_at', $orderDirection = 'DESC');
}