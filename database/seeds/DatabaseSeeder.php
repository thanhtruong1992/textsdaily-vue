<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $this->call(AgencyTableSeeder::class);
        $this->call(ClientTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(ServiceProviderTableSeeder::class);
        $this->call(MccmncTableSeeder::class);
        $this->call(MobilePatternTableSeeder::class);
        $this->call(PreferredServiceProviderTableSeeder::class);
//         $this->call(TemplateTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
    }
}
