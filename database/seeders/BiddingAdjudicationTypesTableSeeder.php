<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BiddingAdjudicationTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('bidding_adjudication_types')->delete();
        
        DB::table('bidding_adjudication_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Massimo Ribasso',
                'description' => NULL,
                'position' => 2,
                'created_at' => '2025-02-05 09:29:36',
                'updated_at' => '2025-05-09 09:13:46',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Offerta Economicamente più vantaggiosa',
                'description' => NULL,
                'position' => 1,
                'created_at' => '2025-02-05 09:29:36',
                'updated_at' => '2025-05-09 09:13:51',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Non prevista',
                'description' => NULL,
                'position' => 4,
                'created_at' => '2025-02-28 14:09:08',
                'updated_at' => '2025-05-09 11:30:14',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Prezzo piu\' alto',
                'description' => 'Prezzo piu\' alto',
                'position' => 3,
                'created_at' => '2025-05-09 11:30:07',
                'updated_at' => '2025-05-09 11:30:07',
            ),
        ));
        
        
    }
}