<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    // menampilkan daftar item
    public function index()
    {
        $items = Item::all(); // mengambil semua data item dari database
        return view('items.index', compact('items')); // mengirim data item ke tampilan 'items.index'
    }

    // menampilkan form untuk membuat item baru
    public function create()
    {
        return view('items.create'); // mengarahkan ke tampilan form pembuatan item
    }

    // menyimpan item baru ke dalam database
    public function store(Request $request)
    {
        $request->validate([ // memvalidasi data yang diterima
            'name' => 'required', // nama item wajib diisi
            'description' => 'required', // deskripsi item wajib diisi
        ]);

        // Item::create($request->all()); // sebelumnya, menyimpan semua data yang diterima
        // return redirect()->route('items.index'); // mengarahkan kembali ke halaman daftar item

        // hanya masukkan atribut yang diizinkan
        Item::create($request->only(['name', 'description'])); // menyimpan data nama dan deskripsi item ke dalam database
        return redirect()->route('items.index')->with('success', 'Item added successfully.'); // mengarahkan kembali dan menampilkan pesan sukses
    }

    // menampilkan detail item tertentu
    public function show(Item $item)
    {
        return view('items.show', compact('item')); // mengirim data item ke tampilan 'items.show'
    }

    // menampilkan form untuk mengedit item
    public function edit(Item $item)
    {
        return view('items.edit', compact('item')); // mengirim data item ke tampilan form edit
    }

    // memperbarui data item di database
    public function update(Request $request, Item $item)
    {
        $request->validate([ // memvalidasi data yang diterima
            'name' => 'required', // nama item wajib diisi
            'description' => 'required', // deskripsi item wajib diisi
        ]);

        // $item->update($request->all()); // sebelumnya, memperbarui semua data yang diterima
        // return redirect()->route('items.index'); // mengarahkan kembali ke halaman daftar item

        // hanya masukkan atribut yang diizinkan
        $item->update($request->only(['name', 'description'])); // memperbarui data nama dan deskripsi item
        return redirect()->route('items.index')->with('success', 'Item updated successfully.'); // mengarahkan kembali dan menampilkan pesan sukses
    }

    // menghapus item dari database
    public function destroy(Item $item)
    {
        // return redirect()->route('items.index'); // sebelumnya, mengarahkan kembali tanpa menghapus item
        $item->delete(); // menghapus item dari database
        return redirect()->route('items.index')->with('success', 'Item deleted successfully.'); // mengarahkan kembali dan menampilkan pesan sukses
    }
}