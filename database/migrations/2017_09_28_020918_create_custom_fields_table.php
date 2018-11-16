<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
class CreateCustomFieldsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create ( 'custom_fields', function (Blueprint $table) {
            $table->increments ( 'id' );
            $table->integer ( 'user_id' )->unsigned ();
            $table->integer ( 'list_id' )->unsigned ();
            $table->string ( 'field_name', 45 );
            $table->enum ( 'field_type', [
                    'single_line'
            ] )->default ( "single_line" );
            $table->string ( 'field_default_value', 255 );
            $table->boolean ( 'required' );
            $table->boolean ( 'unique' );
            $table->boolean ( 'global' );
            $table->integer ( 'created_by' );
            $table->integer ( 'updated_by' );
            $table->timestamps ();
        } );

        DB::unprepared ( "CREATE PROCEDURE `add_customfield`(IN str_table_name VARCHAR(255), IN nameColunm VARCHAR(255))
            BEGIN
                DECLARE table_name_str	LONGTEXT DEFAULT '';
                DECLARE name_colunm LONGTEXT DEFAULT '';

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

                IF LENGTH(nameColunm) > 0 THEN
                    SET name_colunm = CONCAT('',nameColunm);
                END IF;

                START TRANSACTION;
                    SET @query = CONCAT(\"ALTER TABLE \", table_name_str, \" ADD \", nameColunm, \" VARCHAR(255) NULL;\");
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
        Schema::dropIfExists ( 'custom_fields' );

        $sql = "DROP PROCEDURE IF EXISTS add_customfield";
        DB::connection ()->getPdo ()->exec ( $sql );
    }
}
