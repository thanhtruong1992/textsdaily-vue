<?php

use Illuminate\Database\Seeder;

class TemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('template_u_1')->insert([
                'id'            => 1,
                'name'          => 'Template sample 001',
                'language'         => 'ASCII',
                'message'      => 'Hello %%firstname%% %%lastname%%, this is test message was sent to %%phone%%.',
                'created_by'    => 1,
                'updated_by'    => 1,
        ]);

        //
        DB::table('template_u_1')->insert([
                'id'            => 2,
                'name'          => 'Template sample 002',
                'language'         => 'UNICODE',
                'message'      => 'Hello %%firstname%% %%lastname%%, this is test message number 2 was sent to %%phone%%.',
                'created_by'    => 1,
                'updated_by'    => 1,
        ]);

        //
        DB::table('template_u_2')->insert([
                'id'            => 1,
                'name'          => 'Template sample 001',
                'language'         => 'ASCII',
                'message'      => 'Hello %%firstname%% %%lastname%%, this is test message was sent to %%phone%%.',
                'created_by'    => 2,
                'updated_by'    => 2,
        ]);

        //
        DB::table('template_u_2')->insert([
                'id'            => 2,
                'name'          => 'Template sample 002',
                'language'         => 'UNICODE',
                'message'      => 'Hello %%firstname%% %%lastname%%, this is test message number 2 was sent to %%phone%%.',
                'created_by'    => 2,
                'updated_by'    => 2,
        ]);


        DB::table('template_u_3')->insert([
                'id'            => 1,
                'name'          => 'Template sample 001',
                'language'         => 'ASCII',
                'message'      => 'Hello %%firstname%% %%lastname%%, this is test message was sent to %%phone%%.',
                'created_by'    => 3,
                'updated_by'    => 3,
        ]);

        //
        DB::table('template_u_3')->insert([
                'id'            => 2,
                'name'          => 'Template sample 002',
                'language'         => 'UNICODE',
                'message'      => 'Hello %%firstname%% %%lastname%%, this is test message number 2 was sent to %%phone%%.',
                'created_by'    => 3,
                'updated_by'    => 3,
        ]);
    }
}
