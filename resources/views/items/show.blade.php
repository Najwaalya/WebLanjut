<!DOCTYPE html>
<html>
<head>
    <title>Item List</title> <!-- menentukan judul halaman yang muncul di tab browser -->
</head>
<body>
    <h1>Items</h1> <!-- menampilkan judul utama halaman, "Items" -->

    <!-- menampilkan pesan sukses jika ada (misalnya setelah menambah, mengupdate, atau menghapus item) -->
    @if(session('success'))
        <p>{{ session('success') }}</p> <!-- menampilkan pesan sukses dari session -->
    @endif

    <!-- link untuk menambahkan item baru, mengarah ke halaman form pembuatan item -->
    <a href="{{ route('items.create') }}">Add Item</a>

    <ul>
        <!-- looping untuk menampilkan semua item -->
        @foreach ($items as $item)
            <li>
                <!-- menampilkan nama item -->
                {{ $item->name }} - 
                
                <!-- link untuk mengedit item tertentu -->
                <a href="{{ route('items.edit', $item) }}">Edit</a>
                
                <!-- form untuk menghapus item, dengan metode POST dan method spoofing DELETE -->
                <form action="{{ route('items.destroy', $item) }}" method="POST" style="display:inline;">
                    @csrf <!-- menambahkan token CSRF untuk keamanan form -->
                    @method('DELETE') <!-- menambahkan metode DELETE untuk penghapusan item -->
                    
                    <!-- tombol untuk menghapus item -->
                    <button type="submit">Delete</button>
                </form>
            </li>
        @endforeach
    </ul>
</body>
</html>