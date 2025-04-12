<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\PenjualanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
 Route::pattern('id', '[0-9]+'); // artinya ketika ada parameter (id), maka harus berupa angka
  
 Route::get('login', [AuthController::class, 'login'])->name('login');
 Route::post('login', [AuthController::class, 'postlogin']);
 Route::get('register', [AuthController::class, 'register']);
 Route::post('postRegister', [AuthController::class, 'postRegister']);
 Route::get('logout', [AuthController::class, 'logout'])->middleware('auth');
 
 Route::middleware(['auth'])->group(function () { // artinya semua route di dalam group ini harus login dulu
 
     Route::get('/', [WelcomeController::class, 'index']);
     

     Route::group(['prefix' => 'profile'], function () {
        Route::get('/', [ProfileController::class, 'index']);
        Route::post('/update_photo', [ProfileController::class, 'update_photo']);
        Route::post('/logout', function () {
            Auth::logout();
            return redirect('/login');
        })->name('logout');
    });

    // route Level
    // artinya semua route di dalam group ini harus punya role ADM (Administrator)
    Route::group(['prefix' => 'level'], function () {
        Route::middleware(['authorize:ADM'])->group(function () {
            Route::get('/', [LevelController::class, 'index']);          // menampilkan halaman awal level
            Route::post('/list', [LevelController::class, 'list'])->name('level.list'); // menampilkan data level dalam bentuk json untuk datatables
            Route::get('/create', [LevelController::class, 'create']);   // menampilkan halaman form tambah level
            Route::post('/', [LevelController::class, 'store']);         // menyimpan data level baru
            Route::get('/create_ajax', [LevelController::class, 'create_ajax']); // menampilkan halaman form tambah level (ajax)
            Route::post('/ajax', [LevelController::class, 'store_ajax']);        // menyimpan data level baru (ajax)
            Route::get('/{id}', [LevelController::class, 'show']);       // menampilkan detail level
            Route::get('/{id}/show_ajax', [LevelController::class, 'show_ajax']); // menampilkan detail level (ajax)
            Route::get('/{id}/edit', [LevelController::class, 'edit']);  // menampilkan halaman form edit level
            Route::get('/{id}/edit_ajax', [LevelController::class, 'edit_ajax']); // menampilkan halaman form edit level (ajax)
            Route::put('/{id}/update_ajax', [LevelController::class, 'update_ajax']); // menyimpan perubahan data level (ajax)
            Route::put('/{id}', [LevelController::class, 'update']);     // menyimpan perubahan data level
            Route::delete('/{id}', [LevelController::class, 'destroy']); // menghapus data level
            Route::get('/{id}/delete_ajax', [LevelController::class, 'confirm_ajax']); // menampilkan konfirmasi hapus level (ajax)
            Route::delete('/{id}/delete_ajax', [LevelController::class, 'delete_ajax']); // menghapus data level (ajax)
            Route::get('/import', [LevelController::class, 'import']); // menampilkan halaman form upload excel level ajax
            Route::post('/import_ajax', [LevelController::class, 'import_ajax']); // menyimpan import excel level ajax
            Route::get('/export_excel', [LevelController::class,'export_excel']); //export excel
            Route::get('/export_pdf', [LevelController::class,'export_pdf']); //export pdf
        });
    });

    // artinya semua route di dalam group ini harus punya role ADM (Administrator) dan MNG (Manager)
    Route::middleware(['authorize:ADM,MNG,STF'])->group(function () {
        Route::group(['prefix' => 'barang'], function () {
            Route::get('/', [BarangController::class, 'index']);                  // menampilkan halaman awal barang
            Route::post('/list', [BarangController::class, 'list'])->name('barang.list'); // menampilkan data barang dalam bentuk json untuk datatables
            Route::get('/create', [BarangController::class, 'create']);           // menampilkan halaman form tambah barang
            Route::post('/', [BarangController::class, 'store']);                 // menyimpan data barang baru
            Route::get('/create_ajax', [BarangController::class, 'create_ajax']); // menampilkan halaman form tambah barang (ajax)
            Route::post('/ajax', [BarangController::class, 'store_ajax']);        // menyimpan data barang baru (ajax)
            Route::get('/{id}', [BarangController::class, 'show']);               // menampilkan detail barang
            Route::get('/{id}/show_ajax', [BarangController::class, 'show_ajax']); // menampilkan detail barang (ajax)
            Route::get('/{id}/edit', [BarangController::class, 'edit']);          // menampilkan halaman form edit barang
            Route::put('/{id}', [BarangController::class, 'update']);             // menyimpan perubahan data barang
            Route::get('/{id}/edit_ajax', [BarangController::class, 'edit_ajax']); // menampilkan halaman form edit barang (ajax)
            Route::put('/{id}/update_ajax', [BarangController::class, 'update_ajax']); // menyimpan perubahan data barang (ajax)
            Route::get('/{id}/delete_ajax', [BarangController::class, 'confirm_ajax']); // menampilkan konfirmasi hapus barang (ajax)
            Route::delete('/{id}/delete_ajax', [BarangController::class, 'delete_ajax']); // menghapus data barang (ajax)
            Route::delete('/{id}', [BarangController::class, 'destroy']);         // menghapus data barang
            Route::get('/import', [BarangController::class, 'import']); // menampilkan halaman form upload excel barang ajax
            Route::post('/import_ajax', [BarangController::class, 'import_ajax']); // menyimpan import excel barang ajax
            Route::get('/export_excel', [BarangController::class,'export_excel']); //export excel
            Route::get('/export_pdf', [BarangController::class,'export_pdf']); //export pdf
        });
    });

    Route::group(['prefix' => 'user'], function () {
        Route::middleware(['authorize:ADM'])->group(function () {               // artinya semua route di dalam group ini harus ADM
            Route::get('/', [UserController::class, 'index']);          // menampilkan halaman awal user
            Route::post('/list', [UserController::class, 'list'])->name('user.list');      // menampilkan data user dalam bentuk json untuk datatables
            Route::get('/create', [UserController::class, 'create']);   // menampilkan halaman form tambah user
            Route::post('/', [UserController::class, 'store']);         // menyimpan data user baru
            Route::get('/create_ajax', [UserController::class, 'create_ajax']); // menampilkan halaman form tambah user (ajax)
            Route::post('/ajax', [UserController::class, 'store_ajax']);        // menyimpan data user baru (ajax)
            Route::get('/{id}', [UserController::class, 'show']);       // menampilkan detail user
            Route::get('/{id}/show_ajax', [UserController::class, 'show_ajax']); // menampilkan detail user (ajax)
            Route::get('/{id}/edit', [UserController::class, 'edit']);  // menampilkan halaman form edit user
            Route::put('/{id}', [UserController::class, 'update']);     // menyimpan perubahan data user
            Route::get('/{id}/edit_ajax', [UserController::class, 'edit_ajax']); // menampilkan halaman form edit user (ajax)
            Route::put('/{id}/update_ajax', [UserController::class, 'update_ajax']); // menyimpan perubahan data user (ajax)
            Route::delete('/{id}', [UserController::class, 'destroy']); // menghapus data user
            Route::get('/{id}/delete_ajax', [UserController::class, 'confirm_ajax']); // menampilkan konfirmasi hapus user (ajax)
            Route::delete('/{id}/delete_ajax', [UserController::class, 'delete_ajax']); // menghapus data user (ajax)
            Route::get('/import', [UserController::class, 'import']); // menampilkan halaman form upload excel user ajax
            Route::post('/import_ajax', [UserController::class, 'import_ajax']); // menyimpan import excel user ajax
            Route::get('/export_excel', [UserController::class,'export_excel']); //export excel
            Route::get('/export_pdf', [UserController::class,'export_pdf']); //export pdf
        });
    });

    Route::group(['prefix' => 'kategori'], function () {
        Route::middleware(['authorize:ADM,MNG'])->group(function () { // artinya semua route di dalam group ini harus ADM atau MNG
            Route::get('/', [KategoriController::class, 'index']);          // menampilkan halaman awal kategori
            Route::post('/list', [KategoriController::class, 'list']);      // menampilkan data kategori dalam bentuk json untuk datatables
            Route::get('/create', [KategoriController::class, 'create']);   // menampilkan halaman form tambah kategori
            Route::post('/', [KategoriController::class, 'store']);         // menyimpan data kategori baru
            Route::get('/create_ajax', [KategoriController::class, 'create_ajax']); // menampilkan halaman form tambah kategori (ajax)
            Route::post('/ajax', [KategoriController::class, 'store_ajax']);        // menyimpan data kategori baru (ajax)
            Route::get('/{id}', [KategoriController::class, 'show']);       // menampilkan detail kategori
            Route::get('/{id}/show_ajax', [KategoriController::class, 'show_ajax']); // menampilkan detail kategori (ajax)
            Route::get('/{id}/edit', [KategoriController::class, 'edit']);  // menampilkan halaman form edit kategori
            Route::put('/{id}', [KategoriController::class, 'update']);     // menyimpan perubahan data kategori
            Route::get('/{id}/edit_ajax', [KategoriController::class, 'edit_ajax']); // menampilkan halaman form edit kategori (ajax)
            Route::put('/{id}/update_ajax', [KategoriController::class, 'update_ajax']); // menyimpan perubahan data kategori (ajax)
            Route::delete('/{id}', [KategoriController::class, 'destroy']); // menghapus data kategori
            Route::get('/{id}/delete_ajax', [KategoriController::class, 'confirm_ajax']); // menampilkan konfirmasi hapus kategori (ajax)
            Route::delete('/{id}/delete_ajax', [KategoriController::class, 'delete_ajax']); // menghapus data kategori (ajax)
            Route::get('/import', [KategoriController::class, 'import']); // menampilkan halaman form upload excel kategori ajax
            Route::post('/import_ajax', [KategoriController::class, 'import_ajax']); // menyimpan import excel kategori ajax
            Route::get('/export_excel', [KategoriController::class,'export_excel']); //export excel
            Route::get('/export_pdf', [KategoriController::class,'export_pdf']); //export pdf
        });
    });

    Route::group(['prefix' => 'supplier'], function () {
        Route::middleware(['authorize:ADM,MNG'])->group(function () { // artinya semua route di dalam group ini harus ADM atau MNG
            Route::get('/', [SupplierController::class, 'index']);          // menampilkan halaman awal supplier
            Route::post('/list', [SupplierController::class, 'list'])->name('supplier.list');    // menampilkan data supplier dalam bentuk json untuk datatables
            Route::get('/create', [SupplierController::class, 'create']);   // menampilkan halaman form tambah supplier
            Route::post('/', [SupplierController::class, 'store']);         // menyimpan data supplier baru
            Route::get('/create_ajax', [SupplierController::class, 'create_ajax']); // menampilkan halaman form tambah supplier (ajax)
            Route::post('/ajax', [SupplierController::class, 'store_ajax']);        // menyimpan data supplier baru (ajax)
            Route::get('/{id}', [SupplierController::class, 'show']);       // menampilkan detail supplier
            Route::get('/{id}/show_ajax', [SupplierController::class, 'show_ajax']); // menampilkan detail supplier (ajax)
            Route::get('/{id}/edit', [SupplierController::class, 'edit']);  // menampilkan halaman form edit supplier
            Route::put('/{id}', [SupplierController::class, 'update']);     // menyimpan perubahan data supplier
            Route::get('/{id}/edit_ajax', [SupplierController::class, 'edit_ajax']); // menampilkan halaman form edit supplier (ajax)
            Route::put('/{id}/update_ajax', [SupplierController::class, 'update_ajax']); // menyimpan perubahan data supplier (ajax)
            Route::delete('/{id}', [SupplierController::class, 'destroy']); // menghapus data supplier
            Route::get('/{id}/delete_ajax', [SupplierController::class, 'confirm_ajax']); // menampilkan konfirmasi hapus supplier (ajax)
            Route::delete('/{id}/delete_ajax', [SupplierController::class, 'delete_ajax']); // menghapus data supplier (ajax)
            Route::get('/import', [SupplierController::class, 'import']); // menampilkan halaman form upload excel supplier ajax
            Route::post('/import_ajax', [SupplierController::class, 'import_ajax']); // menyimpan import excel supplier ajax
            Route::get('/export_excel', [SupplierController::class,'export_excel']); //export excel
            Route::get('/export_pdf', [SupplierController::class,'export_pdf']); //export pdf
        });
    });

    Route::group(['prefix' => 'stok'], function () {
        Route::middleware(['authorize:ADM,MNG,STF'])->group(function () {
            Route::get('/', [StokController::class, 'index']); // halaman awal stok
            Route::post('/list', [StokController::class, 'list'])->name('stok.list'); // data untuk datatables (json)
            Route::get('/create_ajax', [StokController::class, 'create_ajax']);
            Route::post('/ajax', [StokController::class, 'store_ajax']);
            Route::get('/{id}/show_ajax', [StokController::class, 'show_ajax'])->name('stok.show_ajax');
            Route::get('/{id}/edit', [StokController::class, 'edit']);
            Route::get('/{id}/edit_ajax', [StokController::class, 'edit_ajax'])->name('stok.edit_ajax');
            Route::put('/{id}/update_ajax', [StokController::class, 'update_ajax']);
            Route::get('/{id}/delete_ajax', [StokController::class, 'confirm_ajax'])->name('stok.confirm_ajax');
            Route::delete('/{id}/delete_ajax', [StokController::class, 'delete_ajax']);
            Route::get('/import', [StokController::class, 'import']);
            Route::post('/import_ajax', [StokController::class, 'import_ajax']);
            Route::get('/export_excel', [StokController::class, 'export_excel']);
            Route::get('/export_pdf', [StokController::class, 'export_pdf']);
        });
    });

    Route::group(['prefix' => 'penjualan', 'middleware' => ['authorize:ADM,MNG,STF']], function () {
        Route::get('/', [PenjualanController::class, 'index']); // menampilkan halaman awal penjualan
        Route::post('/list', [PenjualanController::class, 'list']); // menampilkan data penjualan dalam bentuk json untuk datatables
        Route::get('/create', [PenjualanController::class, 'create']); // menampilkan halaman form tambah penjualan
        Route::get('/create_ajax', [PenjualanController::class, 'create_ajax']); // menampilkan halaman form tambah penjualan ajax
        Route::post('/ajax', [PenjualanController::class, 'store_ajax']); // menyimpan data penjualan baru ajax
        Route::get('/{id}/show_ajax', [PenjualanController::class,'show_ajax']); // menampilkan detail penjualan ajax
        Route::get('/{id}/edit_ajax', [PenjualanController::class, 'edit_ajax']); // menampilkan halaman form edit penjualan ajax
        Route::put('/{id}/update_ajax', [PenjualanController::class, 'update_ajax']); // menyimpan perubahan data penjualan ajax
        Route::get('/{id}/delete_ajax', [PenjualanController::class, 'confirm_ajax']); // menampilkan form konfirmasi delete penjualan ajax
        Route::delete('/{id}/delete_ajax', [PenjualanController::class, 'delete_ajax']); // menghapus data penjualan ajax
        Route::get('/import', [PenjualanController::class, 'import']); // menampilkan halaman form upload excel penjualan ajax
        Route::post('/import_ajax', [PenjualanController::class, 'import_ajax']); // menyimpan import excel penjualan ajax
        Route::get('/export_excel', [PenjualanController::class, 'export_excel']); // menampilkan halaman form export excel penjualan ajax
        Route::get('/export_pdf', [PenjualanController::class, 'export_pdf']); // menampilkan halaman form export pdf penjualan
    });
});