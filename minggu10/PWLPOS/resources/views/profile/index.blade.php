@extends('layouts.template')

@section('content')
<div class="container d-flex justify-content-center">
    <div class="card card-primary mt-5 shadow-sm" style="max-width: 1000px; width: 100%;">
        <div class="card-body px-4 py-5">
            
            <div class="row align-items-center">
                <!-- Kolom Foto Profil -->
                <div class="col-md-4 text-center mb-4 mb-md-0">
                    <form action="{{ url('profile/update_photo') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <img src="{{ $user->foto ? asset('storage/foto/' . $user->foto) : asset('adminlte/dist/img/user4-128x128.jpg') }}" 
                            alt="Foto Profil" class="rounded-circle mb-3" width="160" height="160" 
                            style="object-fit: cover;">

                        <div class="form-group mt-4">
                            <label class="btn btn-primary">
                                Edit <input type="file" name="foto" class="d-none" accept="image/*" onchange="this.form.submit()">
                            </label>
                        </div>
                    </form>
                </div>

                <!-- Kolom Informasi Pengguna -->
                <div class="col-md-8">
                    <h4 class="text-primary mb-1">Informasi Pengguna</h4>
                    <hr class="border border-muted opacity-100 mb-4">

                    <div class="mb-3">
                        <h6 class="text-muted">Nama</h6>
                        <h5 class="fw-bold">{{ $user->nama }}</h5>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted">Username</h6>
                        <h5 class="fw-bold">{{ $user->username }}</h5>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted">Level Pengguna</h6>
                        <h5 class="fw-bold">{{ $user->level->level_nama }}</h5>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- SweetAlert2 -->
<script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '{{ session("success") }}',
            showConfirmButton: false,
            timer: 2000
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: '{{ session("error") }}'
        });
    @endif
</script>
@endsection