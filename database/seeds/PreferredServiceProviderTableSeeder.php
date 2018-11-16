<?php

use Illuminate\Database\Seeder;

class PreferredServiceProviderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $filePath = public_path('/'. config('constants.path_sample_service_provider_template'));
        //
        $loadDataQuery = "LOAD DATA INFILE '{$filePath}' INTO TABLE preferred_service_provider
        FIELDS TERMINATED BY ','
        ENCLOSED BY '\"'
        LINES TERMINATED BY '\n'
        IGNORE 1 LINES
        (@col1, @col2, @col3)
        SET service_provider = @col1, country = @col2, network = @col3, created_at = CURRENT_TIMESTAMP();"
        ;
        $pdo = DB::connection()->getPdo();
        $pdo->exec($loadDataQuery);
    }
}
