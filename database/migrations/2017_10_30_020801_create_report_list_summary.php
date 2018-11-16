<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class CreateReportListSummary extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        // create repotr _list_summary_u_xxx template
        Schema::create ( 'report_list_summary_u_template', function (Blueprint $table) {
            $table->increments ( 'id' );
            $table->integer ( 'campaign_id' );
            $table->integer ( 'list_id' );
            $table->string ( 'country', 5 )->nullable();
            $table->string ( 'network', 100 )->nullable();
            $table->string ( 'service_provider', 45 );
            $table->string ( 'currency', 5 )->nullable();
            $table->integer ( 'pending' )->default ( 0 );
            $table->integer ( 'totals' )->default ( 0 );
            $table->integer ( 'failed' )->default ( 0 );
            $table->integer ( 'delivered' )->default ( 0 );
            $table->integer( 'expired' )->default( 0 );
            $table->double('expenses')->default(0);
            $table->double('agency_expenses')->default(0);
            $table->double('client_expenses')->default(0);
            $table->timestamps ();
        } );

        // creat store procedure clone table report_list_summary_template
        DB::unprepared( "CREATE PROCEDURE `new_report_summary_template`(
        	IN userID INT
        )
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

        	IF (userID is not null) THEN
        		SET str_name_table = CONCAT('report_list_summary_u_', userID);
        	END IF;

        	START TRANSACTION;
        		SET @query = CONCAT('CREATE TABLE ', str_name_table, ' LIKE report_list_summary_u_template;');
        		PREPARE qr FROM @query;
        		EXECUTE qr;
        		DEALLOCATE PREPARE qr;

        	COMMIT;
        END" );

        // create store remove report summary template
        DB::unprepared( "CREATE PROCEDURE `remove_report_summary_template`( IN userID INT )
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

        	IF (userID is not null) THEN
        		SET str_name_table = CONCAT('report_list_summary_u_', userID);
        	END IF;

        	START TRANSACTION;
        		SET @query = CONCAT('DROP TABLE ', str_name_table, ';');
        		PREPARE qr FROM @query;
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
    public function down() {
        Schema::dropIfExists ( 'report_list_summary_u_template' );

        $sql1 = "DROP PROCEDURE IF EXISTS remove_report_summary_template";
        DB::connection ()->getPdo ()->exec ( $sql1 );
        $sql2 = "DROP PROCEDURE IF EXISTS new_report_summary_template";
        DB::connection ()->getPdo ()->exec ( $sql2 );
    }
}
