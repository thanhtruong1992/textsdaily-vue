<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreDeltectCountryNetworkServiceProviderOfPhoneNew extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('CREATE PROCEDURE `detectCountryNetworkServiceProdiverOfPhone`(
            IN stringPhone LONGTEXT,
            IN userID INT,
            In parentID INT,
            IN defaultPrice FLOAT,
            IN priceParent FLOAT
        )
        BEGIN
            DECLARE allData LONGTEXT DEFAULT "";
            
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

            IF LENGTH(stringPhone) > 0 THEN
                SET stringPhone = CONCAT(stringPhone, ",");
                SET @i = 1;
                WHILE (LOCATE(\',\', stringPhone) > 0) DO
                    SET @phone = SUBSTRING(stringPhone,1, LOCATE(\',\',stringPhone)-1);
                    SET stringPhone = SUBSTRING(stringPhone, LOCATE(\',\',stringPhone) + 1);
                    
                    IF LENGTH(@phone) > 0 THEN
                    
                        SET @field_group_concat = CONCAT(@phone, " country, network");
                        
                        SET @queryStr = CONCAT("SELECT A1.phone, A1.country, A1.network, A1.service_provider, A1.client_price, IF(A2.price IS NULL, \'", priceParent ,"\', A2.price) AS agency_price FROM (SELECT B.phone, B.country, B.network, B.service_provider, IF(T3.price IS NULL, \'", defaultPrice ,"\', T3.price) AS client_price FROM (SELECT T1.phone, T1.country, T1.network, T2.service_provider FROM (SELECT X2.phone, X1.country, X1.network FROM (SELECT \'", @phone ,"\' AS phone, country, network FROM mobile_pattern WHERE LOCATE(REPLACE(number_pattern, \'*\', \'\'), \'", @phone ,"\') > 0) AS X1 RIGHT JOIN (SELECT \'", @phone ,"\' as phone, null as country, null as network) AS X2 ON X1.phone = X2.phone) AS T1 INNER JOIN preferred_service_provider AS T2 ON T1.country = T2.country AND T1.network = T2.network) AS B LEFT JOIN (SELECT * FROM price_configuration_u_", userID ," WHERE disabled = 0 AND ((network IS NOT NULL AND price > 0) OR price > 0)) AS T3 ON IF (T3.network IS NULL, B.country = T3.country, (B.country = T3.country AND B.network = T3.network)) ORDER BY T3.network DESC limit 1 OFFSET 0) AS A1 LEFT JOIN (SELECT * FROM price_configuration_u_", parentID ," WHERE disabled = 0 AND ((network IS NOT NULL AND price > 0) OR price > 0)) AS A2 ON IF (A2.network IS NULL, A1.country = A2.country, (A1.country = A2.country AND A1.network = A2.network)) ORDER BY A2.network DESC limit 1 OFFSET 0");
                        
                        SET @dataPhone = CONCAT("SELECT * FROM (", @queryStr, ") AS C", @i);
        
                        IF LENGTH(allData) = 0 THEN
                            SET allData = CONCAT(@dataPhone);
                        ELSE
                            SET allData = CONCAT(allData, " UNION ALL ", @dataPhone);
                        END IF;
                        SET @i = @i + 1;
                    END IF;
                    
                END WHILE;
                
                SET @qr = CONCAT(allData);
                PREPARE qr FROM @qr;
                EXECUTE qr;
                DEALLOCATE PREPARE qr;
            END IF;
        END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        $sql = "DROP PROCEDURE IF EXISTS detectCountryNetworkServiceProdiverOfPhone";
        DB::connection ()->getPdo ()->exec ( $sql );
    }
}
