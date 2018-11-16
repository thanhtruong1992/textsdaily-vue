<?php

namespace App\Repositories\Settings;

use Prettus\Repository\Eloquent\BaseRepository;
use DB;
use Illuminate\Support\Facades\Auth;

class PreferredServiceProviderRepository extends BaseRepository implements IPreferredServiceProviderRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return "App\\Models\\PreferredServiceProvider";
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \App\Repositories\Settings\IPreferredServiceProviderRepository::importCSV()
     */
    public function importCSV($filePath, $lineEndCharacter) {
        try {
            // Current User
            $userId = Auth::user()->id;

            //
            $targetTableName = $this->model->getTable ();
            $pdo = DB::connection ()->getPdo ();

            // Create temporary table
            $tmpTableName = 'temp_preferred_service_provider_' . time ();
            $pdo->exec ( "CREATE TABLE {$tmpTableName} LIKE {$targetTableName}; ALTER TABLE {$tmpTableName} DROP INDEX preferred_service_provider_country_network_unique;" );

            // Load data to temporary table
            $loadDataQuery = "LOAD DATA INFILE '{$filePath}' INTO TABLE {$tmpTableName}
                FIELDS TERMINATED BY ','
                ENCLOSED BY '\"'
                LINES TERMINATED BY '{$lineEndCharacter}'
                IGNORE 1 LINES
                (@col1, @col2, @col3)
                SET service_provider = @col1, country = @col2, network = @col3, created_at = CURRENT_TIMESTAMP(), created_by = {$userId};";
            $pdo->exec ( $loadDataQuery );

            // Call stored procedure process insert data
            $fieldInsert = 'country, network, service_provider, created_at, created_by';
            $fieldUpdate = 'service_provider = VALUES(service_provider), updated_at = VALUES(created_at), updated_by = VALUES(created_by)';
            $pdo->exec ( "CALL loadDataInfileCSV('{$tmpTableName}', '{$targetTableName}', '{$fieldInsert}', '{$fieldUpdate}');" );

            //
            return true;
        } catch ( \PDOException $e ) {
            return false;
        } catch ( \Exception $e ) {
            return false;
        }
    }
}