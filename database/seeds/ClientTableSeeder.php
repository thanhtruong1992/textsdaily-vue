<?php

use Illuminate\Database\Seeder;

class ClientTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('clients')->insert([
                'id'            => 1,
                'agency_id'     => 1,
                'logo'          => '',
                'address'       => '',
                'name'          => 'Success Client',
                'fax'           => '',
                'website'       => '',
                'phone'         => '',
                'sender'        => '',
                'created_by'    => 1,
                'updated_by'    => 1,
        ]);
    }
}
