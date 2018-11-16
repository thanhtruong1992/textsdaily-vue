<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportSubscriberListChart extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // creat store procedure report subscriber list chart
        DB::unprepared( 'CREATE PROCEDURE `report_subscriber_list_chart`(
        	IN userID INT,
            IN listID INT
        )
        BEGIN
        	declare tmp_table_name VARCHAR(255) DEFAULT "report_list_summary_u_";
            
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

            IF(userID is not null) THEN
        		SET tmp_table_name = CONCAT(tmp_table_name, userID);
            END IF;

            START TRANSACTION;
        		SET @qr = CONCAT("SELECT SUM(`expenses`) AS total, country FROM ", tmp_table_name, " WHERE list_id = ", listID, " GROUP BY country ORDER BY total DESC, country ASC LIMIT 0, 5");
                PREPARE qr FROM @qr;
        		EXECUTE qr;
        		DEALLOCATE PREPARE qr;
            COMMIT;
        END' );

        DB::unprepared( "CREATE PROCEDURE `report_subscriber_list_data`(
        	IN userID INT,
            IN listID INT,
            IN listCountry LONGTEXT
        )
        BEGIN
        	declare tmp_table_name VARCHAR(255) DEFAULT 'report_list_summary_u_';
            
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

            IF(userID is not null) THEN
        		SET tmp_table_name = CONCAT(tmp_table_name, userID);
            END IF;

            START TRANSACTION;
        		SET @qr = CONCAT('SELECT SUM(`expenses`) AS total_price, country, network, SUM(pending) AS pending, SUM(totals) AS totals, SUM(failed) AS failed, SUM(delivered) AS delivered, SUM(failed) AS failed, SUM(expired) AS expired, expenses  FROM ', tmp_table_name, ' WHERE list_id = ', listID, listCountry, ' GROUP BY network, country ORDER BY country ASC , network ASC, total_price DESC');
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
        $sql2 = "DROP PROCEDURE IF EXISTS report_subscriber_list_data";
        DB::connection ()->getPdo ()->exec ( $sql2 );

        $sql2 = "DROP PROCEDURE IF EXISTS report_subscriber_list_chart";
        DB::connection ()->getPdo ()->exec ( $sql2 );
    }
}
