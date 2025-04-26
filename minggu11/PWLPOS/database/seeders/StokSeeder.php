<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class StokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];

        for ($i = 1; $i <= 10; $i++) {
            $data[] = [
                'barang_id' => $i,
                'user_id' => rand(1, 3), 
                'stok_tanggal' => Carbon::now()->subDays(rand(1, 30)), 
                'stok_jumlah' => rand(10, 50),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('t_stok')->insert($data);
    }
}
