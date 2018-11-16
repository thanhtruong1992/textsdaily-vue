<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class CreateTemplateTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        //
        Schema::create ( 'template_u_template', function (Blueprint $table) {
            $table->increments ( 'id' );
            $table->string ( 'name', 255 );
            $table->enum ( 'language', [ 
                    'ASCII',
                    'UNICODE' 
            ] )->default ( 'ASCII' );
            $table->text ( 'message' );
            $table->unsignedInteger ( 'created_by' );
            $table->unsignedInteger ( 'updated_by' )->nullable ();
            $table->timestamps();
        } );
        
        // create store generate new template table
        DB::unprepared ( "CREATE PROCEDURE new_template_template(IN user_id INT)
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
                    
                IF (user_id is not null) THEN
                    SET str_name_table = CONCAT('template_u_', user_id);
                END IF;
                    
                START TRANSACTION;
                    SET @query = CONCAT('CREATE TABLE ', str_name_table, ' LIKE template_u_template;');
                    
                PREPARE qr FROM @query;
                EXECUTE qr;
                DEALLOCATE PREPARE qr;
                    
                COMMIT;
            END
        " );
        
        // create store delete template table
        DB::unprepared ( "CREATE PROCEDURE remove_template_template(IN user_id INT)
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
                    
                IF (user_id is not null) THEN
                    SET str_name_table = CONCAT('template_u_', user_id);
                END IF;
                    
                START TRANSACTION;
                    SET @query = CONCAT('DROP TABLE ', str_name_table, ';');
                    
                PREPARE qr FROM @query;
                EXECUTE qr;
                DEALLOCATE PREPARE qr;
                    
                COMMIT;
            END
        " );
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        //
        Schema::dropIfExists ( 'template_u_template' );
        
        //
        $sql1 = "DROP PROCEDURE IF EXISTS new_template_template";
        DB::connection ()->getPdo ()->exec ( $sql1 );
        
        //
        $sql2 = "DROP PROCEDURE IF EXISTS remove_template_template";
        DB::connection ()->getPdo ()->exec ( $sql2 );
    }
}
