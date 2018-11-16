<?php

use Illuminate\Database\Seeder;

class ServiceProviderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('service_provider')->insert([
                'id'            => 1,
                'code'          => 'INFOBIP',
                'name'          => 'INFOBIP',
                'config_url'    => 'https://api.infobip.com/sms/',
                'config_username'   => 'dctdemo',
                'config_password'   => 'IreneTan2015',
                'config_access_key' => null,
                'default'       => 1,
                'status'        => 'ACTIVE',
                'created_by'    => 1,
                'updated_by'    => 1,
        ]);

        DB::table('service_provider')->insert([
                'id'            => 2,
                'code'          => 'MESSAGEBIRD',
                'name'          => 'MESSAGE BIRD',
                'config_url'    => 'https://rest.messagebird.com/',
                'config_username'   => null,
                'config_password'   => null,
                'config_access_key' => 'ibBpQT13xSY6o5ZEbanQW60vq',
                'default'       => 0,
                'status'        => 'ACTIVE',
                'created_by'    => 1,
                'updated_by'    => 1,
        ]);

        DB::table('service_provider')->insert([
                'id'            => 3,
                'code'          => 'TMSMS',
                'name'          => 'TM SMS Getway',
                'config_url'    => 'http://tm-gateway.sms-service.com.my:11030/cgi-bin/sendsms',
                'config_username'   => "DataComm_WSH",
                'config_password'   => "00033",
                'config_access_key' => null,
                'default'       => 0,
                'status'        => 'ACTIVE',
                'created_by'    => 1,
                'updated_by'    => 1,
        ]);

        DB::table('service_provider')->insert([
                'id'            => 4,
                'code'          => 'ROUTEMOBILE',
                'name'          => 'oute Mobile',
                'config_url'    => 'http://rslr.connectbind.com:8080/bulksms/bulksms',
                'config_username'   => "Vinc-success",
                'config_password'   => "wdgT4cQI",
                'config_access_key' => null,
                'default'       => 0,
                'status'        => 'ACTIVE',
                'created_by'    => 1,
                'updated_by'    => 1,
        ]);
    }
}
