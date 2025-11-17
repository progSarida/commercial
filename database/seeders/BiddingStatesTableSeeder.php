<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BiddingStatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('bidding_states')->delete();
        
        DB::table('bidding_states')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Da valutare',
                'description' => 'Da valutare',
                'position' => 1,
                'created_at' => '2025-02-05 09:26:12',
                'updated_at' => '2025-05-22 13:38:34',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Rimandato',
                'description' => 'Rimandato',
                'position' => 4,
                'created_at' => '2025-02-05 09:26:12',
                'updated_at' => '2025-06-06 10:00:49',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'In corso',
                'description' => 'In corso',
                'position' => 2,
                'created_at' => '2025-02-05 09:26:44',
                'updated_at' => '2025-05-22 12:46:41',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Inviato. In attesa di riscontro',
                'description' => 'Inviato. In attesa di riscontro',
                'position' => 5,
                'created_at' => '2025-02-05 09:26:44',
                'updated_at' => '2025-06-06 10:00:42',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Completato',
                'description' => 'Completato',
                'position' => 6,
                'created_at' => '2025-02-05 09:27:18',
                'updated_at' => '2025-06-06 10:00:35',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Annullato',
                'description' => 'Annullato',
                'position' => 12,
                'created_at' => '2025-02-07 08:10:13',
                'updated_at' => '2025-06-06 09:59:54',
            ),
            6 => 
            array (
                'id' => 8,
                'name' => 'Scartata',
                'description' => 'Scartata',
                'position' => 7,
                'created_at' => '2025-05-11 05:51:26',
                'updated_at' => '2025-06-06 10:00:30',
            ),
            7 => 
            array (
                'id' => 11,
                'name' => 'Non pertinente',
                'description' => 'Non pertinente',
                'position' => 9,
                'created_at' => '2025-05-11 06:04:46',
                'updated_at' => '2025-06-06 10:00:16',
            ),
            8 => 
            array (
                'id' => 12,
                'name' => 'Non fattibile',
                'description' => 'Non fattibile',
                'position' => 10,
                'created_at' => '2025-05-11 06:25:02',
                'updated_at' => '2025-06-06 10:00:09',
            ),
            9 => 
            array (
                'id' => 14,
                'name' => 'Chiedere rettifica',
                'description' => 'Chiedere rettifica',
                'position' => 13,
                'created_at' => '2025-05-11 07:40:43',
                'updated_at' => '2025-06-06 09:59:48',
            ),
            10 => 
            array (
                'id' => 15,
                'name' => 'Scartata-Distante',
                'description' => 'Scartata-Distante',
                'position' => 8,
                'created_at' => '2025-05-13 14:29:40',
                'updated_at' => '2025-06-06 10:00:23',
            ),
            11 => 
            array (
                'id' => 16,
                'name' => 'Non di interesse',
                'description' => 'Non di interesse',
                'position' => 11,
                'created_at' => '2025-05-13 14:32:34',
                'updated_at' => '2025-06-06 10:00:01',
            ),
            12 => 
            array (
                'id' => 17,
                'name' => 'Completato',
                'description' => 'Completato',
                'position' => 14,
                'created_at' => '2025-05-15 17:42:50',
                'updated_at' => '2025-06-06 09:59:39',
            ),
            13 => 
            array (
                'id' => 19,
                'name' => 'Fattibile',
                'description' => 'Fattibile',
                'position' => 3,
                'created_at' => '2025-06-06 09:59:25',
                'updated_at' => '2025-06-06 10:00:54',
            ),
        ));
        
        
    }
}