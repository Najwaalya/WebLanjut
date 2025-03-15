<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\DataTables\KategoriDataTable;
use App\Models\KategoriModel;

class KategoriController extends Controller
{
    public function index(KategoriDataTable $dataTable)
    {
        /*$data = [
            'kategori_kode' => 'SNK',
            'kategori_nama' => 'Snack/Makanan Ringan',
            'created_at' => now()
        ];*/
        //DB::table('m_kategori')->insert($data);
        //return 'Insert data baru berhasil';

        //$row = DB::table('m_kategori')->where('kategori_kode', 'SNK')->update(['kategori_nama' => 'Camilan']);
        //return 'Update data berhasil. Jumlah data yang diupdate: ' .$row.' baris';

        //$row = DB::table('m_kategori')->where('kategori_kode', 'SNK')->delete();
        //return 'Update data dihapus. Jumlah data yang dihapus: ' .$row.' baris';

        //$data = DB::table('m_kategori')->get();
        //return view('kategori', ['data' => $data]);

        return $dataTable->render('kategori.index');
    }

    public function create()
    {
        return view('kategori.create');
    }

    public function store(Request $request)
    {
        KategoriModel::create([
            'kategori_kode' =>$request->kodeKategori,
            'kategori_nama' =>$request->namaKategori,
        ]);
    }
        
    public function edit($id)
    {
        $kategori = KategoriModel::where('kategori_id', $id)->firstOrFail();
        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori_kode' => 'required|string|max:50',
            'kategori_kode' => 'required|string|max:10',
            'kategori_nama' => 'required|string|max:100',
        ]);
 
        $kategori = KategoriModel::where('kategori_id', $id)->firstOrFail();
        $kategori->update([
            'kategori_kode' => $request->kategori_kode,
            'kategori_nama' => $request->kategori_nama,
        ]);
 
        return redirect('/kategori')->with('success', 'Kategori berhasil diperbarui!');
        return redirect('/kategori');
    }

    public function destroy($id)
    {
        $kategori = KategoriModel::findOrFail($id);
        $kategori->delete();
 
        return redirect('/kategori');
    }
}
