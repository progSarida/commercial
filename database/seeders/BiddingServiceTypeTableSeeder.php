<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BiddingServiceTypeTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('bidding_service_type')->delete();
        
        DB::table('bidding_service_type')->insert(array (
            0 => 
            array (
                'id' => 1,
                'bidding_id' => 2,
                'service_type_id' => 9,
                'created_at' => '2025-02-12 13:31:42',
                'updated_at' => '2025-03-05 16:45:49',
            ),
            1 => 
            array (
                'id' => 2,
                'bidding_id' => 9,
                'service_type_id' => 2,
                'created_at' => '2025-02-13 12:21:02',
                'updated_at' => '2025-03-05 16:41:53',
            ),
            2 => 
            array (
                'id' => 3,
                'bidding_id' => 11,
                'service_type_id' => 4,
                'created_at' => '2025-02-13 12:28:11',
                'updated_at' => '2025-03-14 15:15:15',
            ),
            3 => 
            array (
                'id' => 4,
                'bidding_id' => 12,
                'service_type_id' => 13,
                'created_at' => '2025-02-13 12:30:21',
                'updated_at' => '2025-03-10 14:46:09',
            ),
            4 => 
            array (
                'id' => 5,
                'bidding_id' => 17,
                'service_type_id' => 9,
                'created_at' => '2025-02-13 12:53:36',
                'updated_at' => '2025-03-05 16:41:11',
            ),
            5 => 
            array (
                'id' => 6,
                'bidding_id' => 20,
                'service_type_id' => 7,
                'created_at' => '2025-02-25 09:46:52',
                'updated_at' => '2025-03-05 16:49:13',
            ),
            6 => 
            array (
                'id' => 7,
                'bidding_id' => 21,
                'service_type_id' => 2,
                'created_at' => '2025-02-25 09:57:30',
                'updated_at' => '2025-03-05 16:48:28',
            ),
            7 => 
            array (
                'id' => 8,
                'bidding_id' => 22,
                'service_type_id' => 5,
                'created_at' => '2025-02-25 10:33:00',
                'updated_at' => '2025-03-05 16:39:52',
            ),
            8 => 
            array (
                'id' => 9,
                'bidding_id' => 23,
                'service_type_id' => 7,
                'created_at' => '2025-02-25 10:37:25',
                'updated_at' => '2025-03-05 16:48:02',
            ),
            9 => 
            array (
                'id' => 10,
                'bidding_id' => 24,
                'service_type_id' => 4,
                'created_at' => '2025-02-25 10:41:56',
                'updated_at' => '2025-03-20 11:50:34',
            ),
            10 => 
            array (
                'id' => 11,
                'bidding_id' => 25,
                'service_type_id' => 4,
                'created_at' => '2025-02-25 10:48:36',
                'updated_at' => '2025-03-10 14:43:08',
            ),
            11 => 
            array (
                'id' => 12,
                'bidding_id' => 26,
                'service_type_id' => 9,
                'created_at' => '2025-02-25 10:59:20',
                'updated_at' => '2025-03-18 14:21:00',
            ),
            12 => 
            array (
                'id' => 13,
                'bidding_id' => 27,
                'service_type_id' => 9,
                'created_at' => '2025-02-25 11:02:59',
                'updated_at' => '2025-03-05 16:41:45',
            ),
            13 => 
            array (
                'id' => 14,
                'bidding_id' => 30,
                'service_type_id' => 5,
                'created_at' => '2025-02-25 11:09:30',
                'updated_at' => '2025-03-05 16:47:22',
            ),
            14 => 
            array (
                'id' => 15,
                'bidding_id' => 35,
                'service_type_id' => 2,
                'created_at' => '2025-02-25 11:23:07',
                'updated_at' => '2025-03-05 16:47:10',
            ),
            15 => 
            array (
                'id' => 16,
                'bidding_id' => 36,
                'service_type_id' => 3,
                'created_at' => '2025-02-25 11:29:21',
                'updated_at' => '2025-03-05 16:42:22',
            ),
            16 => 
            array (
                'id' => 17,
                'bidding_id' => 36,
                'service_type_id' => 7,
                'created_at' => '2025-02-25 11:29:21',
                'updated_at' => '2025-03-05 16:42:22',
            ),
            17 => 
            array (
                'id' => 18,
                'bidding_id' => 36,
                'service_type_id' => 9,
                'created_at' => '2025-02-25 11:29:21',
                'updated_at' => '2025-03-05 16:42:22',
            ),
            18 => 
            array (
                'id' => 19,
                'bidding_id' => 38,
                'service_type_id' => 4,
                'created_at' => '2025-02-25 11:45:34',
                'updated_at' => '2025-03-14 15:07:58',
            ),
            19 => 
            array (
                'id' => 20,
                'bidding_id' => 40,
                'service_type_id' => 4,
                'created_at' => '2025-02-25 13:23:34',
                'updated_at' => '2025-03-05 16:41:36',
            ),
            20 => 
            array (
                'id' => 21,
                'bidding_id' => 41,
                'service_type_id' => 4,
                'created_at' => '2025-02-25 14:27:34',
                'updated_at' => '2025-03-05 16:41:19',
            ),
            21 => 
            array (
                'id' => 22,
                'bidding_id' => 42,
                'service_type_id' => 4,
                'created_at' => '2025-02-25 14:29:38',
                'updated_at' => '2025-03-05 16:50:37',
            ),
            22 => 
            array (
                'id' => 23,
                'bidding_id' => 43,
                'service_type_id' => 3,
                'created_at' => '2025-02-25 14:42:50',
                'updated_at' => '2025-03-05 16:45:15',
            ),
            23 => 
            array (
                'id' => 24,
                'bidding_id' => 43,
                'service_type_id' => 9,
                'created_at' => '2025-02-25 14:42:50',
                'updated_at' => '2025-03-05 16:45:15',
            ),
            24 => 
            array (
                'id' => 25,
                'bidding_id' => 44,
                'service_type_id' => 1,
                'created_at' => '2025-02-25 15:03:10',
                'updated_at' => '2025-03-05 16:40:34',
            ),
            25 => 
            array (
                'id' => 26,
                'bidding_id' => 44,
                'service_type_id' => 9,
                'created_at' => '2025-02-25 15:03:10',
                'updated_at' => '2025-03-05 16:40:34',
            ),
            26 => 
            array (
                'id' => 27,
                'bidding_id' => 47,
                'service_type_id' => 4,
                'created_at' => '2025-02-25 15:43:29',
                'updated_at' => '2025-03-05 16:48:17',
            ),
            27 => 
            array (
                'id' => 28,
                'bidding_id' => 52,
                'service_type_id' => 2,
                'created_at' => '2025-02-25 16:17:39',
                'updated_at' => '2025-03-05 16:40:04',
            ),
            28 => 
            array (
                'id' => 29,
                'bidding_id' => 54,
                'service_type_id' => 9,
                'created_at' => '2025-03-03 10:51:50',
                'updated_at' => '2025-03-05 16:47:49',
            ),
            29 => 
            array (
                'id' => 30,
                'bidding_id' => 56,
                'service_type_id' => 1,
                'created_at' => '2025-03-03 14:47:46',
                'updated_at' => '2025-03-05 16:43:06',
            ),
            30 => 
            array (
                'id' => 31,
                'bidding_id' => 56,
                'service_type_id' => 6,
                'created_at' => '2025-03-03 14:47:46',
                'updated_at' => '2025-03-05 16:43:06',
            ),
            31 => 
            array (
                'id' => 32,
                'bidding_id' => 58,
                'service_type_id' => 9,
                'created_at' => '2025-03-03 14:56:18',
                'updated_at' => '2025-03-10 14:06:34',
            ),
            32 => 
            array (
                'id' => 33,
                'bidding_id' => 59,
                'service_type_id' => 4,
                'created_at' => '2025-03-03 15:03:48',
                'updated_at' => '2025-03-10 15:38:32',
            ),
            33 => 
            array (
                'id' => 34,
                'bidding_id' => 60,
                'service_type_id' => 1,
                'created_at' => '2025-03-03 15:06:13',
                'updated_at' => '2025-03-05 16:50:48',
            ),
            34 => 
            array (
                'id' => 35,
                'bidding_id' => 62,
                'service_type_id' => 1,
                'created_at' => '2025-03-03 15:15:29',
                'updated_at' => '2025-03-05 16:47:00',
            ),
            35 => 
            array (
                'id' => 36,
                'bidding_id' => 63,
                'service_type_id' => 2,
                'created_at' => '2025-03-03 15:19:14',
                'updated_at' => '2025-03-05 16:40:20',
            ),
            36 => 
            array (
                'id' => 37,
                'bidding_id' => 64,
                'service_type_id' => 9,
                'created_at' => '2025-03-03 15:23:04',
                'updated_at' => '2025-03-10 15:42:08',
            ),
            37 => 
            array (
                'id' => 38,
                'bidding_id' => 65,
                'service_type_id' => 1,
                'created_at' => '2025-03-03 15:27:21',
                'updated_at' => '2025-03-05 16:49:55',
            ),
            38 => 
            array (
                'id' => 39,
                'bidding_id' => 66,
                'service_type_id' => 2,
                'created_at' => '2025-03-03 15:29:56',
                'updated_at' => '2025-03-05 16:49:22',
            ),
            39 => 
            array (
                'id' => 40,
                'bidding_id' => 67,
                'service_type_id' => 2,
                'created_at' => '2025-03-03 15:32:37',
                'updated_at' => '2025-03-05 16:54:56',
            ),
            40 => 
            array (
                'id' => 41,
                'bidding_id' => 68,
                'service_type_id' => 4,
                'created_at' => '2025-03-03 15:34:13',
                'updated_at' => '2025-03-05 16:46:47',
            ),
            41 => 
            array (
                'id' => 42,
                'bidding_id' => 69,
                'service_type_id' => 2,
                'created_at' => '2025-03-03 15:36:38',
                'updated_at' => '2025-03-06 15:50:28',
            ),
            42 => 
            array (
                'id' => 43,
                'bidding_id' => 70,
                'service_type_id' => 2,
                'created_at' => '2025-03-03 15:38:09',
                'updated_at' => '2025-03-05 16:46:27',
            ),
            43 => 
            array (
                'id' => 44,
                'bidding_id' => 72,
                'service_type_id' => 2,
                'created_at' => '2025-03-07 06:45:02',
                'updated_at' => '2025-03-07 06:46:22',
            ),
            44 => 
            array (
                'id' => 45,
                'bidding_id' => 73,
                'service_type_id' => 4,
                'created_at' => '2025-03-10 14:11:21',
                'updated_at' => '2025-03-10 14:11:21',
            ),
            45 => 
            array (
                'id' => 46,
                'bidding_id' => 74,
                'service_type_id' => 2,
                'created_at' => '2025-03-10 14:14:20',
                'updated_at' => '2025-03-10 14:14:20',
            ),
            46 => 
            array (
                'id' => 47,
                'bidding_id' => 75,
                'service_type_id' => 3,
                'created_at' => '2025-03-10 14:18:43',
                'updated_at' => '2025-03-25 12:59:27',
            ),
            47 => 
            array (
                'id' => 48,
                'bidding_id' => 75,
                'service_type_id' => 11,
                'created_at' => '2025-03-10 14:18:43',
                'updated_at' => '2025-03-25 12:59:27',
            ),
            48 => 
            array (
                'id' => 49,
                'bidding_id' => 75,
                'service_type_id' => 7,
                'created_at' => '2025-03-10 14:18:43',
                'updated_at' => '2025-03-25 12:59:27',
            ),
            49 => 
            array (
                'id' => 50,
                'bidding_id' => 77,
                'service_type_id' => 1,
                'created_at' => '2025-03-10 14:28:12',
                'updated_at' => '2025-03-10 14:30:50',
            ),
            50 => 
            array (
                'id' => 51,
                'bidding_id' => 78,
                'service_type_id' => 16,
                'created_at' => '2025-03-10 14:39:14',
                'updated_at' => '2025-03-18 14:22:37',
            ),
            51 => 
            array (
                'id' => 52,
                'bidding_id' => 79,
                'service_type_id' => 4,
                'created_at' => '2025-03-10 14:50:31',
                'updated_at' => '2025-03-23 20:09:38',
            ),
            52 => 
            array (
                'id' => 53,
                'bidding_id' => 80,
                'service_type_id' => 1,
                'created_at' => '2025-03-10 15:36:52',
                'updated_at' => '2025-03-11 16:13:48',
            ),
            53 => 
            array (
                'id' => 54,
                'bidding_id' => 80,
                'service_type_id' => 13,
                'created_at' => '2025-03-10 15:36:52',
                'updated_at' => '2025-03-11 16:13:48',
            ),
            54 => 
            array (
                'id' => 55,
                'bidding_id' => 80,
                'service_type_id' => 6,
                'created_at' => '2025-03-10 15:36:52',
                'updated_at' => '2025-03-11 16:13:48',
            ),
            55 => 
            array (
                'id' => 56,
                'bidding_id' => 81,
                'service_type_id' => 5,
                'created_at' => '2025-03-10 15:40:43',
                'updated_at' => '2025-03-25 12:56:28',
            ),
            56 => 
            array (
                'id' => 57,
                'bidding_id' => 82,
                'service_type_id' => 15,
                'created_at' => '2025-03-11 16:12:21',
                'updated_at' => '2025-03-11 16:12:21',
            ),
            57 => 
            array (
                'id' => 58,
                'bidding_id' => 83,
                'service_type_id' => 4,
                'created_at' => '2025-03-18 13:34:33',
                'updated_at' => '2025-03-23 20:08:41',
            ),
            58 => 
            array (
                'id' => 59,
                'bidding_id' => 84,
                'service_type_id' => 5,
                'created_at' => '2025-03-18 13:37:52',
                'updated_at' => '2025-03-18 13:37:52',
            ),
            59 => 
            array (
                'id' => 60,
                'bidding_id' => 85,
                'service_type_id' => 13,
                'created_at' => '2025-03-18 13:40:53',
                'updated_at' => '2025-05-15 09:13:15',
            ),
            60 => 
            array (
                'id' => 61,
                'bidding_id' => 86,
                'service_type_id' => 4,
                'created_at' => '2025-03-18 13:44:12',
                'updated_at' => '2025-03-18 14:16:00',
            ),
            61 => 
            array (
                'id' => 62,
                'bidding_id' => 87,
                'service_type_id' => 4,
                'created_at' => '2025-03-18 13:46:55',
                'updated_at' => '2025-03-18 14:17:26',
            ),
            62 => 
            array (
                'id' => 63,
                'bidding_id' => 88,
                'service_type_id' => 1,
                'created_at' => '2025-03-18 15:35:49',
                'updated_at' => '2025-06-16 08:18:43',
            ),
            63 => 
            array (
                'id' => 64,
                'bidding_id' => 89,
                'service_type_id' => 4,
                'created_at' => '2025-03-18 15:39:06',
                'updated_at' => '2025-03-18 15:39:06',
            ),
            64 => 
            array (
                'id' => 65,
                'bidding_id' => 91,
                'service_type_id' => 4,
                'created_at' => '2025-03-18 15:46:31',
                'updated_at' => '2025-03-18 15:46:31',
            ),
            65 => 
            array (
                'id' => 66,
                'bidding_id' => 92,
                'service_type_id' => 1,
                'created_at' => '2025-03-18 15:49:22',
                'updated_at' => '2025-03-18 15:49:22',
            ),
            66 => 
            array (
                'id' => 67,
                'bidding_id' => 93,
                'service_type_id' => 5,
                'created_at' => '2025-05-09 09:52:02',
                'updated_at' => '2025-05-11 08:36:49',
            ),
            67 => 
            array (
                'id' => 68,
                'bidding_id' => 94,
                'service_type_id' => 5,
                'created_at' => '2025-05-09 09:55:56',
                'updated_at' => '2025-05-24 14:08:02',
            ),
            68 => 
            array (
                'id' => 69,
                'bidding_id' => 95,
                'service_type_id' => 5,
                'created_at' => '2025-05-09 09:57:37',
                'updated_at' => '2025-05-11 08:38:02',
            ),
            69 => 
            array (
                'id' => 70,
                'bidding_id' => 96,
                'service_type_id' => 5,
                'created_at' => '2025-05-09 10:01:41',
                'updated_at' => '2025-05-24 14:01:43',
            ),
            70 => 
            array (
                'id' => 71,
                'bidding_id' => 96,
                'service_type_id' => 14,
                'created_at' => '2025-05-09 10:01:41',
                'updated_at' => '2025-05-24 14:01:43',
            ),
            71 => 
            array (
                'id' => 72,
                'bidding_id' => 97,
                'service_type_id' => 5,
                'created_at' => '2025-05-09 10:03:31',
                'updated_at' => '2025-05-13 16:38:03',
            ),
            72 => 
            array (
                'id' => 73,
                'bidding_id' => 98,
                'service_type_id' => 5,
                'created_at' => '2025-05-09 10:07:38',
                'updated_at' => '2025-05-12 08:33:59',
            ),
            73 => 
            array (
                'id' => 74,
                'bidding_id' => 99,
                'service_type_id' => 5,
                'created_at' => '2025-05-09 10:09:18',
                'updated_at' => '2025-05-12 19:05:25',
            ),
            74 => 
            array (
                'id' => 75,
                'bidding_id' => 100,
                'service_type_id' => 5,
                'created_at' => '2025-05-09 10:11:48',
                'updated_at' => '2025-06-03 13:34:08',
            ),
            75 => 
            array (
                'id' => 76,
                'bidding_id' => 100,
                'service_type_id' => 15,
                'created_at' => '2025-05-09 10:11:48',
                'updated_at' => '2025-06-03 13:34:08',
            ),
            76 => 
            array (
                'id' => 77,
                'bidding_id' => 101,
                'service_type_id' => 1,
                'created_at' => '2025-05-09 10:39:04',
                'updated_at' => '2025-05-11 07:58:26',
            ),
            77 => 
            array (
                'id' => 78,
                'bidding_id' => 101,
                'service_type_id' => 13,
                'created_at' => '2025-05-09 10:39:04',
                'updated_at' => '2025-05-11 07:58:26',
            ),
            78 => 
            array (
                'id' => 79,
                'bidding_id' => 101,
                'service_type_id' => 6,
                'created_at' => '2025-05-09 10:39:04',
                'updated_at' => '2025-05-11 07:58:26',
            ),
            79 => 
            array (
                'id' => 80,
                'bidding_id' => 102,
                'service_type_id' => 1,
                'created_at' => '2025-05-09 10:49:35',
                'updated_at' => '2025-06-16 07:41:34',
            ),
            80 => 
            array (
                'id' => 81,
                'bidding_id' => 102,
                'service_type_id' => 9,
                'created_at' => '2025-05-09 10:49:35',
                'updated_at' => '2025-06-16 07:41:34',
            ),
            81 => 
            array (
                'id' => 82,
                'bidding_id' => 102,
                'service_type_id' => 15,
                'created_at' => '2025-05-09 10:49:35',
                'updated_at' => '2025-06-16 07:41:34',
            ),
            82 => 
            array (
                'id' => 83,
                'bidding_id' => 103,
                'service_type_id' => 1,
                'created_at' => '2025-05-09 11:12:50',
                'updated_at' => '2025-06-06 10:29:51',
            ),
            83 => 
            array (
                'id' => 84,
                'bidding_id' => 104,
                'service_type_id' => 1,
                'created_at' => '2025-05-09 11:23:33',
                'updated_at' => '2025-06-03 13:23:15',
            ),
            84 => 
            array (
                'id' => 85,
                'bidding_id' => 105,
                'service_type_id' => 1,
                'created_at' => '2025-05-09 11:31:11',
                'updated_at' => '2025-06-06 10:36:46',
            ),
            85 => 
            array (
                'id' => 86,
                'bidding_id' => 105,
                'service_type_id' => 6,
                'created_at' => '2025-05-09 11:31:11',
                'updated_at' => '2025-06-06 10:36:46',
            ),
            86 => 
            array (
                'id' => 87,
                'bidding_id' => 106,
                'service_type_id' => 15,
                'created_at' => '2025-05-09 12:49:23',
                'updated_at' => '2025-05-11 07:58:34',
            ),
            87 => 
            array (
                'id' => 88,
                'bidding_id' => 107,
                'service_type_id' => 4,
                'created_at' => '2025-05-09 13:04:12',
                'updated_at' => '2025-05-13 16:34:11',
            ),
            88 => 
            array (
                'id' => 89,
                'bidding_id' => 108,
                'service_type_id' => 4,
                'created_at' => '2025-05-09 13:10:07',
                'updated_at' => '2025-05-13 16:37:14',
            ),
            89 => 
            array (
                'id' => 90,
                'bidding_id' => 109,
                'service_type_id' => 4,
                'created_at' => '2025-05-09 13:17:00',
                'updated_at' => '2025-06-06 10:37:15',
            ),
            90 => 
            array (
                'id' => 91,
                'bidding_id' => 110,
                'service_type_id' => 4,
                'created_at' => '2025-05-09 13:22:24',
                'updated_at' => '2025-05-13 16:38:44',
            ),
            91 => 
            array (
                'id' => 92,
                'bidding_id' => 111,
                'service_type_id' => 4,
                'created_at' => '2025-05-09 13:27:56',
                'updated_at' => '2025-06-06 12:01:24',
            ),
            92 => 
            array (
                'id' => 93,
                'bidding_id' => 112,
                'service_type_id' => 4,
                'created_at' => '2025-05-09 13:35:52',
                'updated_at' => '2025-05-13 16:36:08',
            ),
            93 => 
            array (
                'id' => 94,
                'bidding_id' => 113,
                'service_type_id' => 4,
                'created_at' => '2025-05-09 13:41:01',
                'updated_at' => '2025-05-09 13:41:01',
            ),
            94 => 
            array (
                'id' => 95,
                'bidding_id' => 114,
                'service_type_id' => 4,
                'created_at' => '2025-05-09 13:46:18',
                'updated_at' => '2025-05-13 16:31:24',
            ),
            95 => 
            array (
                'id' => 96,
                'bidding_id' => 115,
                'service_type_id' => 4,
                'created_at' => '2025-05-09 13:54:06',
                'updated_at' => '2025-05-23 09:15:39',
            ),
            96 => 
            array (
                'id' => 97,
                'bidding_id' => 115,
                'service_type_id' => 10,
                'created_at' => '2025-05-09 13:54:06',
                'updated_at' => '2025-05-23 09:15:39',
            ),
            97 => 
            array (
                'id' => 98,
                'bidding_id' => 116,
                'service_type_id' => 4,
                'created_at' => '2025-05-09 13:57:39',
                'updated_at' => '2025-05-13 16:33:18',
            ),
            98 => 
            array (
                'id' => 99,
                'bidding_id' => 117,
                'service_type_id' => 4,
                'created_at' => '2025-05-09 14:01:55',
                'updated_at' => '2025-05-23 10:23:06',
            ),
            99 => 
            array (
                'id' => 100,
                'bidding_id' => 118,
                'service_type_id' => 4,
                'created_at' => '2025-05-09 14:06:22',
                'updated_at' => '2025-05-13 16:33:50',
            ),
            100 => 
            array (
                'id' => 101,
                'bidding_id' => 119,
                'service_type_id' => 4,
                'created_at' => '2025-05-09 14:09:34',
                'updated_at' => '2025-05-11 07:58:12',
            ),
            101 => 
            array (
                'id' => 102,
                'bidding_id' => 120,
                'service_type_id' => 4,
                'created_at' => '2025-05-09 15:07:01',
                'updated_at' => '2025-05-13 16:41:48',
            ),
            102 => 
            array (
                'id' => 103,
                'bidding_id' => 121,
                'service_type_id' => 4,
                'created_at' => '2025-05-09 15:11:43',
                'updated_at' => '2025-05-23 08:01:17',
            ),
            103 => 
            array (
                'id' => 104,
                'bidding_id' => 122,
                'service_type_id' => 4,
                'created_at' => '2025-05-09 15:34:58',
                'updated_at' => '2025-06-06 10:37:42',
            ),
            104 => 
            array (
                'id' => 105,
                'bidding_id' => 123,
                'service_type_id' => 18,
                'created_at' => '2025-05-09 15:41:08',
                'updated_at' => '2025-05-13 16:35:56',
            ),
            105 => 
            array (
                'id' => 106,
                'bidding_id' => 124,
                'service_type_id' => 18,
                'created_at' => '2025-05-09 15:45:04',
                'updated_at' => '2025-06-03 10:55:34',
            ),
            106 => 
            array (
                'id' => 107,
                'bidding_id' => 125,
                'service_type_id' => 18,
                'created_at' => '2025-05-09 15:50:03',
                'updated_at' => '2025-05-13 16:35:20',
            ),
            107 => 
            array (
                'id' => 108,
                'bidding_id' => 126,
                'service_type_id' => 15,
                'created_at' => '2025-05-09 15:56:20',
                'updated_at' => '2025-05-13 16:35:33',
            ),
            108 => 
            array (
                'id' => 109,
                'bidding_id' => 127,
                'service_type_id' => 1,
                'created_at' => '2025-05-13 08:59:10',
                'updated_at' => '2025-05-24 13:59:26',
            ),
            109 => 
            array (
                'id' => 110,
                'bidding_id' => 129,
                'service_type_id' => 4,
                'created_at' => '2025-05-13 09:30:50',
                'updated_at' => '2025-05-13 16:43:16',
            ),
            110 => 
            array (
                'id' => 111,
                'bidding_id' => 130,
                'service_type_id' => 4,
                'created_at' => '2025-05-13 09:33:40',
                'updated_at' => '2025-05-13 09:33:40',
            ),
            111 => 
            array (
                'id' => 112,
                'bidding_id' => 131,
                'service_type_id' => 4,
                'created_at' => '2025-05-13 09:40:53',
                'updated_at' => '2025-05-13 16:37:48',
            ),
            112 => 
            array (
                'id' => 113,
                'bidding_id' => 132,
                'service_type_id' => 4,
                'created_at' => '2025-05-13 09:45:10',
                'updated_at' => '2025-05-13 16:31:11',
            ),
            113 => 
            array (
                'id' => 114,
                'bidding_id' => 133,
                'service_type_id' => 5,
                'created_at' => '2025-05-13 09:48:48',
                'updated_at' => '2025-05-13 09:48:48',
            ),
            114 => 
            array (
                'id' => 115,
                'bidding_id' => 134,
                'service_type_id' => 6,
                'created_at' => '2025-05-13 09:53:17',
                'updated_at' => '2025-05-13 09:53:17',
            ),
            115 => 
            array (
                'id' => 116,
                'bidding_id' => 135,
                'service_type_id' => 1,
                'created_at' => '2025-05-13 09:58:28',
                'updated_at' => '2025-06-16 07:11:47',
            ),
            116 => 
            array (
                'id' => 117,
                'bidding_id' => 135,
                'service_type_id' => 2,
                'created_at' => '2025-05-13 09:58:28',
                'updated_at' => '2025-06-16 07:11:47',
            ),
            117 => 
            array (
                'id' => 118,
                'bidding_id' => 135,
                'service_type_id' => 9,
                'created_at' => '2025-05-13 09:58:28',
                'updated_at' => '2025-06-16 07:11:47',
            ),
            118 => 
            array (
                'id' => 119,
                'bidding_id' => 136,
                'service_type_id' => 5,
                'created_at' => '2025-05-13 12:41:49',
                'updated_at' => '2025-05-13 16:33:37',
            ),
            119 => 
            array (
                'id' => 120,
                'bidding_id' => 137,
                'service_type_id' => 1,
                'created_at' => '2025-05-14 06:43:57',
                'updated_at' => '2025-05-14 06:43:57',
            ),
            120 => 
            array (
                'id' => 121,
                'bidding_id' => 138,
                'service_type_id' => 5,
                'created_at' => '2025-05-15 08:41:31',
                'updated_at' => '2025-05-24 14:05:50',
            ),
            121 => 
            array (
                'id' => 122,
                'bidding_id' => 138,
                'service_type_id' => 15,
                'created_at' => '2025-05-15 08:41:31',
                'updated_at' => '2025-05-24 14:05:50',
            ),
            122 => 
            array (
                'id' => 123,
                'bidding_id' => 139,
                'service_type_id' => 5,
                'created_at' => '2025-05-15 08:45:57',
                'updated_at' => '2025-05-15 09:12:26',
            ),
            123 => 
            array (
                'id' => 124,
                'bidding_id' => 140,
                'service_type_id' => 5,
                'created_at' => '2025-05-15 08:50:56',
                'updated_at' => '2025-05-15 09:15:59',
            ),
            124 => 
            array (
                'id' => 125,
                'bidding_id' => 141,
                'service_type_id' => 4,
                'created_at' => '2025-05-15 09:10:08',
                'updated_at' => '2025-06-03 17:05:33',
            ),
            125 => 
            array (
                'id' => 126,
                'bidding_id' => 142,
                'service_type_id' => 6,
                'created_at' => '2025-05-20 14:39:06',
                'updated_at' => '2025-05-22 14:35:31',
            ),
            126 => 
            array (
                'id' => 127,
                'bidding_id' => 143,
                'service_type_id' => 1,
                'created_at' => '2025-05-20 14:44:30',
                'updated_at' => '2025-05-22 14:35:42',
            ),
            127 => 
            array (
                'id' => 128,
                'bidding_id' => 143,
                'service_type_id' => 13,
                'created_at' => '2025-05-20 14:44:30',
                'updated_at' => '2025-05-22 14:35:42',
            ),
            128 => 
            array (
                'id' => 129,
                'bidding_id' => 144,
                'service_type_id' => 1,
                'created_at' => '2025-05-22 15:42:21',
                'updated_at' => '2025-06-17 11:10:24',
            ),
            129 => 
            array (
                'id' => 130,
                'bidding_id' => 144,
                'service_type_id' => 13,
                'created_at' => '2025-05-22 15:42:21',
                'updated_at' => '2025-06-17 11:10:24',
            ),
            130 => 
            array (
                'id' => 131,
                'bidding_id' => 144,
                'service_type_id' => 9,
                'created_at' => '2025-05-22 15:42:21',
                'updated_at' => '2025-06-17 11:10:24',
            ),
            131 => 
            array (
                'id' => 132,
                'bidding_id' => 145,
                'service_type_id' => 1,
                'created_at' => '2025-05-22 16:03:32',
                'updated_at' => '2025-06-06 10:58:08',
            ),
            132 => 
            array (
                'id' => 133,
                'bidding_id' => 146,
                'service_type_id' => 4,
                'created_at' => '2025-05-23 07:58:47',
                'updated_at' => '2025-05-23 08:00:50',
            ),
            133 => 
            array (
                'id' => 134,
                'bidding_id' => 147,
                'service_type_id' => 1,
                'created_at' => '2025-05-23 08:28:55',
                'updated_at' => '2025-05-24 13:59:54',
            ),
            134 => 
            array (
                'id' => 135,
                'bidding_id' => 148,
                'service_type_id' => 5,
                'created_at' => '2025-05-23 09:19:06',
                'updated_at' => '2025-06-06 11:16:26',
            ),
            135 => 
            array (
                'id' => 136,
                'bidding_id' => 149,
                'service_type_id' => 1,
                'created_at' => '2025-05-23 09:25:20',
                'updated_at' => '2025-06-03 13:24:48',
            ),
            136 => 
            array (
                'id' => 137,
                'bidding_id' => 150,
                'service_type_id' => 5,
                'created_at' => '2025-05-23 09:30:29',
                'updated_at' => '2025-05-23 09:30:29',
            ),
            137 => 
            array (
                'id' => 138,
                'bidding_id' => 152,
                'service_type_id' => 15,
                'created_at' => '2025-05-23 09:42:49',
                'updated_at' => '2025-05-23 09:42:49',
            ),
            138 => 
            array (
                'id' => 139,
                'bidding_id' => 153,
                'service_type_id' => 18,
                'created_at' => '2025-05-23 10:07:42',
                'updated_at' => '2025-05-23 10:07:42',
            ),
            139 => 
            array (
                'id' => 140,
                'bidding_id' => 154,
                'service_type_id' => 1,
                'created_at' => '2025-05-23 10:18:56',
                'updated_at' => '2025-06-16 09:19:59',
            ),
            140 => 
            array (
                'id' => 141,
                'bidding_id' => 155,
                'service_type_id' => 1,
                'created_at' => '2025-05-23 14:27:44',
                'updated_at' => '2025-05-23 14:27:44',
            ),
            141 => 
            array (
                'id' => 142,
                'bidding_id' => 155,
                'service_type_id' => 9,
                'created_at' => '2025-05-23 14:27:44',
                'updated_at' => '2025-05-23 14:27:44',
            ),
            142 => 
            array (
                'id' => 143,
                'bidding_id' => 156,
                'service_type_id' => 1,
                'created_at' => '2025-05-23 14:32:00',
                'updated_at' => '2025-06-06 10:59:03',
            ),
            143 => 
            array (
                'id' => 144,
                'bidding_id' => 156,
                'service_type_id' => 9,
                'created_at' => '2025-05-23 14:32:00',
                'updated_at' => '2025-06-06 10:59:03',
            ),
            144 => 
            array (
                'id' => 145,
                'bidding_id' => 157,
                'service_type_id' => 18,
                'created_at' => '2025-05-23 14:37:53',
                'updated_at' => '2025-06-06 12:02:00',
            ),
            145 => 
            array (
                'id' => 146,
                'bidding_id' => 158,
                'service_type_id' => 6,
                'created_at' => '2025-05-27 05:54:33',
                'updated_at' => '2025-06-16 09:11:48',
            ),
            146 => 
            array (
                'id' => 147,
                'bidding_id' => 159,
                'service_type_id' => 10,
                'created_at' => '2025-05-27 06:04:15',
                'updated_at' => '2025-05-27 06:04:15',
            ),
            147 => 
            array (
                'id' => 148,
                'bidding_id' => 160,
                'service_type_id' => 5,
                'created_at' => '2025-05-27 06:12:07',
                'updated_at' => '2025-06-06 10:36:18',
            ),
            148 => 
            array (
                'id' => 149,
                'bidding_id' => 161,
                'service_type_id' => 4,
                'created_at' => '2025-05-27 06:24:30',
                'updated_at' => '2025-06-16 09:03:53',
            ),
            149 => 
            array (
                'id' => 150,
                'bidding_id' => 162,
                'service_type_id' => 5,
                'created_at' => '2025-05-27 06:30:37',
                'updated_at' => '2025-06-16 09:15:00',
            ),
            150 => 
            array (
                'id' => 151,
                'bidding_id' => 163,
                'service_type_id' => 4,
                'created_at' => '2025-06-03 11:04:08',
                'updated_at' => '2025-06-19 08:30:59',
            ),
            151 => 
            array (
                'id' => 152,
                'bidding_id' => 164,
                'service_type_id' => 4,
                'created_at' => '2025-06-03 13:19:10',
                'updated_at' => '2025-06-03 13:19:26',
            ),
            152 => 
            array (
                'id' => 153,
                'bidding_id' => 165,
                'service_type_id' => 4,
                'created_at' => '2025-06-03 13:22:16',
                'updated_at' => '2025-06-03 13:22:16',
            ),
            153 => 
            array (
                'id' => 154,
                'bidding_id' => 166,
                'service_type_id' => 4,
                'created_at' => '2025-06-03 13:26:35',
                'updated_at' => '2025-06-03 13:26:35',
            ),
            154 => 
            array (
                'id' => 155,
                'bidding_id' => 167,
                'service_type_id' => 18,
                'created_at' => '2025-06-03 13:28:43',
                'updated_at' => '2025-06-06 10:47:59',
            ),
            155 => 
            array (
                'id' => 156,
                'bidding_id' => 168,
                'service_type_id' => 4,
                'created_at' => '2025-06-03 13:36:57',
                'updated_at' => '2025-06-03 13:36:57',
            ),
            156 => 
            array (
                'id' => 157,
                'bidding_id' => 169,
                'service_type_id' => 15,
                'created_at' => '2025-06-03 13:39:12',
                'updated_at' => '2025-06-03 13:39:12',
            ),
            157 => 
            array (
                'id' => 158,
                'bidding_id' => 170,
                'service_type_id' => 1,
                'created_at' => '2025-06-03 13:45:46',
                'updated_at' => '2025-06-23 07:21:24',
            ),
            158 => 
            array (
                'id' => 159,
                'bidding_id' => 170,
                'service_type_id' => 6,
                'created_at' => '2025-06-03 13:45:46',
                'updated_at' => '2025-06-23 07:21:24',
            ),
            159 => 
            array (
                'id' => 160,
                'bidding_id' => 170,
                'service_type_id' => 15,
                'created_at' => '2025-06-03 13:45:46',
                'updated_at' => '2025-06-23 07:21:24',
            ),
            160 => 
            array (
                'id' => 161,
                'bidding_id' => 171,
                'service_type_id' => 5,
                'created_at' => '2025-06-03 13:48:58',
                'updated_at' => '2025-06-03 13:48:58',
            ),
            161 => 
            array (
                'id' => 162,
                'bidding_id' => 172,
                'service_type_id' => 5,
                'created_at' => '2025-06-03 13:53:47',
                'updated_at' => '2025-06-06 11:58:07',
            ),
            162 => 
            array (
                'id' => 163,
                'bidding_id' => 173,
                'service_type_id' => 5,
                'created_at' => '2025-06-03 13:59:14',
                'updated_at' => '2025-06-03 13:59:14',
            ),
            163 => 
            array (
                'id' => 164,
                'bidding_id' => 174,
                'service_type_id' => 5,
                'created_at' => '2025-06-03 14:14:04',
                'updated_at' => '2025-06-03 14:14:04',
            ),
            164 => 
            array (
                'id' => 165,
                'bidding_id' => 175,
                'service_type_id' => 4,
                'created_at' => '2025-06-03 14:17:39',
                'updated_at' => '2025-06-06 10:37:29',
            ),
            165 => 
            array (
                'id' => 166,
                'bidding_id' => 176,
                'service_type_id' => 4,
                'created_at' => '2025-06-04 07:35:25',
                'updated_at' => '2025-06-06 10:38:12',
            ),
            166 => 
            array (
                'id' => 167,
                'bidding_id' => 177,
                'service_type_id' => 1,
                'created_at' => '2025-06-16 06:57:53',
                'updated_at' => '2025-06-16 06:57:53',
            ),
            167 => 
            array (
                'id' => 168,
                'bidding_id' => 177,
                'service_type_id' => 15,
                'created_at' => '2025-06-16 06:57:53',
                'updated_at' => '2025-06-16 06:57:53',
            ),
            168 => 
            array (
                'id' => 169,
                'bidding_id' => 178,
                'service_type_id' => 10,
                'created_at' => '2025-06-16 07:21:09',
                'updated_at' => '2025-06-16 07:21:09',
            ),
            169 => 
            array (
                'id' => 170,
                'bidding_id' => 178,
                'service_type_id' => 15,
                'created_at' => '2025-06-16 07:21:09',
                'updated_at' => '2025-06-16 07:21:09',
            ),
            170 => 
            array (
                'id' => 171,
                'bidding_id' => 179,
                'service_type_id' => 1,
                'created_at' => '2025-06-16 07:38:29',
                'updated_at' => '2025-06-16 07:38:29',
            ),
            171 => 
            array (
                'id' => 172,
                'bidding_id' => 180,
                'service_type_id' => 6,
                'created_at' => '2025-06-16 07:52:57',
                'updated_at' => '2025-06-16 07:52:57',
            ),
            172 => 
            array (
                'id' => 173,
                'bidding_id' => 181,
                'service_type_id' => 5,
                'created_at' => '2025-06-16 08:03:26',
                'updated_at' => '2025-06-16 08:03:26',
            ),
            173 => 
            array (
                'id' => 174,
                'bidding_id' => 182,
                'service_type_id' => 5,
                'created_at' => '2025-06-16 08:15:10',
                'updated_at' => '2025-06-16 08:15:10',
            ),
            174 => 
            array (
                'id' => 175,
                'bidding_id' => 183,
                'service_type_id' => 10,
                'created_at' => '2025-06-16 09:01:08',
                'updated_at' => '2025-06-16 09:01:08',
            ),
            175 => 
            array (
                'id' => 176,
                'bidding_id' => 184,
                'service_type_id' => 5,
                'created_at' => '2025-06-16 09:08:02',
                'updated_at' => '2025-06-16 09:08:15',
            ),
            176 => 
            array (
                'id' => 177,
                'bidding_id' => 184,
                'service_type_id' => 14,
                'created_at' => '2025-06-16 09:08:02',
                'updated_at' => '2025-06-16 09:08:15',
            ),
            177 => 
            array (
                'id' => 178,
                'bidding_id' => 185,
                'service_type_id' => 10,
                'created_at' => '2025-06-16 09:17:50',
                'updated_at' => '2025-06-16 09:17:50',
            ),
            178 => 
            array (
                'id' => 179,
                'bidding_id' => 186,
                'service_type_id' => 10,
                'created_at' => '2025-06-16 09:29:11',
                'updated_at' => '2025-06-16 09:29:11',
            ),
            179 => 
            array (
                'id' => 180,
                'bidding_id' => 187,
                'service_type_id' => 10,
                'created_at' => '2025-06-16 09:35:58',
                'updated_at' => '2025-06-16 09:35:58',
            ),
            180 => 
            array (
                'id' => 181,
                'bidding_id' => 188,
                'service_type_id' => 10,
                'created_at' => '2025-06-16 09:42:04',
                'updated_at' => '2025-06-16 09:42:04',
            ),
            181 => 
            array (
                'id' => 182,
                'bidding_id' => 189,
                'service_type_id' => 2,
                'created_at' => '2025-06-17 08:19:41',
                'updated_at' => '2025-06-17 08:19:41',
            ),
            182 => 
            array (
                'id' => 183,
                'bidding_id' => 189,
                'service_type_id' => 5,
                'created_at' => '2025-06-17 08:19:41',
                'updated_at' => '2025-06-17 08:19:41',
            ),
            183 => 
            array (
                'id' => 184,
                'bidding_id' => 190,
                'service_type_id' => 10,
                'created_at' => '2025-06-19 09:05:23',
                'updated_at' => '2025-06-19 09:05:23',
            ),
            184 => 
            array (
                'id' => 185,
                'bidding_id' => 191,
                'service_type_id' => 5,
                'created_at' => '2025-06-19 09:28:29',
                'updated_at' => '2025-06-19 09:28:29',
            ),
            185 => 
            array (
                'id' => 186,
                'bidding_id' => 192,
                'service_type_id' => 10,
                'created_at' => '2025-06-20 07:02:18',
                'updated_at' => '2025-06-20 07:02:18',
            ),
            186 => 
            array (
                'id' => 187,
                'bidding_id' => 193,
                'service_type_id' => 10,
                'created_at' => '2025-06-23 07:27:09',
                'updated_at' => '2025-06-24 09:21:24',
            ),
            187 => 
            array (
                'id' => 188,
                'bidding_id' => 194,
                'service_type_id' => 5,
                'created_at' => '2025-06-24 09:36:33',
                'updated_at' => '2025-06-24 09:36:33',
            ),
            188 => 
            array (
                'id' => 189,
                'bidding_id' => 195,
                'service_type_id' => 10,
                'created_at' => '2025-06-24 09:40:03',
                'updated_at' => '2025-06-24 09:40:03',
            ),
            189 => 
            array (
                'id' => 190,
                'bidding_id' => 196,
                'service_type_id' => 10,
                'created_at' => '2025-06-26 15:10:17',
                'updated_at' => '2025-06-26 15:10:17',
            ),
            190 => 
            array (
                'id' => 191,
                'bidding_id' => 197,
                'service_type_id' => 10,
                'created_at' => '2025-06-26 15:14:55',
                'updated_at' => '2025-06-26 15:14:55',
            ),
            191 => 
            array (
                'id' => 192,
                'bidding_id' => 198,
                'service_type_id' => 1,
                'created_at' => '2025-06-26 15:19:13',
                'updated_at' => '2025-06-26 15:19:13',
            ),
            192 => 
            array (
                'id' => 193,
                'bidding_id' => 198,
                'service_type_id' => 13,
                'created_at' => '2025-06-26 15:19:13',
                'updated_at' => '2025-06-26 15:19:13',
            ),
            193 => 
            array (
                'id' => 194,
                'bidding_id' => 198,
                'service_type_id' => 9,
                'created_at' => '2025-06-26 15:19:13',
                'updated_at' => '2025-06-26 15:19:13',
            ),
            194 => 
            array (
                'id' => 195,
                'bidding_id' => 199,
                'service_type_id' => 10,
                'created_at' => '2025-06-27 10:09:05',
                'updated_at' => '2025-06-27 10:09:05',
            ),
            195 => 
            array (
                'id' => 196,
                'bidding_id' => 200,
                'service_type_id' => 10,
                'created_at' => '2025-07-02 06:52:54',
                'updated_at' => '2025-07-02 06:52:54',
            ),
            196 => 
            array (
                'id' => 197,
                'bidding_id' => 201,
                'service_type_id' => 10,
                'created_at' => '2025-07-02 06:57:35',
                'updated_at' => '2025-07-02 06:57:35',
            ),
            197 => 
            array (
                'id' => 198,
                'bidding_id' => 202,
                'service_type_id' => 5,
                'created_at' => '2025-07-02 07:06:48',
                'updated_at' => '2025-07-02 07:06:48',
            ),
            198 => 
            array (
                'id' => 199,
                'bidding_id' => 203,
                'service_type_id' => 1,
                'created_at' => '2025-07-02 07:22:30',
                'updated_at' => '2025-07-02 07:22:30',
            ),
            199 => 
            array (
                'id' => 200,
                'bidding_id' => 204,
                'service_type_id' => 10,
                'created_at' => '2025-07-03 13:11:34',
                'updated_at' => '2025-07-03 13:11:34',
            ),
            200 => 
            array (
                'id' => 201,
                'bidding_id' => 205,
                'service_type_id' => 3,
                'created_at' => '2025-07-03 13:32:22',
                'updated_at' => '2025-07-03 13:32:22',
            ),
            201 => 
            array (
                'id' => 202,
                'bidding_id' => 205,
                'service_type_id' => 2,
                'created_at' => '2025-07-03 13:32:22',
                'updated_at' => '2025-07-03 13:32:22',
            ),
            202 => 
            array (
                'id' => 203,
                'bidding_id' => 205,
                'service_type_id' => 9,
                'created_at' => '2025-07-03 13:32:22',
                'updated_at' => '2025-07-03 13:32:22',
            ),
            203 => 
            array (
                'id' => 204,
                'bidding_id' => 206,
                'service_type_id' => 10,
                'created_at' => '2025-07-03 13:36:51',
                'updated_at' => '2025-07-03 13:36:51',
            ),
            204 => 
            array (
                'id' => 205,
                'bidding_id' => 207,
                'service_type_id' => 1,
                'created_at' => '2025-07-03 13:40:25',
                'updated_at' => '2025-07-03 13:40:25',
            ),
            205 => 
            array (
                'id' => 206,
                'bidding_id' => 208,
                'service_type_id' => 1,
                'created_at' => '2025-07-03 13:43:34',
                'updated_at' => '2025-07-03 13:43:34',
            ),
            206 => 
            array (
                'id' => 207,
                'bidding_id' => 209,
                'service_type_id' => 5,
                'created_at' => '2025-07-04 06:59:11',
                'updated_at' => '2025-07-04 06:59:11',
            ),
            207 => 
            array (
                'id' => 208,
                'bidding_id' => 210,
                'service_type_id' => 5,
                'created_at' => '2025-07-04 07:05:29',
                'updated_at' => '2025-08-06 09:36:35',
            ),
            208 => 
            array (
                'id' => 209,
                'bidding_id' => 211,
                'service_type_id' => 1,
                'created_at' => '2025-07-08 06:53:10',
                'updated_at' => '2025-07-08 06:53:10',
            ),
            209 => 
            array (
                'id' => 210,
                'bidding_id' => 211,
                'service_type_id' => 12,
                'created_at' => '2025-07-08 06:53:10',
                'updated_at' => '2025-07-08 06:53:10',
            ),
            210 => 
            array (
                'id' => 211,
                'bidding_id' => 211,
                'service_type_id' => 9,
                'created_at' => '2025-07-08 06:53:10',
                'updated_at' => '2025-07-08 06:53:10',
            ),
            211 => 
            array (
                'id' => 212,
                'bidding_id' => 212,
                'service_type_id' => 1,
                'created_at' => '2025-07-10 07:00:21',
                'updated_at' => '2025-07-10 07:00:21',
            ),
            212 => 
            array (
                'id' => 213,
                'bidding_id' => 213,
                'service_type_id' => 4,
                'created_at' => '2025-07-10 07:04:04',
                'updated_at' => '2025-07-10 07:04:04',
            ),
            213 => 
            array (
                'id' => 214,
                'bidding_id' => 214,
                'service_type_id' => 12,
                'created_at' => '2025-07-11 12:38:37',
                'updated_at' => '2025-07-11 12:38:37',
            ),
            214 => 
            array (
                'id' => 215,
                'bidding_id' => 215,
                'service_type_id' => 4,
                'created_at' => '2025-07-11 12:42:31',
                'updated_at' => '2025-07-11 12:42:31',
            ),
            215 => 
            array (
                'id' => 216,
                'bidding_id' => 216,
                'service_type_id' => 4,
                'created_at' => '2025-07-14 06:45:05',
                'updated_at' => '2025-07-14 06:45:05',
            ),
            216 => 
            array (
                'id' => 217,
                'bidding_id' => 216,
                'service_type_id' => 10,
                'created_at' => '2025-07-14 06:45:05',
                'updated_at' => '2025-07-14 06:45:05',
            ),
            217 => 
            array (
                'id' => 218,
                'bidding_id' => 217,
                'service_type_id' => 5,
                'created_at' => '2025-07-14 06:47:53',
                'updated_at' => '2025-07-14 06:47:53',
            ),
            218 => 
            array (
                'id' => 219,
                'bidding_id' => 218,
                'service_type_id' => 4,
                'created_at' => '2025-07-15 06:55:02',
                'updated_at' => '2025-07-15 06:55:02',
            ),
            219 => 
            array (
                'id' => 220,
                'bidding_id' => 219,
                'service_type_id' => 6,
                'created_at' => '2025-07-15 06:59:44',
                'updated_at' => '2025-07-15 06:59:44',
            ),
            220 => 
            array (
                'id' => 221,
                'bidding_id' => 220,
                'service_type_id' => 4,
                'created_at' => '2025-07-16 07:29:11',
                'updated_at' => '2025-07-16 07:29:11',
            ),
            221 => 
            array (
                'id' => 222,
                'bidding_id' => 221,
                'service_type_id' => 4,
                'created_at' => '2025-07-16 07:32:10',
                'updated_at' => '2025-07-28 06:55:46',
            ),
            222 => 
            array (
                'id' => 223,
                'bidding_id' => 222,
                'service_type_id' => 5,
                'created_at' => '2025-07-17 07:21:00',
                'updated_at' => '2025-07-17 07:21:00',
            ),
            223 => 
            array (
                'id' => 224,
                'bidding_id' => 223,
                'service_type_id' => 4,
                'created_at' => '2025-07-18 07:01:36',
                'updated_at' => '2025-07-18 07:01:36',
            ),
            224 => 
            array (
                'id' => 225,
                'bidding_id' => 224,
                'service_type_id' => 10,
                'created_at' => '2025-07-22 07:31:04',
                'updated_at' => '2025-07-22 07:31:04',
            ),
            225 => 
            array (
                'id' => 226,
                'bidding_id' => 225,
                'service_type_id' => 4,
                'created_at' => '2025-07-22 07:38:24',
                'updated_at' => '2025-07-22 07:38:24',
            ),
            226 => 
            array (
                'id' => 227,
                'bidding_id' => 225,
                'service_type_id' => 10,
                'created_at' => '2025-07-22 07:38:24',
                'updated_at' => '2025-07-22 07:38:24',
            ),
            227 => 
            array (
                'id' => 228,
                'bidding_id' => 226,
                'service_type_id' => 5,
                'created_at' => '2025-07-22 12:40:40',
                'updated_at' => '2025-07-22 12:40:40',
            ),
            228 => 
            array (
                'id' => 229,
                'bidding_id' => 227,
                'service_type_id' => 4,
                'created_at' => '2025-07-22 12:45:14',
                'updated_at' => '2025-07-22 12:45:14',
            ),
            229 => 
            array (
                'id' => 230,
                'bidding_id' => 228,
                'service_type_id' => 10,
                'created_at' => '2025-07-22 12:49:26',
                'updated_at' => '2025-07-22 12:49:26',
            ),
            230 => 
            array (
                'id' => 231,
                'bidding_id' => 229,
                'service_type_id' => 4,
                'created_at' => '2025-07-23 07:30:07',
                'updated_at' => '2025-07-23 07:30:36',
            ),
            231 => 
            array (
                'id' => 232,
                'bidding_id' => 229,
                'service_type_id' => 10,
                'created_at' => '2025-07-23 07:30:07',
                'updated_at' => '2025-07-23 07:30:36',
            ),
            232 => 
            array (
                'id' => 233,
                'bidding_id' => 230,
                'service_type_id' => 5,
                'created_at' => '2025-07-31 09:28:54',
                'updated_at' => '2025-07-31 09:28:54',
            ),
            233 => 
            array (
                'id' => 234,
                'bidding_id' => 231,
                'service_type_id' => 4,
                'created_at' => '2025-08-06 07:46:49',
                'updated_at' => '2025-08-06 07:46:49',
            ),
            234 => 
            array (
                'id' => 235,
                'bidding_id' => 232,
                'service_type_id' => 4,
                'created_at' => '2025-08-06 08:59:04',
                'updated_at' => '2025-08-06 08:59:04',
            ),
            235 => 
            array (
                'id' => 236,
                'bidding_id' => 233,
                'service_type_id' => 1,
                'created_at' => '2025-08-06 09:06:49',
                'updated_at' => '2025-08-06 09:06:49',
            ),
            236 => 
            array (
                'id' => 237,
                'bidding_id' => 233,
                'service_type_id' => 13,
                'created_at' => '2025-08-06 09:06:49',
                'updated_at' => '2025-08-06 09:06:49',
            ),
            237 => 
            array (
                'id' => 238,
                'bidding_id' => 234,
                'service_type_id' => 4,
                'created_at' => '2025-08-06 09:12:21',
                'updated_at' => '2025-08-06 09:12:21',
            ),
            238 => 
            array (
                'id' => 239,
                'bidding_id' => 234,
                'service_type_id' => 10,
                'created_at' => '2025-08-06 09:12:21',
                'updated_at' => '2025-08-06 09:12:21',
            ),
            239 => 
            array (
                'id' => 240,
                'bidding_id' => 235,
                'service_type_id' => 5,
                'created_at' => '2025-08-06 09:44:03',
                'updated_at' => '2025-08-06 09:44:03',
            ),
            240 => 
            array (
                'id' => 241,
                'bidding_id' => 235,
                'service_type_id' => 14,
                'created_at' => '2025-08-06 09:44:03',
                'updated_at' => '2025-08-06 09:44:03',
            ),
            241 => 
            array (
                'id' => 242,
                'bidding_id' => 236,
                'service_type_id' => 5,
                'created_at' => '2025-08-06 09:47:31',
                'updated_at' => '2025-08-06 09:47:31',
            ),
            242 => 
            array (
                'id' => 243,
                'bidding_id' => 237,
                'service_type_id' => 5,
                'created_at' => '2025-08-07 08:26:50',
                'updated_at' => '2025-08-07 08:26:50',
            ),
            243 => 
            array (
                'id' => 244,
                'bidding_id' => 238,
                'service_type_id' => 4,
                'created_at' => '2025-08-13 06:48:49',
                'updated_at' => '2025-08-20 07:38:08',
            ),
            244 => 
            array (
                'id' => 245,
                'bidding_id' => 239,
                'service_type_id' => 4,
                'created_at' => '2025-08-13 06:51:41',
                'updated_at' => '2025-08-13 06:51:41',
            ),
            245 => 
            array (
                'id' => 246,
                'bidding_id' => 240,
                'service_type_id' => 5,
                'created_at' => '2025-08-13 07:45:45',
                'updated_at' => '2025-08-13 07:45:45',
            ),
            246 => 
            array (
                'id' => 247,
                'bidding_id' => 241,
                'service_type_id' => 4,
                'created_at' => '2025-08-13 07:50:55',
                'updated_at' => '2025-08-13 07:50:55',
            ),
            247 => 
            array (
                'id' => 248,
                'bidding_id' => 242,
                'service_type_id' => 4,
                'created_at' => '2025-08-13 07:53:30',
                'updated_at' => '2025-08-13 07:53:30',
            ),
            248 => 
            array (
                'id' => 249,
                'bidding_id' => 243,
                'service_type_id' => 4,
                'created_at' => '2025-08-13 08:01:06',
                'updated_at' => '2025-08-13 08:01:06',
            ),
            249 => 
            array (
                'id' => 250,
                'bidding_id' => 244,
                'service_type_id' => 4,
                'created_at' => '2025-08-20 07:51:06',
                'updated_at' => '2025-08-20 07:51:06',
            ),
            250 => 
            array (
                'id' => 251,
                'bidding_id' => 245,
                'service_type_id' => 4,
                'created_at' => '2025-08-20 07:55:04',
                'updated_at' => '2025-08-20 07:55:04',
            ),
            251 => 
            array (
                'id' => 252,
                'bidding_id' => 246,
                'service_type_id' => 4,
                'created_at' => '2025-08-28 12:02:37',
                'updated_at' => '2025-08-28 12:02:37',
            ),
            252 => 
            array (
                'id' => 253,
                'bidding_id' => 247,
                'service_type_id' => 5,
                'created_at' => '2025-08-28 12:07:14',
                'updated_at' => '2025-08-28 12:07:14',
            ),
            253 => 
            array (
                'id' => 254,
                'bidding_id' => 248,
                'service_type_id' => 5,
                'created_at' => '2025-09-02 07:17:16',
                'updated_at' => '2025-09-02 07:17:16',
            ),
            254 => 
            array (
                'id' => 255,
                'bidding_id' => 248,
                'service_type_id' => 14,
                'created_at' => '2025-09-02 07:17:16',
                'updated_at' => '2025-09-02 07:17:16',
            ),
            255 => 
            array (
                'id' => 256,
                'bidding_id' => 249,
                'service_type_id' => 5,
                'created_at' => '2025-09-08 07:39:46',
                'updated_at' => '2025-09-08 07:39:46',
            ),
            256 => 
            array (
                'id' => 257,
                'bidding_id' => 249,
                'service_type_id' => 14,
                'created_at' => '2025-09-08 07:39:46',
                'updated_at' => '2025-09-08 07:39:46',
            ),
            257 => 
            array (
                'id' => 258,
                'bidding_id' => 250,
                'service_type_id' => 4,
                'created_at' => '2025-09-08 07:43:35',
                'updated_at' => '2025-09-08 07:43:35',
            ),
            258 => 
            array (
                'id' => 259,
                'bidding_id' => 251,
                'service_type_id' => 4,
                'created_at' => '2025-09-08 07:49:08',
                'updated_at' => '2025-09-08 07:49:08',
            ),
        ));
        
        
    }
}