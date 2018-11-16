<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldUsernameIntoTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add column username and remove unique of column email
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('name');
            $table->dropUnique('users_email_unique');
        });

        // clone data email into username
        $statement = "UPDATE users AS U1 INNER JOIN users AS U2 ON U1.id = U2.id SET U1.username = U2.email;";
        DB::unprepared($statement);

        // remove not null of column username
        $statement = "ALTER TABLE `users` MODIFY COLUMN username VARCHAR(191) NOT NULL;";
        DB::unprepared($statement);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
