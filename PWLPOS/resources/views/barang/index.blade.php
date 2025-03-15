@extends('layouts.app')
 
 {{-- Customize layout sections --}}
 @section('subtitle', 'Barang')
 @section('content_header_title', 'Home')
 @section('content_header_subtitle', 'Barang')
 
 @section('content')
     <div class="container">
         <div class="card">
             <div class="card-header d-flex justify-content-between align-items-center"">
                 <span>Manage Barang</span>
             </div>
             <div class="card-body">
                    <a href="{{ route('barang.create') }}" class="btn btn-primary">+ Add</a>
                 {!! $dataTable->table() !!}
             </div>
         </div>
     </div>
 @endsection

 @push('styles')
    <style>
        .card-body {
            max-height: 80vh; /* Sesuaikan tinggi agar footer tetap terlihat */
            overflow-y: auto;
        }
    </style>
@endpush
 
 @push('scripts')
     {!! $dataTable->scripts() !!}
 @endpush