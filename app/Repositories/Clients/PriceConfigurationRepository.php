<?php

namespace App\Repositories\Clients;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PriceConfigurationRepository extends BaseRepository implements IPriceConfigurationRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return "App\\Models\\PriceConfiguration";
    }

    /**
     * Change dynamic table
     *
     * @param int $idUser
     */
    private function changeTableName($idUser) {
        $this->__changeTableName ( array (
                'u_template' => 'u_' . $idUser
        ) );
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \App\Repositories\Clients\IPriceConfigurationRepository::findWherePriceConfiguration()
     */
    public function findWherePriceConfiguration($idUser, array $where, $columns = ['*']) {
        $this->changeTableName ( $idUser );
        //return parent::findWhere ( $where, $columns )->orderBy('updated_at', 'DESC')->first ();
        return parent::scopeQuery(function($query) use( $where, $columns ){
            return $query->select($columns)->where($where)->orderBy('updated_at', 'DESC')->orderBy('created_at', 'DESC');
        })->first();
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \App\Repositories\Clients\IPriceConfigurationRepository::updatePriceConfiguration()
     */
    public function updatePriceConfiguration($idUser, array $attributes, $id) {
        $this->changeTableName ( $idUser );
        // Add update_by
        $attributes['updated_by'] = Auth::user()->id;
        return parent::update ( $attributes, $id, $this->model->getTable () );
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \App\Repositories\Clients\IPriceConfigurationRepository::createPriceConfiguration()
     */
    public function createPriceConfiguration($idUser, array $attributes) {
        $this->changeTableName ( $idUser );
        // Add created_by
        $attributes['created_by'] = Auth::user()->id;
        return parent::create ( $attributes, $this->model->getTable () );
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Clients\IPriceConfigurationRepository::allPriceConfiguration()
     */
    public function allPriceConfiguration( $idUser, array $columns = [] ) {
        $this->changeTableName( $idUser );
        $qr = DB::table( $this->model->getTable() );
        if ( $columns ) {
            $qr->select( $columns );
        }
        $this->resetModel();
        return $qr->get();
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Clients\IPriceConfigurationRepository::getDisabledCoutries()
     */
    public function getDisabledCoutries($idUser) {
        $this->changeTableName($idUser);
        return $this->findWhere([
                'disabled' => 1
        ], ['country']);
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Clients\IPriceConfigurationRepository::getEnabledCoutries()
     */
    public function getEnabledCoutries($idUser) {
        $this->changeTableName($idUser);
        return $this->findWhere([
                'disabled' => 0,
                'network' => null
        ], ['country']);
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Clients\IPriceConfigurationRepository::deletePriceConfiguration()
     */
    public function deletePriceConfiguration( $idUser, $id ) {
        $this->changeTableName($idUser);
        return parent::delete($id, $this->model->getTable());
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Clients\IPriceConfigurationRepository::importCSV()
     */
    public function importCSV($idClient, $filePath, $lineEndCharacter) {
        try {
            $this->changeTableName($idClient);
            // Current User
            $userId = Auth::user()->id;

            //
            $targetTableName = $this->model->getTable ();
            $pdo = DB::connection ()->getPdo ();

            // Create temporary table
            $tmpTableName = 'temp_price_configuration_' . time ();
            $pdo->exec ( "CREATE TABLE {$tmpTableName} LIKE {$targetTableName}; ALTER TABLE {$tmpTableName} DROP INDEX price_configuration_u_template_country_network_unique;" );

            // Load data to temporary table
            $loadDataQuery = "LOAD DATA INFILE '{$filePath}' INTO TABLE {$tmpTableName}
            FIELDS TERMINATED BY ','
            ENCLOSED BY '\"'
            LINES TERMINATED BY '{$lineEndCharacter}'
            IGNORE 1 LINES
            (@col1, @col2, @col3, @col4)
            SET country = @col1, network = @col2, price = @col3, disabled = @col4, created_at = CURRENT_TIMESTAMP(), created_by = {$userId};";
            $pdo->exec ( $loadDataQuery );

            // Call stored procedure process insert data
            $fieldInsert = 'country, network, price, disabled, created_at, created_by';
            $fieldUpdate = 'country = VALUES(country), network = VALUES(network), price = VALUES(price), disabled = VALUES(disabled), updated_at = VALUES(created_at), updated_by = VALUES(created_by)';
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