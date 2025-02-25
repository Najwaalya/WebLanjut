<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($id)
    {
        return 'Halaman Artikel dengan ID '. $id;
    }
}
