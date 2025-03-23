<?php
 
 namespace App\Models;
 
 use Illuminate\Database\Eloquent\Factories\HasFactory;
 use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
 class BarangModel extends Model
 {
     use HasFactory;
 
     protected $table = 'm_barang';
     protected $primaryKey = 'barang_id';
 
     protected $fillable = ['kategori_id', 'kode_barang', 'nama_barang', 'harga_beli', 'harga_jual'];
 
     public function kategori()
     {
         return $this->belongsTo(KategoriModel::class, 'kategori_id', 'kategori_id');
     }
 }