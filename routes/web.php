<?php

use Illuminate\Support\Facades\Route; // Mengimpor facade Route untuk mendefinisikan rute web
use App\Http\Controllers\ItemController; // Mengimpor controller ItemController untuk menangani rute terkait item

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // Rute untuk halaman utama (root) yang menampilkan tampilan 'welcome'
    return view('welcome');
});

Route::resource('items', ItemController::class);
// Mendefinisikan rute untuk resource 'items', yang otomatis menghubungkan berbagai 
// metode HTTP dengan metode pada ItemController (index, create, store, show, edit, update, destroy)