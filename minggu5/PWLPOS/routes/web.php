<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    /*return view('welcome'); */
    return view('home');
})->name('home');

Route::get('/level', [LevelController::class, 'index']);
Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
Route::get('/user', [UserController::class, 'index']);
Route::get('/user/tambah', [UserController::class, 'tambah']);
Route::post('/user/tambah_simpan', [UserController::class, 'tambah_simpan']);
Route::get('/user/ubah/{id}', [UserController::class, 'ubah']);
Route::put('/user/ubah_simpan/{id}', [UserController::class, 'ubah_simpan']);
Route::get('/user/hapus/{id}', [UserController::class, 'hapus']);

Route::get('/kategori/create', [KategoriController::class, 'create'])->name('kategori.create');
Route::get('/kategori/data', [KategoriController::class, 'data'])->name('kategori.data');
Route::post('/kategori', [KategoriController::class, 'store']);
Route::get('/kategori/{id}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
Route::put('/kategori/{id}', [KategoriController::class, 'update'])->name('kategori.update');
Route::post('/kategori/delete/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');