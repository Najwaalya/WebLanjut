@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit User</h1>

    <form method="POST" action="{{ route('user.update', $user->user_id) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Level ID</label>
            <input type="text" name="level_id" class="form-control" value="{{ $user->level_id }}" required>
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
        </div>
        <div class="form-group">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" value="{{ $user->nama }}" required>
        </div>
        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ url ('/user') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
