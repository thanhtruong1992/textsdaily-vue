<?php

use Illuminate\Database\Seeder;

class AgencyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('agencies')->insert([
                'id'            => 1,
                'name'          => 'Success Agency',
                'product_name'  => 'Success',
                'logo'          => '',
                'created_by'    => 1,
                'updated_by'    => 1,
        ]);
    }
}
