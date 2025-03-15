@extends('layouts.app')
 
 @section('content')
 <div class="container">
     <h2>Edit Barang</h2>
     <form action="{{ route('barang.update', $barang->barang_id) }}" method="POST">
         @csrf
         @method('PUT')
         
         <div class="mb-3">
             <label for="kategori_id" class="form-label">ID Kategori</label>
             <input type="text" class="form-control" id="kategori_id" name="kategori_id" value="{{ $barang->kategori_id }}" required>
         </div>
 
         <div class="mb-3">
             <label for="kode_barang" class="form-label">Kode Barang</label>
             <input type="text" class="form-control" id="kode_barang" name="kode_barang" value="{{ $barang->kode_barang }}" required>
         </div>
 
         <div class="mb-3">
             <label for="nama_barang" class="form-label">Nama Barang</label>
             <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="{{ $barang->nama_barang }}" required>
         </div>
 
         <div class="mb-3">
             <label for="harga_beli" class="form-label">Harga Beli</label>
             <input type="text" class="form-control" id="harga_beli" name="harga_beli" value="{{ $barang->harga_beli }}" required>
         </div>
 
         <div class="mb-3">
             <label for="harga_jual" class="form-label">Harga Jual</label>
             <input type="text" class="form-control" id="harga_jual" name="harga_jual" value="{{ $barang->harga_jual }}" required>
         </div>
         <button type="submit" class="btn btn-primary">Update</button>
         <a href="{{ url ('/barang') }}" class="btn btn-secondary">Kembali</a>
     </form>
 </div>
 @endsection