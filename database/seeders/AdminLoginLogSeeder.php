<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminLoginLogSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $rows = [
            [1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-07-08 23:08:33', '2025-07-08 20:08:33'],
            [2, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-07-29 15:12:09', '2025-07-29 12:12:09'],
            [3, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-07-30 13:41:59', '2025-07-30 10:41:59'],
            [4, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-07-30 17:16:18', '2025-07-30 14:16:18'],
            [5, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-08-06 12:35:58', '2025-08-06 09:35:58'],
            [6, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-08-07 14:42:32', '2025-08-07 11:42:32'],
            [7, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-08-07 15:04:35', '2025-08-07 12:04:35'],
            [8, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-08-12 00:36:13', '2025-08-11 21:36:13'],
            [9, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-08-12 13:29:29', '2025-08-12 10:29:29'],
            [10, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-08-15 00:39:01', '2025-08-14 21:39:01'],
            [11, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-08-16 14:42:21', '2025-08-16 11:42:21'],
            [12, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-08-17 14:02:58', '2025-08-17 11:02:58'],
            [13, 'dev.mohamedsaid@gmail.com', '::1', 'Desktop', '2025-08-20 14:55:59', '2025-08-20 11:55:59'],
            [14, 'dev.mohamedsaid@gmail.com', '::1', 'Desktop', '2025-08-20 15:34:18', '2025-08-20 12:34:18'],
            [15, 'dev.mohamedsaid@gmail.com', '::1', 'Desktop', '2025-08-20 18:33:23', '2025-08-20 15:33:23'],
            [16, 'dev.mohamedsaid@gmail.com', '::1', 'Desktop', '2025-08-20 18:36:06', '2025-08-20 15:36:06'],
            [17, 'dev.mohamedsaid@gmail.com', '::1', 'Desktop', '2025-08-23 15:58:13', '2025-08-23 12:58:13'],
            [18, 'dev.mohamedsaid@gmail.com', '::1', 'Desktop', '2025-08-23 23:13:24', '2025-08-23 20:13:24'],
            [19, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-08-25 15:38:26', '2025-08-25 12:38:26'],
            [20, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-08-25 21:03:10', '2025-08-25 18:03:10'],
            [21, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-01 14:23:14', '2025-09-01 11:23:14'],
            [22, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-01 16:21:25', '2025-09-01 13:21:25'],
            [23, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-01 21:46:00', '2025-09-01 18:46:00'],
            [24, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-01 22:33:05', '2025-09-01 19:33:05'],
            [25, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-02 15:35:30', '2025-09-02 12:35:30'],
            [26, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-07 20:55:09', '2025-09-07 17:55:09'],
            [27, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-08 23:40:11', '2025-09-08 20:40:11'],
            [28, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-09 17:03:41', '2025-09-09 14:03:41'],
            [29, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-10 13:54:50', '2025-09-10 10:54:50'],
            [30, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-10 13:55:32', '2025-09-10 10:55:32'],
            [31, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-13 15:20:13', '2025-09-13 12:20:13'],
            [32, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-13 15:41:33', '2025-09-13 12:41:33'],
            [33, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-18 15:11:58', '2025-09-18 12:11:58'],
            [34, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-20 12:52:45', '2025-09-20 09:52:45'],
            [35, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-10-05 16:54:32', '2025-10-05 13:54:32'],
            [36, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-10-07 13:34:53', '2025-10-07 10:34:53'],
            [37, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-10-07 15:24:33', '2025-10-07 12:24:33'],
            [38, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-10-08 13:26:06', '2025-10-08 10:26:06'],
            [39, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-10-26 14:30:52', '2025-10-26 11:30:52'],
            [40, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-11-04 13:04:16', '2025-11-04 11:04:16'],
            [41, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-11-05 12:55:18', '2025-11-05 10:55:18'],
            [42, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-11-10 19:55:50', '2025-11-10 17:55:50'],
            [43, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-11-11 00:19:57', '2025-11-10 22:19:57'],
            [44, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-11-13 20:48:35', '2025-11-13 18:48:35'],
            [45, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-12-11 19:40:21', '2025-12-11 17:40:21'],
            [46, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-12-22 14:37:40', '2025-12-22 12:37:40'],
            [47, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-12-22 14:51:58', '2025-12-22 12:51:58'],
            [48, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-12-22 16:46:16', '2025-12-22 14:46:16'],
            [49, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-12-28 15:06:43', '2025-12-28 13:06:43'],
            [50, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-01-08 16:14:38', '2026-01-08 14:14:38'],
            [51, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-01-31 14:18:24', '2026-01-31 12:18:24'],
            [52, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-02-04 17:13:44', '2026-02-04 15:13:44'],
            [53, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-02-04 17:25:40', '2026-02-04 15:25:40'],
            [54, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-02-04 17:47:32', '2026-02-04 15:47:32'],
            [55, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-02-04 20:58:11', '2026-02-04 18:58:11'],
            [56, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-02-05 13:01:22', '2026-02-05 11:01:22'],
            [57, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-02-05 20:28:01', '2026-02-05 18:28:01'],
            [58, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-02-10 20:45:32', '2026-02-10 18:45:32'],
            [59, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-02-11 12:40:46', '2026-02-11 10:40:46'],
            [60, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-02-18 16:24:03', '2026-02-18 14:24:03'],
            [61, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-02-18 17:34:13', '2026-02-18 15:34:13'],
            [62, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-02-19 12:54:31', '2026-02-19 10:54:31'],
            [63, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-02-22 14:07:19', '2026-02-22 12:07:19'],
            [64, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-02-26 13:07:17', '2026-02-26 11:07:17'],
            [65, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-03-02 12:46:31', '2026-03-02 10:46:31'],
            [66, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-03-05 00:27:12', '2026-03-04 22:27:12'],
            [67, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-04-01 13:44:14', '2026-04-01 11:44:14'],
            [68, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-04-27 15:54:30', '2026-04-27 12:54:30'],
            [69, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-05-09 11:03:03', '2026-05-09 08:03:03'],
        ];

        $payload = array_map(static fn (array $row) => [
            'id' => $row[0],
            'admin_id' => 1,
            'email' => $row[1],
            'ip' => $row[2],
            'device_type' => $row[3],
            'logged_at' => $row[4],
            'created_at' => $row[5],
            'updated_at' => $row[5],
        ], $rows);

        foreach (array_chunk($payload, 250) as $chunk) {
            $this->schemaAwareUpsert('admin_login_logs', $chunk, ['id'], ['ip', 'device_type', 'logged_at', 'updated_at']);
        }
    }
}
