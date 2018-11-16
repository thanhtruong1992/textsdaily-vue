<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class UserTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        //
        DB::table ( 'users' )->insert ( [
                'id' => 1,
                'name' => 'Admin',
                'username' => "admin@success-ss.com.vn",
                'email' => 'admin@success-ss.com.vn',
                'password' => Hash::make ( '123456' ),
                'remember_token' => null,
                'agency_id' => 1,
                'parent_id' => null,
                'country' => 'SG',
                'language' => 'en',
                'time_zone' => 'Asia/Singapore',
                'status' => 'ENABLED',
                'type' => 'GROUP1',
                'encrypted' => 0,
                'blocked' => 0,
                'billing_type' => 'UNLIMITED',
                'credits' => 0,
                'credits_usage' => 0,
                'credits_limit' => 0,
                'currency' => 'USD',
                'default_price_sms' => 0,
                'sender' => '{"Verify":"Verify"}',
                'created_by' => 1,
                'updated_by' => 1
        ]);
        //
        DB::table ( 'users' )->insert ( [
                'id' => 2,
                'name' => 'Client',
                'username' => "client@success-ss.com.vn",
                'email' => 'client@success-ss.com.vn',
                'password' => Hash::make ( '123456' ),
                'remember_token' => null,
                'agency_id' => 1,
                'parent_id' => 1,
                'country' => 'SG',
                'language' => 'en',
                'time_zone' => 'Asia/Singapore',
                'status' => 'ENABLED',
                'type' => 'GROUP2',
                'encrypted' => 0,
                'blocked' => 0,
                'billing_type' => 'ONE_TIME',
                'credits' => 0,
                'credits_usage' => 0,
                'credits_limit' => 0,
                'currency' => 'USD',
                'default_price_sms' => 0.2,
                'sender' => '{"Verify":"Verify"}',
                'created_by' => 1,
                'updated_by' => 1
        ]);
        //
        DB::table ( 'users' )->insert ( [
                'id' => 3,
                'name' => 'User',
                'username' => 'user@success-ss.com.vn',
                'email' => 'user@success-ss.com.vn',
                'password' => Hash::make ( '123456' ),
                'remember_token' => null,
                'agency_id' => 1,
                'parent_id' => 2,
                'country' => 'SG',
                'language' => 'en',
                'time_zone' => 'Asia/Singapore',
                'status' => 'ENABLED',
                'type' => 'GROUP3',
                'encrypted' => 0,
                'blocked' => 0,
                'billing_type' => 'ONE_TIME',
                'credits' => 0,
                'credits_usage' => 0,
                'credits_limit' => 0,
                'currency' => 'USD',
                'default_price_sms' => 0.2,
                'sender' => '{"Verify":"Verify"}',
                'created_by' => 1,
                'updated_by' => 1
        ]);

        DB::table ( 'subscriber_lists' )->insert ( [
                'id' => 1,
                'name' => 'Global Suppression List',
                'user_id' => 3,
                'is_global' => true,
                'created_by' => 1,
                'updated_by' => 1,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s')
        ]);

        //DB::statement ( 'CALL generateCampaignTableByUser(1)' );
        //DB::statement ( 'CALL generateCampaignTableByUser(2)' );
        DB::statement ( 'CALL generateCampaignTableByUser(3)' );
        //DB::statement ( 'CALL new_template_template(1)' );
        //DB::statement ( 'CALL new_template_template(2)' );
        DB::statement ( 'CALL new_template_template(3)' );
        //DB::statement ( 'CALL new_report_summary_template(1)' );
        //DB::statement ( 'CALL new_report_summary_template(2)' );
        DB::statement ( 'CALL new_report_summary_template(3)' );
        // Price configuration
        DB::statement ( 'CALL generatePriceConfigurationTableByUser(2)' );
        DB::statement ( 'CALL generatePriceConfigurationTableByUser(3)' );
        // Global Suppression List
        DB::statement ( 'CALL new_subscriber_template(1)' );
    }
}
