<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreDetectCountryNetworkServiceProviderOfPhone extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::unprepared ( "CREATE PROCEDURE `detect_country_network_service_provider_of_phone`(
        	IN phone VARCHAR(50),
            IN userID INT,
            IN defaultPrice DOUBLE
        )
        BEGIN
        	SET @qr = CONCAT(\"SELECT T1.number_pattern, T1.country, T1.network, IFNULL(T1.price, \", defaultPrice ,\") AS price, T2.service_provider FROM (SELECT T1.number_pattern, T1.country, T1.network, T2.price FROM mobile_pattern AS T1 LEFT JOIN price_configuration_u_\", userID ,\" AS T2 ON T1.country = T2.country AND T1.network = T2.network AND T2.disabled = 0 WHERE LENGTH('\", phone, \"') = LENGTH(T1.number_pattern) AND '\", phone , \"' LIKE CONCAT(REGEXP_REPLACE(number_pattern, '[*]', ''), '%')) AS T1 LEFT JOIN preferred_service_provider AS T2 ON T1.country = T2.country AND T1.network = T2.network\");
            PREPARE qr FROM @qr;
        	EXECUTE qr;
        	DEALLOCATE PREPARE qr;
        END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        $sql1 = "DROP PROCEDURE IF EXISTS detect_country_network_service_provider_of_phone";
        DB::connection ()->getPdo ()->exec ( $sql1 );
    }
}
