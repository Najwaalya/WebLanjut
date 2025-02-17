<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Item::all(); // Mengambil semua data item dari model Item
        return view('items.index', compact('item'));  // Mengembalikan tampilan 'items.index' dan mengirimkan data items
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('items.create'); // Mengembalikan tampilan form untuk membuat item
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data inputan dari pengguna
        $request->validate([
            'name' => 'required', // Kolom 'name' wajib diisi
            'description' => 'required', // Kolom 'description' wajib diisi
        ]);

        Item::create($request->only(['name', 'description'])); // Menyimpan data item yang valid ke dalam database
        return redirect()->route('item.index')->with('success', 'Item added successfully.');  // Redirect ke halaman index dengan pesan sukses
    }

    /**
     * Display the specified resource.
     */
    public function show(string $item)
    {
        // Mengembalikan tampilan 'item.show' dan mengirimkan data item
        return view('item.show', compact('item')); // Parameter 'item' seharusnya adalah objek, bukan string
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $item)
    {
        // Mengembalikan tampilan form edit dan mengirimkan data item yang ingin diedit
        return view('items.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $item)
    {
        // Validasi data inputan dari pengguna
        $request->validate([
            'name' => 'required', // Kolom 'name' wajib diisi
            'description' => 'required', // Kolom 'description' wajib diisi
        ])
        // Hanya masukkan atribut yang diizinkan
        // Mengupdate data item yang ada dengan atribut yang diizinkan
        $item->update($request->only(['name', 'description']));

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('items.index')->with('success', 'Item updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $item)
    {
        // Menghapus item dari database
        $item-> delete();
        
        // Redirect ke halaman index dengan pesan sukses
        return redirect()-> route('items.index')->with('success', 'Item deleted successfully');
    }
}
