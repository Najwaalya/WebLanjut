<!DOCTYPE html>
<html>
<head>
    <title>Edit Item</title> <!-- menentukan judul halaman yang muncul di tab browser -->
</head>
<body>
    <h1>Edit Item</h1> <!-- menampilkan judul utama halaman, "Edit Item" -->

    <!-- form untuk mengedit item yang sudah ada, mengarah ke route 'items.update' dengan metode PUT -->
    <form action="{{ route('items.update', $item) }}" method="POST">
        @csrf <!-- menambahkan token CSRF untuk keamanan form -->
        @method('PUT') <!-- menambahkan metode PUT untuk mengupdate data item -->

        <!-- label dan input untuk nama item, dengan nilai awal dari nama item yang akan diedit -->
        <label for="name">Name:</label>
        <input type="text" name="name" value="{{ $item->name }}" required> <!-- input teks untuk nama item, diisi dengan nama item yang ada -->

        <br>

        <!-- label dan textarea untuk deskripsi item, dengan nilai awal dari deskripsi item yang akan diedit -->
        <label for="description">Description:</label>
        <textarea name="description" required>{{ $item->description }}</textarea> <!-- textarea untuk deskripsi item, diisi dengan deskripsi item yang ada -->

        <br>

        <!-- tombol untuk mengirimkan form yang berisi perubahan -->
        <button type="submit">Update Item</button>
    </form>

    <!-- link untuk kembali ke halaman daftar item -->
    <a href="{{ route('items.index') }}">Back to List</a>
</body>
</html>