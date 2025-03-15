@extends('layouts.app')
 
 {{-- Customize layout sections --}}
 
 @section('subtitle', 'Barang')
 @section('content_header_title', 'Barang')
 @section('content_header_subtitle', 'Create')
 
 {{-- Content body: main page content --}}
 
 @section('content')
     <div class="container">
         <div class="card card-primary">
             <div class="card-header">
                 <h3 class="card-title">Tambah Barang Baru</h3>
             </div>

             <form method="POST" action="{{ route('barang.store') }}">
             @csrf
                <div class="card-body">
                    <div class="form-group">
                         <label for="kategori_id">ID Kategori</label>
                         <input type="text" class="form-control" id="kategori_id" name="kategori_id" placeholder="">
                     </div>
                     <div class="form-group">
                         <label for="kode_barang">Kode Barang</label>
                         <input type="text" class="form-control" id="kode_barang" name="kode_barang" placeholder="">
                     </div>
                     <div class="form-group">
                         <label for="nama_barang">Nama Barang</label>
                         <input type="text" class="form-control" id="nama_barang" name="nama_barang" placeholder="">
                     </div>
                     <div class="form-group">
                         <label for="harga_beli">Harga Beli</label>
                         <input type="text" class="form-control" id="harga_beli" name="harga_beli" placeholder="">
                     </div>
                     <div class="form-group">
                         <label for="harga_jual">Harga Jual</label>
                         <input type="text" class="form-control" id="harga_jual" name="harga_jual" placeholder="">
                    </div>
                </div>

                 <div class="card-footer">
                     <button type="submit" class="btn btn-primary">Submit</button>
                 </div>
             </form>
         </div>
     </div>
 @endsection