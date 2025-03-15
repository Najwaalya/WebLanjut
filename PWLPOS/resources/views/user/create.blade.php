@extends('layouts.app')

{{-- Customize layout sections --}}
@section('subtitle', 'User')
@section('content_header_title', 'User')
@section('content_header_subtitle', 'Create')

{{-- Content body: main page content --}}
@section('content')
    <div class="container">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Buat User Baru</h3>
            </div>

            <form method="POST" action="{{ route('user.store') }}">
                @csrf
                <div class="card-body">
                    <div class="form-grup">
                        <label for="levelID">Level ID</label>
                        <input type="text" class="form-control" name="level_id" id="level_id" required>
                    </div>
                    <div class="form-grup">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" id="username" required>
                    </div>
                    <div class="form-grup">
                        <label>Nama</label>
                        <input type="text" name="nama" class="form-control" id="nama" required>
                    </div>
                    <div class="form-grup">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" id="password" required>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection
