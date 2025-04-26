<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReturPenjualanSeeder extends Seeder
{
    public function run()
    {
        // Mengisi data untuk tabel retur_penjualan
        DB::table('retur_penjualan')->insert([
            [
                'penjualan_id' => 1,  // Asumsikan penjualan_id 1 ada di tabel penjualan
                'barang_id' => 2,     // Asumsikan barang_id 2 ada di tabel barang
                'jumlah' => 1,
                'alasan' => 'Barang rusak',
                'tanggal_retur' => Carbon::now(),
            ],
            [
                'penjualan_id' => 1,
                'barang_id' => 3,
                'jumlah' => 2,
                'alasan' => 'Tidak sesuai dengan pesanan',
                'tanggal_retur' => Carbon::now()->subDays(2),
            ],
            [
                'penjualan_id' => 1,
                'barang_id' => 5,
                'jumlah' => 1,
                'alasan' => 'Barang cacat pabrik',
                'tanggal_retur' => Carbon::now()->subWeek(),
            ],
            // Tambahkan data lainnya sesuai dengan kebutuhan
        ]);
    }
}
