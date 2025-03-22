<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        /* tambah data user dengan Eloquent Model
        $data = [
            'nama' => 'Pelanggan Pertama',
        ];
        UserModel::where('username', 'customer-1')->update($data); // tambahkan data ke tabel m_user

        //coba akses model UserModel
        $user = UserModel::all(); // ambil semua data dari tabel m_user
        return view('user', ['data' => $user]);

        $user = UserModel::findOr(20, ['username', 'nama'], function(){
            abort(404);
        });

        $user = UserModel::where('username', 'manager9')->firstOrFail();
        $user = UserModel::where('level_id', 2)->count();
        $user = UserModel::firstOrNew(
        
        $user = UserModel::create(
            [
                'username' => 'manager11',
                 'nama' => 'Manager11',
                'password' => Hash::make('12345'),
                'level_id' => 2,
            ],
        );
        
        $user->username = 'manager12';
        */

        $user = UserModel::with('level')->get();
        return view('user', ['data' => $user]);;
     } 
 
     public function tambah()
     {
         return view('user_tambah');
     }
 
     public function tambah_simpan(Request $request)
     {
        UserModel::create([
             'username' => $request->username,
             'nama' => $request->nama,
             'password' => Hash::make($request->password),
             'level_id' => $request->level_id
         ]);
 
         return redirect('/user');
     }
 
     public function ubah($id)
     {
         $user = UserModel::find($id);
         return view('user_ubah', ['data' => $user]);
     }
 
     public function ubah_simpan($id, Request $request)
     {
         $user = UserModel::find($id);
 
         $user->username = $request->username;
         $user->nama = $request->nama;
         $user->password = Hash::make('$request->password');
         $user->level_id = $request->level_id;
        $user->save();

        /* $user->wasChanged();
        $user->wasChanged('username');
        $user->wasChanged(['username', 'level_id']);
        $user->wasChanged('nama');
        dd($user->wasChanged(['username', 'level_id']));
        */

        return redirect('/user');
    }

    public function hapus($id)
    {
        $user = UserModel::find($id);
        $user->delete();

        return redirect('/user');
    }
}
