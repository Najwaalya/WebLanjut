@extends('layouts.app')
 
 @section('content')
 <div class="container">
     <h2>Edit Kategori</h2>

     <form method="POST" action="{{ route('kategori.update', $kategori->kategori_id) }}">
         @csrf
         @method('PUT')
         <div class="form-group">
             <label for="kategori_id" class="form-label" >Kode Kategori</label>
             <input type="text" class="form-control" id="kategori_kode" name="kategori_kode" value="{{ $kategori->kategori_kode }}" required>
         </div>
 
         <div class="form-group">
             <label for="kategori_nama" class="form-label">Nama Kategori</label>
             <input type="text" class="form-control" id="kategori_nama" name="kategori_nama" value="{{ $kategori->kategori_nama }}" required>
         </div>
 
         <button type="submit" class="btn btn-primary">Update</button>
         <a href="{{ url ('/kategori') }}" class="btn btn-secondary">Kembali</a>
     </form>
 </div>
 @endsection