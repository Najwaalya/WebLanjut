<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index(){
        return 'Selamat Datang';
    }

    public function about(){
        return 'Nama: Najwa Alya Nurizzah <br> NIM: 2341720230';
    }

    public function articles($id){
        return 'Halaman Artikel dengan ID '. $id;
    }
}
