@extends('layouts.app')
 
 {{-- Customize layout sections --}}
 
 @section('subtitle', 'Kategori')
 @section('content_header_title', 'Home')
 @section('content_header_subtitle', 'Kategori')
 
 @section('content')
     <div class="container">
         <div class="card">
             <div class="card-header d-flex justify-content-between align-items-center" >
                <h5>Manage Kategori</h5>
            </div>
             <div class="card-body" >
             <a href="{{ url('kategori/create') }}" class="btn btn-primary btn-lg px-4 ml-auto">+ Add</a>
                 {{ $dataTable->table() }}
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
     {{ $dataTable->scripts() }}
 @endpush