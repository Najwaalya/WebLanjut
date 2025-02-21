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

Route::get('/hello', function(){
    return 'Hello World';
});

Route::get('/world', function(){
    return 'World';
}); 

Route::get('/', function(){
    return 'Selamat Datang';
}); 

Route::get('/about', function(){
    return 'NIM: 2341720230 <br> Nama: Najwa Alya Nurizzah';
});

Route::get('/user/{name}', function($name){
    return 'Nama saya '.$name;
});

Route::get('/posts/{post}/comments/{comment}', function($postId, $commentId){
    return 'Pos ke-'.$postId." Komentar ke-: ".$commentId;
});

Route::get('/articles/{id}', function($id){
    return 'Halaman Artikel dengan ID '.$id;
});

Route::get('/user/{name?}', function($name=null){
    return 'Nama saya '.$name;
});

Route::resource('items', ItemController::class);
// Mendefinisikan rute untuk resource 'items', yang otomatis menghubungkan berbagai 
// metode HTTP dengan metode pada ItemController (index, create, store, show, edit, update, destroy)