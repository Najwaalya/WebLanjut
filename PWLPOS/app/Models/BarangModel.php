<?php
 
 namespace App\Models;

 use Illuminate\Database\Eloquent\Factories\HasFactory;
 use Illuminate\Database\Eloquent\Model;
 
 class BarangModel extends Model
 {
     use HasFactory;
 
     protected $table = 'm_barang'; // Pastikan ini benar sesuai dengan nama tabel di database
     protected $primaryKey = 'barang_id'; // Pastikan primary key sesuai dengan database
     public $timestamps = true;
 
     protected $fillable = [
         'kategori_id',
         'kode_barang',
         'nama_barang',
         'harga_beli',
         'harga_jual',
     ];
 } 