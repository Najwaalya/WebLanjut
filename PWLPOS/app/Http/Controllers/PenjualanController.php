<?php

namespace App\Http\Controllers;

use App\Models\PenjualanModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

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

        $user = UserModel::all();

        return view('penjualan.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'user' => $user]);
    }

    public function list(Request $request)
    {
        $penjualan = PenjualanModel::select('penjualan_id', 'user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal')->with('user');

        if ($request->user_id) {
            $penjualan->where('user_id', $request->user_id);
        }

        return DataTables::of($penjualan)
            ->addIndexColumn()
            ->addColumn('aksi', function ($penjualan) {
                $btn = '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function show($id)
    {
        $penjualan = PenjualanModel::with(['details.barang'])->findOrFail($id);

        $details = $penjualan->details->map(function ($item) {
            return [
                'nama_barang' => $item->barang->nama_barang ?? 'N/A',
                'harga' => $item->harga,
                'jumlah' => $item->jumlah,
                'subtotal' => $item->harga * $item->jumlah,
            ];
        });

        return response()->json(['details' => $details]);
    }

    public function create_ajax()
    {
        $user = UserModel::select('user_id', 'username')->get();
        return view('penjualan.create_ajax')->with('user', $user);
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'penjualan_kode' => 'required|min:3|unique:t_penjualan,penjualan_kode',
                'penjualan_tanggal' => 'required|date',
                'pembeli' => 'required|min:3',
                'user_id' => 'required|exists:m_user,user_id',
            ];
    
            $validator = Validator::make($request->all(), $rules);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }
    
            try {
                PenjualanModel::create([
                    'penjualan_kode' => $request->penjualan_kode,
                    'penjualan_tanggal' => $request->penjualan_tanggal,
                    'pembeli' => $request->pembeli,
                    'user_id' => $request->user_id,
                ]);
    
                return response()->json([
                    'status' => true,
                    'message' => 'Data penjualan berhasil disimpan'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Server Error: ' . $e->getMessage()
                ], 500);
            }
        }
    
        return response()->json([
            'status' => false,
            'message' => 'Invalid request type'
        ], 400);
    }

    public function confirm_ajax(string $id)
    {
        $penjualan = PenjualanModel::find($id);
        return view('penjualan.confirm_ajax', ['penjualan' => $penjualan]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $penjualan = PenjualanModel::find($id);
    
                if ($penjualan) {
                    $penjualan->delete();
    
                    return response()->json([
                        'status' => true,
                        'message' => 'Data penjualan berhasil dihapus'
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data penjualan tidak ditemukan'
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Error deleting penjualan: ' . $e->getMessage());
    
                if (str_contains($e->getMessage(), 'SQLSTATE[23000]')) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data tidak dapat dihapus karena masih terkait dengan data lain di sistem'
                    ]);
                }
    
                return response()->json([
                    'status' => false,
                    'message' => 'Server Error: ' . $e->getMessage()
                ], 500);
            }
        }
    
        return redirect('/');
    }    

    public function edit_ajax(string $id)
    {
        $penjualan = PenjualanModel::find($id);
        $user = UserModel::select('user_id', 'username')->get();
    
        return view('penjualan.edit_ajax', ['penjualan' => $penjualan, 'user' => $user]);
    }
    
    
    public function update_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'penjualan_kode' => 'required|min:3|unique:t_penjualan,penjualan_kode,' . $id . ',penjualan_id',
                'penjualan_tanggal' => 'required|date',
                'pembeli' => 'required|min:3',
                'user_id' => 'required|exists:m_user,user_id',
            ];
    
            $validator = Validator::make($request->all(), $rules);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }
    
            $penjualan = PenjualanModel::find($id);
            if ($penjualan) {
                $penjualan->update([
                    'penjualan_kode' => $request->penjualan_kode,
                    'penjualan_tanggal' => $request->penjualan_tanggal,
                    'pembeli' => $request->pembeli,
                    'user_id' => $request->user_id,
                ]);
    
                return response()->json([
                    'status' => true,
                    'message' => 'Data penjualan berhasil diperbarui'
                ]);
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
        $penjualan = PenjualanModel::find($id);
    
        return view('penjualan.show_ajax', ['penjualan' => $penjualan]);
    }
    
    public function import()
    {
        return view('penjualan.import');
    }
    
    
    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_penjualan' => ['required', 'mimes:xlsx', 'max:1024'] // Validasi file
            ];
    
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }
    
            $file = $request->file('file_penjualan');
    
            try {
                $reader = IOFactory::createReader('Xlsx');
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray(null, false, true, true); // Mengambil data Excel
    
                $insert = [];
                if (count($data) > 1) {
                    foreach ($data as $row => $value) {
                        if ($row > 1) {
                            // Ambil dan validasi tanggal
                            $tanggal = $value['D'];
                            if (!empty($tanggal)) {
                                if (is_numeric($tanggal)) {
                                    $tanggal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggal)->format('Y-m-d H:i:s');
                                } else {
                                    $tanggal = date('Y-m-d H:i:s', strtotime($tanggal));
                                }
                            } else {
                                $tanggal = now();
                            }
    
                            $insert[] = [
                                'user_id'            => (int) $value['A'], // Pastikan integer
                                'pembeli'            => $value['B'],
                                'penjualan_kode'     => $value['C'],
                                'penjualan_tanggal'  => $tanggal,
                                'created_at'         => now(),
                                'updated_at'         => now(),
                            ];
                        }
                    }
    
                    if (count($insert) > 0) {
                        PenjualanModel::insertOrIgnore($insert);
                    }
    
                    return response()->json([
                        'status' => true,
                        'message' => 'Data penjualan berhasil diimport'
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Tidak ada data yang diimport'
                    ]);
                }
    
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()
                ]);
            }
        }
    
        return redirect('/');
    }    
    
    public function export_excel()
    {
        // Ambil data penjualan yang akan diekspor
        $penjualan = PenjualanModel::select('user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal')
                    ->orderBy('penjualan_tanggal')
                    ->with('user') // Relasi untuk mendapatkan nama petugas
                    ->get();
    
        // Load library Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet(); // Ambil sheet yang aktif
    
        // Set header tabel
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Petugas');
        $sheet->setCellValue('C1', 'Pembeli');
        $sheet->setCellValue('D1', 'Kode Penjualan');
        $sheet->setCellValue('E1', 'Tanggal Penjualan');
    
        // Set style untuk header
        $sheet->getStyle('A1:E1')->getFont()->setBold(true); // Bold header
    
        // Isi data
        $no = 1; // Nomor data dimulai dari 1
        $baris = 2; // Baris data dimulai dari baris ke-2
        foreach ($penjualan as $value) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->user->nama); // Nama petugas
            $sheet->setCellValue('C' . $baris, $value->pembeli);
            $sheet->setCellValue('D' . $baris, $value->penjualan_kode);
            $sheet->setCellValue('E' . $baris, $value->penjualan_tanggal);
            $baris++;
            $no++;
        }
    
        // Set auto size untuk kolom
        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    
        // Set judul sheet
        $sheet->setTitle('Data Penjualan');
    
        // Menyimpan ke file Excel
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Penjualan_' . date('Y-m-d H:i:s') . '.xlsx';
    
        // Menyiapkan header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
    
        // Simpan file ke output untuk didownload
        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        set_time_limit(300);
        $penjualan = PenjualanModel::select('user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal')
                    ->orderBy('penjualan_tanggal')
                    ->with('user')
                    ->get();
    

        $pdf = Pdf::loadView('penjualan.export_pdf', ['penjualan' => $penjualan]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption("isRemoteEnabled", true);
    
        return $pdf->stream('Data Penjualan_' . date('Y-m-d H:i:s') . '.pdf');
    }
}
