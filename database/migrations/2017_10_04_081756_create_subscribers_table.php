<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
class CreateSubscribersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        // create subscriber template
        Schema::create ( 'subscribers_l_template', function (Blueprint $table) {
            $table->increments ( 'id' );
            $table->string ( 'phone', 20 );
            $table->string ( 'country', 5 )->nullable ();
            $table->string ( 'network', 100 )->nullable ();
            $table->tinyInteger('ported')->default(0);
            $table->string ( 'mccmnc', 15 )->nullable ();
            $table->string( 'service_provider', 25 )->nullable ();
            $table->enum ( 'status', [
                    'SUBSCRIBED',
                    'UNSUBSCRIBED',
                    'INVALID'
            ] )->default ( 'SUBSCRIBED' );
            $table->timestamp ( 'unsubscription_date' )->nullable ();
            $table->string ( 'title', 5 )->nullable ();
            $table->string ( 'first_name', 191 )->nullable ();
            $table->string ( 'last_name', 191 )->nullable ();
            $table->enum ( 'detect_status', ['PENDING', 'PROCESSING', 'PROCESSED'] )->default ( 'PENDING' );
            $table->timestamp('detect_updated_at')->nullable ();
            $table->unsignedInteger ( 'created_by' )->nullable ();
            $table->unsignedInteger ( 'updated_by' )->nullable ();
            $table->timestamps ();
        } );

        // create store generate new subscriber table
        DB::unprepared ( "CREATE PROCEDURE new_subscriber_template(IN list_id INT)
            BEGIN
                DECLARE str_name_table LONGTEXT DEFAULT '';

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

                IF (list_id is not null) THEN
                    SET str_name_table = CONCAT('subscribers_l_', list_id);
                END IF;

                START TRANSACTION;
                    SET @query = CONCAT('CREATE TABLE ', str_name_table, ' LIKE subscribers_l_template;');

                PREPARE qr FROM @query;
                EXECUTE qr;
                DEALLOCATE PREPARE qr;

                COMMIT;
            END
        " );

        // create store delete subscriber table
        DB::unprepared ( "CREATE PROCEDURE `delete_subscriber_template`(IN list_id INT)
            BEGIN
                DECLARE str_name_table LONGTEXT DEFAULT '';

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

                IF (list_id is not null) THEN
                    SET str_name_table = CONCAT('subscribers_l_', list_id);
                END IF;

                START TRANSACTION;
                    SET @query = CONCAT('DROP TABLE ', str_name_table, ';');

                PREPARE qr FROM @query;
                EXECUTE qr;
                DEALLOCATE PREPARE qr;

                COMMIT;
            END
        " );

        DB::unprepared ( "CREATE PROCEDURE clone_table_subscriber(
    	       IN str_table_name VARCHAR(255),
    	       IN str_table_temp_name VARCHAR(255)
            )
            BEGIN
	           DECLARE table_name_str	LONGTEXT DEFAULT '';
	           DECLARE table_temp_name_str LONGTEXT DEFAULT '';

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

	           IF LENGTH(str_table_name) > 0 THEN
		          SET table_name_str = str_table_name;
	           END IF;

	           IF LENGTH(str_table_temp_name) > 0 THEN
		          SET table_temp_name_str = str_table_temp_name;
	           END IF;

	           START TRANSACTION;
                  -- drop table
                  SET @query = CONCAT('DROP TABLE IF EXISTS ', table_temp_name_str, ';');
                  PREPARE qr FROM @query;
		          EXECUTE qr;
		          DEALLOCATE PREPARE qr;

		          SET @query = CONCAT('CREATE TABLE ', table_temp_name_str, ' LIKE ' , table_name_str, ';');
		          PREPARE qr FROM @query;
		          EXECUTE qr;
		          DEALLOCATE PREPARE qr;
                  select true;
	           COMMIT;
            END
        ");

        DB::unprepared ( "CREATE PROCEDURE `move_data_subscribers`(
    	       IN table_tmp 		VARCHAR(255),	-- Table temporary contains subscribers need import
    	       IN table_subscriber	VARCHAR(255),	-- Table subscribers for import
    	       IN UpdateIfDuplicate	boolean,	-- Flag update subscriber exists
    	       IN update_fields	LONGTEXT,			-- Fields for update subscribers exists. Format: CustomField001,CustomField002
    	       IN list_id			INT,			-- List ID
    	       IN user_id			INT,				-- User ID
               IN statusUpdate VARCHAR (255),
               IN flagUpdate BOOLEAN
            )
        BEGIN

	       DECLARE str_update_fields	LONGTEXT DEFAULT '';
	       DECLARE qr_update			LONGTEXT DEFAULT '';
	       DECLARE str_insert_fields	LONGTEXT DEFAULT '';
           DECLARE str_trim_insert_fields	LONGTEXT DEFAULT '';
	       DECLARE field_group_concat	LONGTEXT DEFAULT '';

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

	       SET SESSION group_concat_max_len = 4294967295;

	       IF LENGTH(update_fields) > 0 THEN
		       SET str_update_fields = GENERATE_UPDATE_FIELDS(update_fields, ',', 'SUBS', 'TEMP');
	           END IF;

	       START TRANSACTION;

               -- STEP 1: STATISTICS FOR LOGS IMPORT
    		   IF LENGTH(update_fields) > 0 THEN
    	           SET field_group_concat = CONCAT('TEMP.', REPLACE(update_fields, ',', ',\", \",TEMP.'));
    		   ELSE
    		       SET field_group_concat = 'TEMP.phone';
    		   END IF;

               -- STEP COUNT SUBSCIBER
               SET @qr = CONCAT('SELECT COUNT(id) INTO @TotalSubscribers FROM ', table_tmp, ';');
    		   PREPARE qr FROM @qr;
    		   EXECUTE qr;
    		   DEALLOCATE PREPARE qr;

                -- STEP VALIDATE PHONE SUBCRIBER
    		   SET @qr = CONCAT(\"SELECT GROUP_CONCAT(\", field_group_concat, \" SEPARATOR '\n') INTO @InvalidData FROM \", table_tmp, \" AS TEMP WHERE TRIM(TEMP.phone) NOT REGEXP '([\+]?[0-9 ]){1,15}$';\");
    		   PREPARE qr FROM @qr;
    		   EXECUTE qr;
    		   DEALLOCATE PREPARE qr;

               -- STEP DELETE INVALID PHONE SUBCRIBER
    		   SET @qr = CONCAT(\"DELETE FROM \", table_tmp, \" WHERE TRIM(phone) NOT REGEXP '([\+]?[0-9 ]){1,15}$';\");
    		   PREPARE qr FROM @qr;
    		   EXECUTE qr;
    		   DEALLOCATE PREPARE qr;

    		   -- STEP 1.1: STATISTICS DUPLICATE
    		   SET @qr = CONCAT(\"SELECT GROUP_CONCAT(\", field_group_concat, \" SEPARATOR '\n') INTO @DuplicateData FROM \", table_tmp, \" AS TEMP, \", table_tmp, \" AS T2 WHERE TEMP.id < T2.id AND TRIM(TEMP.phone) = TRIM(T2.phone);\");
    		   PREPARE qr FROM @qr;
    		   EXECUTE qr;
    		   DEALLOCATE PREPARE qr;

    		   -- STEP 2: REMOVE DATA EMAIL DUPLICATE
    		   SET @qr = CONCAT('DELETE T1 FROM ', table_tmp, ' AS T1, ', table_tmp, ' AS T2 WHERE T1.id < T2.id AND TRIM(T1.phone) = TRIM(T2.phone);');
    		   PREPARE qr FROM @qr;
    		   EXECUTE qr;
    		   DEALLOCATE PREPARE qr;

			   IF (flagUpdate = 0) THEN
				   -- STEP 1.2: STATISTICS INSERT
				   SET @qr = CONCAT('SELECT COUNT(1) INTO @TotalInserted FROM ', table_tmp, ' AS TEMP LEFT JOIN ', table_subscriber, ' AS SUBS ON TRIM(SUBS.phone) = REGEXP_REPLACE(TEMP.`phone`, \"[+, ]\", \"\") WHERE SUBS.id IS NULL;');
				   PREPARE qr FROM @qr;
				   EXECUTE qr;
				   DEALLOCATE PREPARE qr;
			   ELSE
				   SET @qr = CONCAT(\"SELECT GROUP_CONCAT(\", field_group_concat, \" SEPARATOR '\n') INTO @SkipData FROM \", table_tmp, \" AS TEMP LEFT JOIN \", table_subscriber ,\" AS SUB ON REGEXP_REPLACE(TEMP.`phone`, '[+, ]', '') = SUB.phone WHERE SUB.phone IS NULL;\");
				   PREPARE qr FROM @qr;
				   EXECUTE qr;
				   DEALLOCATE PREPARE qr;
               END IF;

    		   -- STEP 1.3: STATISTICS UPDATE
    		   SET @qr = CONCAT('SELECT COUNT(1) INTO @TotalUpdated FROM ', table_subscriber, ' AS SUBS, ', table_tmp, ' AS TEMP WHERE TRIM(SUBS.phone) = REGEXP_REPLACE(TEMP.`phone`, \"[+, ]\", \"\");');
    		   PREPARE qr FROM @qr;
    		   EXECUTE qr;
    		   DEALLOCATE PREPARE qr;

               IF (flagUpdate = 0) THEN
					-- STEP 3: UPDATE SUBSCRIBERS EXIST
				   IF UpdateIfDuplicate = true AND LENGTH(str_update_fields) > 0 THEN
					  SET qr_update = CONCAT('UPDATE ', table_subscriber, ' AS SUBS, ', table_tmp, ' AS TEMP SET __STR_REPLACE__ WHERE TRIM(SUBS.phone) = REGEXP_REPLACE(TEMP.`phone`, \"[+, ]\", \"\");');
					  SET @qr = REPLACE(qr_update, '__STR_REPLACE__', str_update_fields);
					  PREPARE qr FROM @qr;
					  EXECUTE qr;
					  DEALLOCATE PREPARE qr;
				   END IF;

				   -- STEP 4: REMOVE SUBSCRIBERS EXIST AT TEMP TABLE
				   SET @qr = CONCAT('DELETE TEMP FROM ', table_subscriber, ' AS SUBS, ', table_tmp, ' AS TEMP WHERE TRIM(SUBS.phone) = TRIM(TEMP.phone);');
				   PREPARE qr FROM @qr;
				   EXECUTE qr;
				   DEALLOCATE PREPARE qr;

				   -- STEP 5: INSERT NEW SUBSCRIBERS
				   SET @qr = CONCAT(\"SELECT GROUP_CONCAT(COLUMN_NAME) INTO @str_insert FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '\", table_tmp, \"' AND COLUMN_NAME <> 'id';\");
				   PREPARE qr FROM @qr;
				   EXECUTE qr;
				   DEALLOCATE PREPARE qr;
				   SET str_insert_fields = @str_insert;

				   SET @qr = CONCAT(\"SELECT GROUP_CONCAT(CONCAT('TRIM(', COLUMN_NAME, ')')) INTO @str_insert_trim FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '\", table_tmp, \"' AND COLUMN_NAME <> 'id';\");
				   PREPARE qr FROM @qr;
				   EXECUTE qr;
				   DEALLOCATE PREPARE qr;
				   SET str_trim_insert_fields = REPLACE_SPECIAL_CHARACTER(@str_insert_trim);

				   SET @qr = CONCAT('INSERT INTO ', table_subscriber, '(', str_insert_fields, ') SELECT ', str_trim_insert_fields, ' FROM ', table_tmp, ';');
				   PREPARE qr FROM @qr;
				   EXECUTE qr;
				   DEALLOCATE PREPARE qr;
			   ELSE
				   SET @qr = CONCAT('UPDATE ', table_subscriber, ' AS SUBS, ', table_tmp, ' AS TEMP SET SUBS.status = \"', statusUpdate ,'\", SUBS.unsubscription_date = \"', now() ,'\" WHERE SUBS.phone = REGEXP_REPLACE(TEMP.`phone`, \"[+, ]\", \"\");');
				   PREPARE qr FROM @qr;
				   EXECUTE qr;
				   DEALLOCATE PREPARE qr;
               END IF;


    		   -- STEP 7: DROP TEMPORARY TABLE
    		   SET @qr = CONCAT('DROP TABLE ', table_tmp, ';');
    		   PREPARE qr FROM @qr;
    		   EXECUTE qr;
    		   DEALLOCATE PREPARE qr;

    		   -- STEP8: RETURN
    		   SELECT @TotalSubscribers AS TotalSubscribers, @TotalInserted AS TotalInserted, @TotalUpdated AS TotalUpdated, @DuplicateData AS DuplicateData, @InvalidData AS InvalidData, @SkipData AS SkipData;

	       COMMIT;

        END");

        DB::unprepared ( "CREATE FUNCTION GENERATE_UPDATE_FIELDS(
            str LONGTEXT, delim VARCHAR(5),
            refix_tbl_1 VARCHAR(25),
            refix_tbl_2 VARCHAR(25)
            ) RETURNS LONGTEXT CHARSET latin1
        BEGIN
	       DECLARE results LONGTEXT DEFAULT '';
	       DECLARE cur_text varchar(255) DEFAULT '';
	       DECLARE count INT DEFAULT 0;
	       DECLARE pos INT DEFAULT 1;


	       SET count = (LENGTH(str) - LENGTH(REPLACE(str, delim, '')))/LENGTH(delim) + 1;


	       WHILE pos <= count DO

		      SET cur_text = TRIM(REPLACE(SUBSTRING(SUBSTRING_INDEX(str, delim, pos), LENGTH(SUBSTRING_INDEX(str, delim, pos-1)) + 1), delim, ''));
		      IF CHAR_LENGTH(cur_text) > 0 THEN
			     SET results = CONCAT(results, refix_tbl_1, '.', cur_text, ' = ', refix_tbl_2, '.', cur_text, ', ');
		      END IF;
	          SET  pos = pos + 1;

	       END WHILE;

	       RETURN TRIM(BOTH ', ' FROM results);
        END
        ");

        DB::unprepared ("CREATE PROCEDURE `summary_subscribers`(
        	IN listID VARCHAR(255),
            IN userID INT,
            IN totalSMS INT,
            IN defaultPriceSMS DOUBLE
        )
        BEGIN
        	declare list_id VARCHAR(255) default \"\";
            declare tbl VARCHAR(255) default \"\";
            declare x INT default 0;
            declare string_query LONGTEXT default \"\";

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
        		-- STEP TOTAL SUNSCRIBER
        		SET @qr = CONCAT(\"SELECT COUNT(1) INTO @TotalSubscriber FROM (\", string_query, \")  AS T1;\");
        		PREPARE qr FROM @qr;
        		EXECUTE qr;
        		DEALLOCATE PREPARE qr;

                -- STEP TOTAL DUPLICATE
                SET @qr = CONCAT(\"SELECT SUM(total) INTO @TotalDuplicate FROM (SELECT phone, COUNT(1) AS total FROM (\", string_query, \")  AS T1 GROUP BY phone HAVING (COUNT(1) > 1)) AS T2;\");
        		PREPARE qr FROM @qr;
        		EXECUTE qr;
        		DEALLOCATE PREPARE qr;

                -- STEP TOTAL PRICE SMS
                SET @qr = CONCAT(\"SELECT SUM(COALESCE(T2.price, \", defaultPriceSMS ,\") * \", totalSMS ,\") INTO @TotalPrice FROM (\", string_query ,\") T1 LEFT JOIN  price_configuration_u_\", userID ,\" T2 ON T1.country =  T2.country AND T1.network = T2.network;\");
				PREPARE qr FROM @qr;
        		EXECUTE qr;
        		DEALLOCATE PREPARE qr;

                -- RETURN
        	    SELECT @TotalSubscriber AS TotalSubscriber, @TotalDuplicate AS TotalDuplicate, @TotalPrice AS TotalPrice;
        	COMMIT;

        END");

        DB::unprepared ( "CREATE FUNCTION `REPLACE_SPECIAL_CHARACTER`(
        	str LONGTEXT
        ) RETURNS LONGTEXT CHARSET latin1
        BEGIN
        	 DECLARE position INT DEFAULT 0;
             DECLARE phone VARCHAR(255) DEFAULT \"phone\";

             SET position = POSITION(\"phone\" IN str);

        	 IF position > 0 THEN
                SET phone = CONCAT(\"REGEXP_REPLACE(`phone`, '[+, ]', '')\");
             END IF;

             SET str = REPLACE(str,\"phone\", phone);
        	 RETURN str;
        END");
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        //
        Schema::dropIfExists ( 'subscribers_l_template' );

        //
        $sql1 = "DROP PROCEDURE IF EXISTS new_subscriber_template";
        DB::connection ()->getPdo ()->exec ( $sql1 );

        //
        $sql2 = "DROP PROCEDURE IF EXISTS delete_subscriber_template";
        DB::connection ()->getPdo ()->exec ( $sql2 );

        //
        $sql3 = "DROP PROCEDURE IF EXISTS clone_table_subscriber";
        DB::connection ()->getPdo ()->exec ( $sql3 );

    }
}
