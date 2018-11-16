<?php

namespace App\Repositories\Settings;

use Prettus\Repository\Eloquent\BaseRepository;
use DB;
use Illuminate\Support\Facades\Auth;

class MobilePatternRepository extends BaseRepository implements IMobilePatternRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return "App\\Models\\MobilePattern";
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Settings\IMobilePatternRepository::getDataTableList()
     */
    public function getDataTableList($keyword, $orderBy = 'country', $orderDirection = 'ASC') {
        $qr = DB::table( $this->model->getTable() )
        ->select('mobile_pattern.id', 'number_pattern', DB::raw('IFNULL(`name`, `country`) AS `country`'), 'network')
        ->leftJoin('countries', 'country', '=', 'code');
        //
        if ( $keyword ) {
            $qr->where('number_pattern', 'LIKE', $keyword . '%')
            ->orWhere('country', 'LIKE', '%' . $keyword . '%')
            ->orWhere('network', 'LIKE', '%' . $keyword . '%')
            ->orWhere('name', 'LIKE', '%' . $keyword . '%')
            ;
        }
        //
        if ( $orderBy && $orderDirection ) {
            $qr->orderBy($orderBy, $orderDirection);
        } else {
            $qr->orderBy('country', 'ASC');
        }
        return $qr->paginate(10);
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Settings\IMobilePatternRepository::importCSV()
     */
    public function importCSV($filePath, $lineEndCharacter) {
        try {
            // Current User
            $userId = Auth::user()->id;

            //
            $targetTableName = $this->model->getTable ();
            $pdo = DB::connection ()->getPdo ();

            // Create temporary table
            $tmpTableName = 'temp_mobile_pattern_' . time ();
            $pdo->exec ( "CREATE TABLE {$tmpTableName} LIKE {$targetTableName}; ALTER TABLE {$tmpTableName} DROP INDEX unique_number_pattern;" );

            // Load data to temporary table
            $loadDataQuery = "LOAD DATA INFILE '{$filePath}' INTO TABLE {$tmpTableName}
            FIELDS TERMINATED BY ','
            ENCLOSED BY '\"'
            LINES TERMINATED BY '{$lineEndCharacter}'
            IGNORE 1 LINES
            (@col1, @col2, @col3)
            SET number_pattern = @col1, country = @col2, network = @col3, created_at = CURRENT_TIMESTAMP(), created_by = {$userId};";
            $pdo->exec ( $loadDataQuery );

            // Call stored procedure process insert data
            $fieldInsert = 'country, network, number_pattern, created_at, created_by';
            $fieldUpdate = 'country = VALUES(country), network = VALUES(network), updated_at = VALUES(created_at), updated_by = VALUES(created_by)';
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