<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('company')->insert([
           'name'  => "Starbucks",
            'identification_verification' => 1
        ]);
        DB::table('company')->insert([
           'name'  => "Portal",
            'identification_verification' => 0
        ]);
    }
}
