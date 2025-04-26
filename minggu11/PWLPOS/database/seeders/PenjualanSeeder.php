<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];

        for ($i = 1; $i <= 10; $i++) {
            $data[] = [
                'user_id' => rand(1, 3), 
                'penjualan_kode' => 'PNJ00' . $i,
                'pembeli' => 'Pembeli ' . $i,
                'penjualan_tanggal' => Carbon::now()->subDays(rand(1, 30)), 
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('t_penjualan')->insert($data);
    }
}
