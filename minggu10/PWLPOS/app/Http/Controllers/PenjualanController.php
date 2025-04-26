<?php

namespace App\Http\Controllers;

use App\Models\PenjualanModel;
use App\Models\DetailPenjualanModel;
use App\Models\BarangModel;
use App\Models\UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PenjualanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Penjualan',
            'list' => ['Home', 'Penjualan']
        ];

        $page = (object) [
            'title' => 'Daftar Penjualan yang terdaftar di sistem'
        ];

        $activeMenu = 'penjualan';

        $penjualan = PenjualanModel::with('user')->get(); // Ambil semua penjualan dengan relasi user
        $barang = BarangModel::all(); // ambil semua barang
        $user = UserModel::all(); // ambil semua user
        $penjualanDetail = DetailPenjualanModel::with('penjualan.user')->get();

        return view('penjualan.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'penjualan' => $penjualan,
            'barang' => $barang,
            'user' => $user,
            'penjualanDetail' => $penjualanDetail
        ]);
    }

    public function list(Request $request)
    {
        $penjualanDetail = DetailPenjualanModel::with('penjualan', 'penjualan.user', 'barang');

        if ($request->filter_barang) {
            $penjualanDetail->where('barang_id', $request->filter_barang);
        }

        if ($request->filter_kode_penjualan) {
            $penjualanDetail->where('penjualan_id', $request->filter_kode_penjualan);
        }

        return DataTables::of($penjualanDetail)
            ->addIndexColumn()
            ->addColumn('aksi', function ($penjualanDetail) {
                $btn = '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualanDetail->detail_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualanDetail->detail_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualanDetail->detail_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax()
    {
        $user = UserModel::select('user_id', 'nama')->get();
        $barang = BarangModel::select('barang_id', 'nama_barang', 'harga_jual')->get();
        $penjualan = PenjualanModel::select('penjualan_id', 'penjualan_kode', 'pembeli')->get();

        return view('penjualan.create_ajax', compact('user', 'barang', 'penjualan'));
    }

    public function store_ajax(Request $request)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->back();
        }
    
        $validator = Validator::make($request->all(), [
            'aksi' => 'required|in:baru,lama',
            'detail' => 'required|array|min:1',
            'detail.*.barang_id' => 'required|exists:m_barang,barang_id',
            'detail.*.jumlah' => 'required|integer|min:1',
        ]);
    
        // Validasi tambahan berdasarkan aksi
        $validator->after(function ($validator) use ($request) {
            if ($request->aksi === 'baru') {
                if (!$request->user_id || !$request->pembeli || !$request->penjualan_kode || !$request->penjualan_tanggal) {
                    $validator->errors()->add('penjualan', 'Semua field penjualan harus diisi jika membuat penjualan baru.');
                }
            } elseif ($request->aksi === 'lama' && !$request->penjualan_id) {
                $validator->errors()->add('penjualan_id', 'Pilih penjualan yang sudah ada.');
            }
        });
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'msgField' => $validator->errors(),
            ]);
        }
    
        try {
            DB::beginTransaction();
    
            // Jika aksi baru, buat penjualan
            if ($request->aksi === 'baru') {
                // Cek kode unik
                if (PenjualanModel::where('penjualan_kode', $request->penjualan_kode)->exists()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Kode penjualan sudah ada.',
                    ]);
                }
    
                $penjualan = PenjualanModel::create([
                    'user_id' => $request->user_id,
                    'pembeli' => $request->pembeli,
                    'penjualan_kode' => $request->penjualan_kode,
                    'penjualan_tanggal' => Carbon::parse($request->penjualan_tanggal)->format('Y-m-d H:i:s'),
                ]);
            } else {
                $penjualan = PenjualanModel::findOrFail($request->penjualan_id);
            }
    
            // Simpan detail
            foreach ($request->detail as $item) {
                $barang = BarangModel::findOrFail($item['barang_id']);
    
                DetailPenjualanModel::create([
                    'penjualan_id' => $penjualan->penjualan_id,
                    'barang_id' => $item['barang_id'],
                    'jumlah' => $item['jumlah'],
                    'harga' => $barang->harga_jual * $item['jumlah'],
                ]);
            }
    
            DB::commit();
    
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil disimpan',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage(),
            ]);
        }
    }

    public function edit_ajax($id)
    {
        $penjualanDetail = DetailPenjualanModel::findOrFail($id);
        $penjualan = PenjualanModel::all();
        $barang = BarangModel::select('barang_id', 'nama_barang')->get();
        $user = UserModel::select('user_id', 'nama')->get();

        return view('penjualan.edit_ajax', compact('penjualanDetail', 'penjualan', 'barang', 'user'));
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            // Validasi input
            $rules = [
                'user_id' => 'required|exists:m_user,user_id',
                'pembeli' => 'required|string|max:50',
                'penjualan_kode' => 'required|string|max:20|unique:t_penjualan,penjualan_kode,' . $id . ',penjualan_id',
                'penjualan_tanggal' => 'required|date',
                'barang_id' => 'required|exists:m_barang,barang_id',
                'jumlah' => 'required|integer|min:1',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            // Ambil detail penjualan
            $penjualanDetail = DetailPenjualanModel::findOrFail($id);
            $penjualan = PenjualanModel::findOrFail($penjualanDetail->penjualan_id);

            // Update penjualan
            $penjualan->update([
                'user_id' => $request->user_id,
                'pembeli' => $request->pembeli,
                'penjualan_kode' => $request->penjualan_kode,
                'penjualan_tanggal' => Carbon::parse($request->penjualan_tanggal)->format('Y-m-d H:i:s'),
            ]);

            // Ambil barang_id dan jumlah dari request
            $barang_id = $request->input('barang_id');
            $jumlah = $request->input('jumlah');

            // Ambil data barang dari DB untuk ambil harga_jual
            $barang = BarangModel::findOrFail($barang_id);

            // Update penjualan detail
            $penjualanDetail->update([
                'barang_id' => $barang_id,
                'jumlah' => $jumlah,
                'harga' => $barang->harga_jual * $jumlah,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data Transaksi Penjualan berhasil diperbarui',
            ]);
        }

        return redirect('/');
    }

    public function confirm_ajax($id)
    {
        $penjualanDetail = DetailPenjualanModel::find($id);

        // Pastikan penjualanDetail ditemukan
        if (!$penjualanDetail) {
            // Jika data tidak ditemukan, bisa mengembalikan response error atau mengarahkan ke halaman lain
            return redirect()->back()->with('error', 'Detail penjualan tidak ditemukan');
        }

        // Mengirim data penjualanDetail ke view
        return view('penjualan.confirm_ajax', ['penjualanDetail' => $penjualanDetail]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $penjualanDetail = DetailPenjualanModel::find($id);

            if ($penjualanDetail) {
                try {
                    $penjualanDetail->delete();
                    return response()->json([
                        'status' => true,
                        'message' => 'Data berhasil dihapus'
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data penjualan gagal dihapus karena ada relasi dengan tabel lain.'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }

        return redirect('/');
    }

    public function show_ajax(string $id)
    {
        $penjualanDetail = DetailPenjualanModel::with('penjualan.user', 'barang')->find($id);

        return view('penjualan.show_ajax', compact('penjualanDetail'));
    }

    public function import()
    {
        return view('penjualan.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                // Validasi file harus xls atau xlsx, max 1MB
                'file_penjualan' => ['required', 'mimes:xlsx', 'max:1024']
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }
            
            $file = $request->file('file_penjualan'); // ambil file dari request
            $reader = IOFactory::createReader('Xlsx'); // load reader file excel
            $reader->setReadDataOnly(true); // hanya membaca data
            $spreadsheet = $reader->load($file->getRealPath()); // load file excel
            $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif
            $data = $sheet->toArray(null, false, true, true); // ambil data excel
            
            $insertDetail = [];
            $existingPenjualan = [];

            if (count($data) > 1) { // jika data lebih dari 1 baris
                foreach ($data as $baris => $value) {
                    if ($baris > 1) { // baris ke 1 adalah header, maka lewati
                        $kode = $value['B'];

                        // Jika belum ada, simpan penjualan master-nya
                        if (!isset($existingPenjualan[$kode])) {
                            $penjualan = PenjualanModel::firstOrCreate(
                                ['penjualan_kode' => $kode],
                                [   'user_id' => $value['A'],
                                    'penjualan_tanggal' => $value['C'],
                                    'pembeli' => $value['D'],]
                            );
                            $existingPenjualan[$kode] = $penjualan->penjualan_id;
                        }
                        
                        $barang_id = $value['E'];
                        $jumlah = $value['F'];

                        // Ambil data barang dari DB untuk ambil harga_jual
                        $barang = BarangModel::findOrFail($barang_id);
                        
                        // Tambah ke detail penjualan
                        $insertDetail[] = [
                            'penjualan_id' => $existingPenjualan[$kode],
                            'barang_id' => $barang_id,
                            'jumlah' => $jumlah,
                            'harga' => $barang->harga_jual * $jumlah,
                            'created_at' => now(),
                        ];
                    }
                }

                // Insert detail penjualan, jika ada data yang valid
                if (count($insertDetail) > 0) {
                    DetailPenjualanModel::insert($insertDetail);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diimport'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada data yang diimport'
                ]);
            }
        }

        return redirect('/');
    }

    public function export_excel()
    {
        // ambil data stok yang akan di export
        $penjualanDetail = DetailPenjualanModel::select(
            'detail_id', 
            'penjualan_id',
            'barang_id',
            'jumlah',
            'harga'
            )
        ->orderBy('detail_id')
        ->with('penjualan', 'penjualan.user', 'barang') // pastikan relasi didefinisikan
        ->get();

        // load library excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif

        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Id Detail');
        $sheet->setCellValue('C1', 'Id Penjualan');
        $sheet->setCellValue('D1', 'Nama Penjual');
        $sheet->setCellValue('E1', 'Kode Penjualan');
        $sheet->setCellValue('F1', 'Tanggal Penjualan');
        $sheet->setCellValue('G1', 'Nama Pembeli');
        $sheet->setCellValue('H1', 'Nama Barang');
        $sheet->setCellValue('I1', 'Jumlah Barang');
        $sheet->setCellValue('J1', 'Total Harga');

        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
        $no = 1;
        $baris = 2;
        foreach ($penjualanDetail as $item) {
            $sheet->setCellValue('A' . $baris, $no++);
            $sheet->setCellValue('B' . $baris, $item->detail_id);
            $sheet->setCellValue('C' . $baris, $item->penjualan->penjualan_id);
            $sheet->setCellValue('D' . $baris, $item->penjualan->user->nama);
            $sheet->setCellValue('E' . $baris, $item->penjualan->penjualan_kode);
            $sheet->setCellValue('F' . $baris, $item->penjualan->penjualan_tanggal);
            $sheet->setCellValue('G' . $baris, $item->penjualan->pembeli);
            $sheet->setCellValue('H' . $baris, $item->barang->nama_barang);
            $sheet->setCellValue('I' . $baris, $item->jumlah);
            $sheet->setCellValue('J' . $baris, $item->harga);
            $baris++;
        }

        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Penjualan');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Penjualan_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        // Ambil data penjualan detail dengan relasi
        $penjualanDetail = DetailPenjualanModel::select(
            'detail_id', 
            'penjualan_id',
            'barang_id',
            'jumlah',
            'harga'
            )
        ->orderBy('detail_id')
        ->with('penjualan', 'penjualan.user', 'barang') // pastikan relasi didefinisikan
        ->get();

        $pdf = Pdf::loadView('penjualan.export_pdf', ['penjualan' => $penjualanDetail]);
        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();

        return $pdf->stream('Data Penjualan ' . date('Y-m-d H:i:s') . '.pdf');
    }
}