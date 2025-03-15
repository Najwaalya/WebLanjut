<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;
use App\DataTables\UserDataTable;

class UserController extends Controller
{
    public function index(UserDataTable $dataTable)
    {
        //$user = UserModel::all();
        //return view('user', ['data' => $user]);

        /*$user = UserModel::with('level')->get();
        return view('user', ['data' => $user]);
        */
        return $dataTable->render('user.index');
    } 

    /*public function tambah()
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

        return redirect('/user');
    }

    public function hapus($id)
    {
        $user = UserModel::find($id);
        $user->delete();

        return redirect('/user');
    }
    */

    public function create()
    {
        return view('user.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'level_id' => 'required|integer',
            'username' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'password' => 'required|min:6'
        ]);
        
        UserModel::create([
            'level_id' => $request->level_id,
            'username' => $request->username,
            'nama' => $request->nama,
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = UserModel::findOrFail($id);
        return view('user.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'level_id' => 'required|integer',
            'username' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
        ]);

        $user = UserModel::findOrFail($id);
        $user->update([
            'level_id' => $request->level_id,
            'username' => $request->username,
            'nama' => $request->nama,
        ]);

        return redirect()->route('user.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = UserModel::findOrFail($id);
        $user->delete();
        return response()->json(['success' => 'User berhasil dihapus!']);
    }
}
