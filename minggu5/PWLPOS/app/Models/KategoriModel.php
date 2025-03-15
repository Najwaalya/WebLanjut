<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriModel extends Model
{
    protected $table = 'm_kategori';
    protected $primarykey = 'kategori_id';

    protected $fillable = ['kategori_kode', 'kategori_nama'];

    public function barang(): HasMany
    {
        return $this->HasMany(BarangModel::class, 'barang_id', 'barang_id');
    }
}
