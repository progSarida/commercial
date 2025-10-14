<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BiddingDataSourcesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        DB::table('bidding_data_sources')->delete();

        DB::table('bidding_data_sources')->insert(array (
            0 =>
            array (
                'id' => 1,
                'name' => 'Contatto diretto',
                'description' => '',
                'position' => 1,
                'created_at' => '2025-02-11 15:19:13',
                'updated_at' => '2025-02-11 15:19:13',
            ),
            1 =>
            array (
                'id' => 2,
                'name' => 'Roga',
                'description' => '',
                'position' => 2,
                'created_at' => '2025-02-11 15:19:13',
                'updated_at' => '2025-02-11 15:19:13',
            ),
            2 =>
            array (
                'id' => 3,
                'name' => 'Presidia',
                'description' => '',
                'position' => 3,
                'created_at' => '2025-02-11 15:19:32',
                'updated_at' => '2025-02-11 15:19:32',
            ),
            3 =>
            array (
                'id' => 4,
                'name' => 'Telemat',
                'description' => '',
                'position' => 4,
                'created_at' => '2025-02-11 15:19:32',
                'updated_at' => '2025-02-11 15:19:32',
            ),
            4 =>
            array (
                'id' => 5,
                'name' => 'Mondo Appalti',
                'description' => '',
                'position' => 5,
                'created_at' => '2025-02-11 15:38:14',
                'updated_at' => '2025-02-11 15:38:14',
            ),
        ));


    }
}
