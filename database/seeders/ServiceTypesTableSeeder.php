<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        DB::table('service_types')->delete();

        DB::table('service_types')->insert(array (
            0 =>
            array (
                'id' => 1,
                'name' => 'CDS',
                'description' => 'Violazioni al Codice Della Strada',
                'position' => 1,
                'mandatory' => 0,
                'ref' => 'cds',
                'created_at' => '2025-01-29 09:55:27',
                'updated_at' => '2025-03-07 06:36:29',
            ),
            1 =>
            array (
                'id' => 2,
                'name' => 'CUP',
                'description' => 'Canone Unico Patrimoniale',
                'position' => 6,
                'mandatory' => 0,
                'ref' => 'cup',
                'created_at' => '2025-01-29 09:55:27',
                'updated_at' => '2025-03-06 11:07:14',
            ),
            2 =>
            array (
                'id' => 3,
                'name' => 'IMU',
                'description' => 'Imposta Municipale Unica',
                'position' => 4,
                'mandatory' => 0,
                'ref' => 'imu',
                'created_at' => '2025-01-29 09:55:27',
                'updated_at' => '2025-03-06 11:02:59',
            ),
            3 =>
            array (
                'id' => 4,
                'name' => 'Gestione parcheggi',
                'description' => 'Gestione parcheggi',
                'position' => 11,
                'mandatory' => 0,
                'ref' => 'parc',
                'created_at' => '2025-01-29 09:55:27',
                'updated_at' => '2025-03-10 15:44:12',
            ),
            4 =>
            array (
                'id' => 5,
                'name' => 'Illuminazione votiva',
                'description' => 'Illuminazione votiva',
                'position' => 13,
                'mandatory' => 0,
                'ref' => 'vot',
                'created_at' => '2025-01-29 09:55:27',
                'updated_at' => '2025-03-10 14:34:41',
            ),
            5 =>
            array (
                'id' => 6,
                'name' => 'Apparecchiature',
                'description' => 'Noleggio apparecchiature elettroniche',
                'position' => 3,
                'mandatory' => 0,
                'ref' => 'app',
                'created_at' => '2025-01-29 09:55:27',
                'updated_at' => '2025-03-06 11:18:54',
            ),
            6 =>
            array (
                'id' => 7,
                'name' => 'Gestione integrale',
                'description' => 'Gestione integrale',
                'position' => 8,
                'mandatory' => 0,
                'ref' => 'int',
                'created_at' => '2025-02-20 15:24:43',
                'updated_at' => '2025-03-06 11:19:21',
            ),
            7 =>
            array (
                'id' => 9,
                'name' => 'Coattiva',
                'description' => 'Riscossione coattiva',
                'position' => 10,
                'mandatory' => 0,
                'ref' => 'coa',
                'created_at' => '2025-02-28 14:07:39',
                'updated_at' => '2025-03-10 14:34:15',
            ),
            8 =>
            array (
                'id' => 10,
                'name' => 'Parcometri',
                'description' => 'Parcometri',
                'position' => 12,
                'mandatory' => 0,
                'ref' => 'imp',
                'created_at' => '2025-03-06 10:49:18',
                'updated_at' => '2025-03-10 14:34:30',
            ),
            9 =>
            array (
                'id' => 11,
                'name' => 'TSRSU',
                'description' => 'Tassa-Tariffa rifiuti',
                'position' => 5,
                'mandatory' => 0,
                'ref' => 'tsrsu',
                'created_at' => '2025-03-06 10:52:56',
                'updated_at' => '2025-03-06 11:03:16',
            ),
            10 =>
            array (
                'id' => 12,
                'name' => 'Gestione ordinaria',
                'description' => 'Gestione ordinaria',
                'position' => 7,
                'mandatory' => 0,
                'ref' => 'ord',
                'created_at' => '2025-03-06 11:04:29',
                'updated_at' => '2025-03-06 11:19:10',
            ),
            11 =>
            array (
                'id' => 13,
                'name' => 'CDS Estere',
                'description' => 'Violazioni cds estere',
                'position' => 2,
                'mandatory' => 0,
                'ref' => 'est',
                'created_at' => '2025-03-06 11:06:28',
                'updated_at' => '2025-03-06 11:06:28',
            ),
            12 =>
            array (
                'id' => 14,
                'name' => 'Gestione cimiteri',
                'description' => 'Gestione cimiteri',
                'position' => 14,
                'mandatory' => 0,
                'ref' => 'cim',
                'created_at' => '2025-03-06 11:08:41',
                'updated_at' => '2025-03-10 14:34:48',
            ),
            13 =>
            array (
                'id' => 15,
                'name' => 'Altro',
                'description' => 'Altro',
                'position' => 17,
                'mandatory' => 0,
                'ref' => 'altr',
                'created_at' => '2025-03-06 11:20:02',
                'updated_at' => '2025-05-09 10:39:52',
            ),
            14 =>
            array (
                'id' => 16,
                'name' => 'Servizio idrico',
                'description' => 'Servizio idrico',
                'position' => 15,
                'mandatory' => 0,
                'ref' => 'acq',
                'created_at' => '2025-03-10 14:31:52',
                'updated_at' => '2025-03-10 14:35:01',
            ),
            15 =>
            array (
                'id' => 17,
                'name' => 'Gestione accertamenti',
                'description' => 'Gestione accertamenti',
                'position' => 9,
                'mandatory' => 0,
                'ref' => 'acc',
                'created_at' => '2025-03-10 14:32:45',
                'updated_at' => '2025-03-10 14:34:07',
            ),
            16 =>
            array (
                'id' => 18,
                'name' => 'Recupero crediti',
                'description' => 'Recupero crediti',
                'position' => 16,
                'mandatory' => 0,
                'ref' => 'rc',
                'created_at' => '2025-05-09 10:39:22',
                'updated_at' => '2025-05-09 10:40:04',
            ),
        ));


    }
}
