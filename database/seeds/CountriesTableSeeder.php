<?php

use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $filePath = storage_path('csv/countries.csv');
        //
        $loadDataQuery = "LOAD DATA INFILE '{$filePath}' INTO TABLE countries
        FIELDS TERMINATED BY ','
        ENCLOSED BY '\"'
        LINES TERMINATED BY '\n'
        IGNORE 1 LINES
        (@col1, @col2)
        SET code = @col2, name = @col1, created_at = CURRENT_TIMESTAMP(), updated_at = CURRENT_TIMESTAMP();";
        $pdo = DB::connection()->getPdo();
        $pdo->exec($loadDataQuery);
    }
}
