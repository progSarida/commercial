<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BiddingTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        DB::table('bidding_types')->delete();

        DB::table('bidding_types')->insert(array (
            0 =>
            array (
                'id' => 1,
                'name' => 'RdO',
                'description' => NULL,
                'position' => 5,
                'created_at' => '2025-02-05 09:28:03',
                'updated_at' => '2025-05-09 09:32:06',
            ),
            1 =>
            array (
                'id' => 2,
                'name' => 'Lettera di Invito',
                'description' => NULL,
                'position' => 3,
                'created_at' => '2025-02-05 09:28:03',
                'updated_at' => '2025-05-09 09:32:33',
            ),
            2 =>
            array (
                'id' => 3,
                'name' => 'Manifestazione di Interesse',
                'description' => NULL,
                'position' => 2,
                'created_at' => '2025-02-05 09:28:27',
                'updated_at' => '2025-05-09 09:32:42',
            ),
            3 =>
            array (
                'id' => 4,
                'name' => 'Avviso Pubblico',
                'description' => '',
                'position' => 4,
                'created_at' => '2025-02-05 09:28:27',
                'updated_at' => '2025-02-05 09:28:27',
            ),
            4 =>
            array (
                'id' => 5,
                'name' => 'Procedura Aperta',
                'description' => NULL,
                'position' => 1,
                'created_at' => '2025-02-05 09:28:39',
                'updated_at' => '2025-05-09 09:32:12',
            ),
            5 =>
            array (
                'id' => 6,
                'name' => 'Trattativa',
                'description' => '',
                'position' => 6,
                'created_at' => '2025-02-07 08:10:53',
                'updated_at' => '2025-02-07 08:10:53',
            ),
            6 =>
            array (
                'id' => 7,
                'name' => 'Trattativa Privata',
                'description' => '',
                'position' => 7,
                'created_at' => '2025-02-07 08:10:53',
                'updated_at' => '2025-02-07 08:10:53',
            ),
            7 =>
            array (
                'id' => 8,
                'name' => 'Iscrizione Albo Fornitori',
                'description' => '',
                'position' => 8,
                'created_at' => '2025-02-12 15:00:48',
                'updated_at' => '2025-02-12 15:00:48',
            ),
            8 =>
            array (
                'id' => 9,
                'name' => 'Indagine di mercato',
                'description' => '',
                'position' => 9,
                'created_at' => '2025-02-13 14:19:39',
                'updated_at' => '2025-02-13 14:19:39',
            ),
            9 =>
            array (
                'id' => 10,
                'name' => 'Procedura Negoziata',
                'description' => NULL,
                'position' => 10,
                'created_at' => '2025-02-28 14:08:38',
                'updated_at' => '2025-02-28 14:08:46',
            ),
            10 =>
            array (
                'id' => 11,
                'name' => 'Procedura ristretta',
                'description' => 'Procedura ristretta',
                'position' => 11,
                'created_at' => '2025-05-13 07:42:10',
                'updated_at' => '2025-05-13 07:42:10',
            ),
            11 =>
            array (
                'id' => 12,
                'name' => 'Non prevista',
                'description' => 'Non prevista',
                'position' => 12,
                'created_at' => '2025-06-06 10:02:44',
                'updated_at' => '2025-06-06 10:02:53',
            ),
        ));


    }
}
