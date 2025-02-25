<!DOCTYPE html>
<html>
<head>
    <title>Add Item</title> <!-- menentukan judul halaman yang muncul di tab browser -->
</head>
<body>
    <h1>Add Item</h1> <!-- menampilkan judul utama halaman, "Add Item" -->

    <!-- form untuk menambah item baru, mengarah ke route 'items.store' dengan metode POST -->
    <form action="{{ route('items.store') }}" method="POST">
        @csrf <!-- menambahkan token CSRF untuk keamanan form -->
        
        <!-- label dan input untuk nama item -->
        <label for="name">Name:</label>
        <input type="text" name="name" required> <!-- input teks untuk nama item, wajib diisi -->
        <br>
        
        <!-- label dan textarea untuk deskripsi item -->
        <label for="description">Description:</label>
        <textarea name="description" required></textarea> <!-- textarea untuk deskripsi item, wajib diisi -->
        <br>
        
        <!-- tombol untuk mengirimkan form -->
        <button type="submit">Add Item</button>
    </form>

    <!-- link untuk kembali ke halaman daftar item -->
    <a href="{{ route('items.index') }}">Back to List</a>
</body>
</html>