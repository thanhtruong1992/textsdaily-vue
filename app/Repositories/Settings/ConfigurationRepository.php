<?php

namespace App\Repositories\Settings;

use Illuminate\Support\Facades\DB;

class ConfigurationRepository implements IConfigurationRepository {

    private $table_name = "configuration";
    private $key = "config_report";

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Settings\IConfigurationRepository::updateConfiguration()
     */
    public function updateConfiguration($data) {
        $configuration = DB::table($this->table_name)->where('key', $this->key)->first();
        if (!empty($configuration)) {
            return DB::table($this->table_name)->where('id', $configuration->id)->update([
                'value' => $data
            ]);
        } else {
            return DB::table($this->table_name)->insert([
                'key' => $this->key,
                'value' => $data
            ]);
        }
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Settings\IConfigurationRepository::fetchConfiguration()
     */
    public function fetchConfiguration() {
        return DB::table($this->table_name)->where('key', $this->key)->first();
    }

    public function exportCSVDetail($startDate, $endDate, $headerCSV, $pathFile) {
        return DB::statement("call auto_trigger_report(?,?,?,?)", array($startDate, $endDate, $headerCSV, $pathFile));
    }
}