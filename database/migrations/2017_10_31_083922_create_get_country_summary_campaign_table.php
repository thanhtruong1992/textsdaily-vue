<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGetCountrySummaryCampaignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared( "CREATE PROCEDURE `get_country_network_summary_cammpaign`(
			IN userID INT,
        	IN listID VARCHAR(255),
            IN totalSMS INT,
            IN defaultPriceSMS DOUBLE
        )
        BEGIN
        	declare list_id VARCHAR(255) default \"\";
        	declare x INT default 0;
        	declare string_query LONGTEXT default \"\";
			
			DECLARE EXIT HANDLER FOR SQLEXCEPTION
			BEGIN
				-- ERROR
				ROLLBACK;
			END;

			DECLARE EXIT HANDLER FOR SQLWARNING
			BEGIN
				-- WARNING
				ROLLBACK;
			END;

        	IF LENGTH(listID) > 0 THEN
        		SET list_id = listID;
        	END IF;

        	SET @count = (LENGTH(list_id) - LENGTH(REPLACE(list_id, \",\", \"\")))/LENGTH(\",\") + 1;
        	SET @count = ROUND(@count, 0);
        	loop_insert: LOOP
        		SET x = x + 1;
        		SET @list_id = TRIM(REPLACE(SUBSTRING(SUBSTRING_INDEX(list_id, \",\", x), LENGTH(SUBSTRING_INDEX(list_id, \",\", x-1)) + 1), \",\", \"\"));

        		IF LENGTH(string_query) = 0 THEN
        			SET string_query = CONCAT(\"SELECT `phone`, `status`, IFNULL(country, 'Unknown') AS country, IFNULL(network, 'Unknown') AS network FROM subscribers_l_\", @list_id, \" WHERE status = 'SUBSCRIBED'\");
        		ELSE
        			SET string_query = CONCAT(string_query, \" UNION ALL SELECT `phone`, `status`, IFNULL(country, 'Unknown') AS country, IFNULL(network, 'Unknown') AS network FROM subscribers_l_\", @list_id, \" WHERE status = 'SUBSCRIBED'\");
        		END IF;

        		IF( x = @count) THEN
        			LEAVE loop_insert;
        		END IF;
        	END LOOP loop_insert;

            START TRANSACTION;
        		SET @qr = CONCAT(\"SELECT T3.country, T3.network, T3.TotalRecipients, T3.price, (TotalRecipients * \", totalSMS ,\") AS TotalSMS FROM (SELECT T1.country, T1.network, COALESCE(T2.price, \", defaultPriceSMS ,\") AS price, count(T1.network) AS TotalRecipients FROM (\", string_query ,\") T1  LEFT JOIN  price_configuration_u_\", userID ,\" T2  ON T1.country =  T2.country AND T1.network = T2.network GROUP BY T1.country, T1.network) AS T3 ORDER BY T3.country ASC, T3.network ASC, T3.TotalRecipients DESC;\");
                PREPARE qr FROM @qr;
        		EXECUTE qr;
        		DEALLOCATE PREPARE qr;
            COMMIT;
        END" );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    	$sql = "DROP PROCEDURE IF EXISTS get_country_network_summary_cammpaign";
    	DB::connection ()->getPdo ()->exec ( $sql );
    }
}
