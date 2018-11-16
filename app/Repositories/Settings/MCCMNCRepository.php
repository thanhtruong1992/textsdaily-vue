<?php

namespace App\Repositories\Settings;

use Prettus\Repository\Eloquent\BaseRepository;
use DB;
use Illuminate\Support\Facades\Auth;

class MCCMNCRepository extends BaseRepository implements IMCCMNCRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return "App\\Models\\MCCMNC";
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Settings\IMCCMNCRepository::importCSV()
     */
    public function importCSV($filePath, $lineEndCharacter) {
        try {
            // Current User
            $userId = Auth::user()->id;

            //
            $targetTableName = $this->model->getTable ();
            $pdo = DB::connection ()->getPdo ();

            // Create temporary table
            $tmpTableName = 'temp_mccmnc_' . time ();
            $pdo->exec ( "CREATE TABLE {$tmpTableName} LIKE {$targetTableName}; ALTER TABLE {$tmpTableName} DROP INDEX unique_mccmnc;" );

            // Load data to temporary table
            $loadDataQuery = "LOAD DATA INFILE '{$filePath}' INTO TABLE {$tmpTableName}
                FIELDS TERMINATED BY ','
                ENCLOSED BY '\"'
                LINES TERMINATED BY '{$lineEndCharacter}'
                IGNORE 1 LINES
                (@col1, @col2, @col3)
                SET mccmnc = @col1, country = @col2, network = @col3, created_at = CURRENT_TIMESTAMP(), created_by = {$userId};";
            $pdo->exec ( $loadDataQuery );

            // Call stored procedure process insert data
            $fieldInsert = 'country, network, mccmnc, created_at, created_by';
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

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Settings\IMCCMNCRepository::getDataTableList()
     */
    public function getDataTableList($keyword, $orderBy = 'country', $orderDirection = 'ASC') {
        $qr = DB::table( $this->model->getTable() )
            ->select('mccmnc.id', 'mccmnc', DB::raw('IFNULL(`name`, `country`) AS `country`'), 'network')
            ->leftJoin('countries', 'country', '=', 'code');
        //
        if ( $keyword ) {
            $qr->where('mccmnc', $keyword)
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
}