<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturPenjualanModel extends Model
{
    use HasFactory;

    protected $table = 'retur_penjualan';
    protected $primaryKey = 'retur_id';
    protected $fillable = [
        'penjualan_id',
        'barang_id',
        'jumlah',
        'alasan',
        'tanggal_retur'
    ];

    public function penjualan()
    {
        return $this->belongsTo(PenjualanModel::class, 'penjualan_id', 'penjualan_id');
    }

    public function barang()
    {
        return $this->belongsTo(BarangModel::class, 'barang_id', 'barang_id');
    }
}
