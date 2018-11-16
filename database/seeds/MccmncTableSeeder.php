<?php

use Illuminate\Database\Seeder;

class MccmncTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $filePath = public_path('/'. config('constants.path_sample_mnc_mcc_template'));
        //
        $loadDataQuery = "LOAD DATA INFILE '{$filePath}' INTO TABLE mccmnc
            FIELDS TERMINATED BY ','
            ENCLOSED BY '\"'
            LINES TERMINATED BY '\n'
            IGNORE 1 LINES
            (@col1, @col2, @col3)
            SET mccmnc = @col1, country = @col2, network = @col3, created_at = CURRENT_TIMESTAMP();"
        ;
        $pdo = DB::connection()->getPdo();
        $pdo->exec($loadDataQuery);
    }
}
