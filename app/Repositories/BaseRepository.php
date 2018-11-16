<?php
namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository as PrettusBaseRepository;
use Prettus\Repository\Events\RepositoryEntityCreated;
use Prettus\Repository\Events\RepositoryEntityUpdated;
use Prettus\Repository\Events\RepositoryEntityDeleted;

class BaseRepository extends PrettusBaseRepository
{
    /**
     * {@inheritDoc}
     * @see \Prettus\Repository\Eloquent\BaseRepository::model()
     */
    public function model() {}

    /**
     * FUNCTION TO CHANGE TABLE NAME BY USER
     * @param array $arrReplace
     */
    protected function __changeTableName( array $arrReplace )
    {
        $tableName = $this->model->getTable();
        foreach ( $arrReplace as $key => $value ){
            if (!is_null($value)){
                $tableName = str_replace($key, $value, $tableName);
            }
        }
        $this->model->setTable($tableName);
    }

    /**
     * FN REPLACE RAW QUERY WITH PARAMETERS
     * @param string $sql
     * @param array $bindings
     * @return string
     */
    public function replaceQuery($sql, $bindings) {
        $needle = '?';
        foreach ($bindings as $replace){
            $pos = strpos($sql, $needle);
            if ($pos !== false) {
                if (gettype($replace) === "string") {
                    $replace = ' "'.addslashes($replace).'" ';
                }
                $sql = substr_replace($sql, $replace, $pos, strlen($needle));
            }
        }
        return $sql;
    }

    /**
     * Save a new entity in repository
     *
     * @throws ValidatorException
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function create(array $attributes, $table_name = null)
    {
        if (!is_null($this->validator)) {
            // we should pass data that has been casts by the model
            // to make sure data type are same because validator may need to use
            // this data to compare with data that fetch from database.
            $attributes = $this->model->newInstance()->forceFill($attributes)->toArray();

            $this->validator->with($attributes)->passesOrFail(ValidatorInterface::RULE_CREATE);
        }

        $model = $this->model->newInstance($attributes);
        if ( $table_name ){
            $model->setTable($table_name);
        }
        $model->save();
        $this->resetModel();

        event(new RepositoryEntityCreated($this, $model));

        return $this->parserResult($model);
    }

    /**
     * Update a entity in repository by id
     *
     * @throws ValidatorException
     *
     * @param array $attributes
     * @param       $id
     *
     * @return mixed
     */
    public function update(array $attributes, $id, $table_name = null)
    {
        $this->applyScope();

        if (!is_null($this->validator)) {
            // we should pass data that has been casts by the model
            // to make sure data type are same because validator may need to use
            // this data to compare with data that fetch from database.
            $attributes = $this->model->newInstance()->forceFill($attributes)->toArray();

            $this->validator->with($attributes)->setId($id)->passesOrFail(ValidatorInterface::RULE_UPDATE);
        }

        $temporarySkipPresenter = $this->skipPresenter;

        $this->skipPresenter(true);

        $model = $this->model->findOrFail($id);
        if ( $table_name ){
            $model->setTable($table_name);
        }
        $model->fill($attributes);
        $model->save();

        $this->skipPresenter($temporarySkipPresenter);
        $this->resetModel();

        event(new RepositoryEntityUpdated($this, $model));

        return $this->parserResult($model);
    }

    public function getAllTable( $likeName = null, $preventName = null ) {
        $databaseName = \DB::getDatabaseName();
        $qr = "SELECT DISTINCT TABLE_NAME AS table_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$databaseName}'";
        if ( $likeName ) {
            $qr .= " AND TABLE_NAME LIKE '{$likeName}%'";
        }
        if ( $preventName) {
            $qr .= " AND TABLE_NAME != '{$preventName}'";
        }
        $qr .= " ORDER BY TABLE_NAME desc";

        return \DB::select( \DB::raw($qr) );
    }

    /**
     * Delete a entity in repository by id
     *
     * @param $id
     *
     * @return int
     */
    public function delete($id, $table_name = null)
    {
        $this->applyScope();

        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);

        $model = $this->find($id);
        $originalModel = clone $model;

        $this->skipPresenter($temporarySkipPresenter);
        $this->resetModel();

        //
        if ( $table_name ){
            $model->setTable($table_name);
        }

        $deleted = $model->delete();

        event(new RepositoryEntityDeleted($this, $originalModel));

        return $deleted;
    }
}
?>