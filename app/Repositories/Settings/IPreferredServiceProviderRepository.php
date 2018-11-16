<?php

namespace App\Repositories\Settings;

interface IPreferredServiceProviderRepository {
    public function importCSV($filePath, $lineEndCharacter);
}