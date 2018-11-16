<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePriceConfiguration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create( 'price_configuration_u_template', function (Blueprint $table) {
            $table->increments ( 'id' );
            $table->string('country', 5);
            $table->string('network', 100)->nullable();
            $table->double('price')->default(0);
            $table->tinyInteger('disabled')->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps ();
            // Index
            $table->unique(['country', 'network']);
        });

        // Stored procedures
        DB::unprepared ( "CREATE PROCEDURE generatePriceConfigurationTableByUser( IN user_id INT )
            BEGIN

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

                START TRANSACTION;

                    IF(user_id IS NOT NULL) THEN

                        SET @qrDropTable = CONCAT('DROP TABLE IF EXISTS price_configuration_u_', user_id, ';');
                        PREPARE qr FROM @qrDropTable;
                        EXECUTE qr;
                        DEALLOCATE PREPARE qr;

                        SET @queryCreateTable = CONCAT('CREATE TABLE price_configuration_u_', user_id, ' LIKE price_configuration_u_template;');
                        PREPARE qr FROM @queryCreateTable;
                        EXECUTE qr;
                        DEALLOCATE PREPARE qr;

                    END IF;

                COMMIT;
            END
        " );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists ( 'price_configuration_u_template' );
        // Stored procedures
        DB::unprepared ( "DROP PROCEDURE IF EXISTS generatePriceConfigurationTableByUser" );
    }
}
