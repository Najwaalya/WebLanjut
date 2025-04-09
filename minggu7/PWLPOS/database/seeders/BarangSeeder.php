<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['barang_id' => 1, 'kategori_id' => 1, 'kode_barang' => 'ELK001', 'nama_barang' => 'Laptop', 'harga_beli' => 8000000, 'harga_jual' => 10000000],
            ['barang_id' => 2, 'kategori_id' => 1, 'kode_barang' => 'ELK002', 'nama_barang' => 'Smartphone', 'harga_beli' => 4000000, 'harga_jual' => 5000000],
            ['barang_id' => 3, 'kategori_id' => 2, 'kode_barang' => 'PKN001', 'nama_barang' => 'Kaos', 'harga_beli' => 75000, 'harga_jual' => 100000],
            ['barang_id' => 4, 'kategori_id' => 2, 'kode_barang' => 'PKN002', 'nama_barang' => 'Celana Jeans', 'harga_beli' => 150000, 'harga_jual' => 200000],
            ['barang_id' => 5, 'kategori_id' => 3, 'kode_barang' => 'MKN001', 'nama_barang' => 'Nasi Goreng', 'harga_beli' => 20000, 'harga_jual' => 25000],
            ['barang_id' => 6, 'kategori_id' => 3, 'kode_barang' => 'MKN002', 'nama_barang' => 'Roti', 'harga_beli' => 10000, 'harga_jual' => 15000],
            ['barang_id' => 7, 'kategori_id' => 4, 'kode_barang' => 'MNM001', 'nama_barang' => 'Teh Botol', 'harga_beli' => 4000, 'harga_jual' => 5000],
            ['barang_id' => 8, 'kategori_id' => 4, 'kode_barang' => 'MNM002', 'nama_barang' => 'Kopi', 'harga_beli' => 8000, 'harga_jual' => 10000],
            ['barang_id' => 9, 'kategori_id' => 5, 'kode_barang' => 'PRT001', 'nama_barang' => 'Kompor Gas', 'harga_beli' => 250000, 'harga_jual' => 300000],
            ['barang_id' => 10, 'kategori_id' => 5, 'kode_barang' => 'PRT002', 'nama_barang' => 'Setrika', 'harga_beli' => 150000, 'harga_jual' => 200000],
        ];

        DB::table('m_barang')->insert($data);
    }
}
