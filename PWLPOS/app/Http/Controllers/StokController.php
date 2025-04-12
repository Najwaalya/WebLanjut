<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\BarangModel;
use App\Models\UserModel;
use App\Models\StokModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Http\Request;

class StokController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Stok Barang',
            'list'  => ['Home', 'Stok']
        ];
    
        $page = (object) [
            'title' => 'Daftar data stok barang'
        ];
    
        $activeMenu = 'stok';
    
        return view('stok.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }
    
    public function list(Request $request)
    {
        $stok = StokModel::with('barang:barang_id,nama_barang', 'user:user_id,nama')
            ->select('stok_id', 'barang_id', 'user_id', 'stok_tanggal', 'stok_jumlah');
    
        if ($request->has('barang_id') && !empty($request->barang_id)) {
            $stok->where('barang_id', $request->barang_id);
        }
    
        return DataTables::of($stok)
            ->addIndexColumn()
            ->editColumn('stok_tanggal', function ($s) {
                return date('d-m-Y', strtotime($s->stok_tanggal));
            })
            ->addColumn('barang_nama', function ($s) {
                return $s->barang->nama_barang ?? '-';
            })
            ->addColumn('user_nama', function ($s) {
                return $s->user->nama ?? '-';
            })
            ->addColumn('aksi', function ($s) {
                $btn = '<button onclick="modalAction(\'' . route('stok.show_ajax', $s->stok_id) . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . route('stok.edit_ajax', $s->stok_id) . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . route('stok.confirm_ajax', $s->stok_id) . '\')" class="btn btn-danger btn-sm">Hapus</button>';
                return $btn;
            })            
            ->rawColumns(['aksi'])
            ->make(true);
    }    
    
    public function create_ajax()
    {
        $barang = BarangModel::all(); 
        $user = UserModel::all();
    
        return view('stok.create_ajax', [
            'barang' => $barang,
            'user' => $user,
        ]);
    }
    
    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'barang_id' => 'required|exists:m_barang,barang_id',
                'user_id' => 'required|exists:m_user,user_id',
                'stok_tanggal' => 'required|date',
                'stok_jumlah' => 'required|integer|min:1',
            ];
    
            $validator = Validator::make($request->all(), $rules);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }
    
            StokModel::create([
                'barang_id'    => $request->barang_id,
                'user_id'      => $request->user_id,
                'stok_tanggal' => $request->stok_tanggal,
                'stok_jumlah'  => $request->stok_jumlah,
            ]);
    
            return response()->json([
                'status' => true,
                'message' => 'Data stok berhasil disimpan',
            ]);
        }
    
        return redirect('/');
    }

    public function show_ajax(string $id)
    {
        $stok = \App\Models\StokModel::with(['barang', 'user'])->find($id);
    
        if (!$stok) {
            return response()->json(['status' => false, 'message' => 'Data stok tidak ditemukan.'], 404);
        }
    
        return view('stok.show_ajax', ['stok' => $stok]);
    }

    public function edit_ajax(string $id)
    {
        $stok = \App\Models\StokModel::with(['barang', 'user'])->find($id);
        $barang = \App\Models\BarangModel::all();
        $user = \App\Models\UserModel::all();
    
        return view('stok.edit_ajax', [
            'stok' => $stok,
            'barang' => $barang,
            'user' => $user
        ]);
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'barang_id'     => 'required|exists:m_barang,barang_id',
                'user_id'       => 'required|exists:m_user,user_id',
                'stok_tanggal'  => 'required|date',
                'stok_jumlah'   => 'required|integer|min:1',
            ];
    
            $validator = Validator::make($request->all(), $rules);
    
            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }
    
            $stok = \App\Models\StokModel::find($id);
            if ($stok) {
                $stok->update($request->all());
    
                return response()->json([
                    'status'  => true,
                    'message' => 'Data stok berhasil diupdate'
                ]);
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data stok tidak ditemukan'
                ]);
            }
        }
    
        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        $stok = \App\Models\StokModel::with(['barang', 'user'])->find($id);
    
        return view('stok.confirm_ajax', ['stok' => $stok]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $stok = \App\Models\StokModel::find($id);
    
            if (!$stok) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
    
            try {
                $stok->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data stok berhasil dihapus'
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data gagal dihapus karena masih terdapat keterkaitan dengan tabel lain'
                ]);
            }
        }
    
        return redirect('/');
    }

    public function import()
    {
        return view('stok.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_stok' => ['required', 'mimes:xlsx', 'max:1024']
            ];
    
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }
    
            $file = $request->file('file_stok');
    
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, false, true, true);
    
            $insert = [];
            if (count($data) > 1) {
                foreach ($data as $row => $value) {
                    if ($row > 1) {
                        $insert[] = [
                            'barang_id'    => $value['A'], // Pastikan ini ID barang yang valid
                            'user_id'      => $value['B'], // ID user yang input
                            'stok_tanggal' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['C'])->format('Y-m-d H:i:s'),
                            'stok_jumlah'  => (int)$value['D'],
                            'created_at'   => now(),
                            'updated_at'   => now(),
                        ];
                    }
                }
    
                if (count($insert) > 0) {
                    \App\Models\StokModel::insertOrIgnore($insert);
                }
    
                return response()->json([
                    'status' => true,
                    'message' => 'Data stok berhasil diimport'
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
        $stok = \App\Models\StokModel::with(['barang', 'user'])
            ->orderBy('stok_tanggal')
            ->get();
    
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Barang');
        $sheet->setCellValue('C1', 'User Input');
        $sheet->setCellValue('D1', 'Tanggal Stok');
        $sheet->setCellValue('E1', 'Jumlah Stok');
    
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
    
        $no = 1;
        $baris = 2;
        foreach ($stok as $item) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $item->barang->nama_barang ?? '-');
            $sheet->setCellValue('C' . $baris, $item->user->nama ?? '-');
            $sheet->setCellValue('D' . $baris, $item->stok_tanggal);
            $sheet->setCellValue('E' . $baris, $item->stok_jumlah);
            $baris++;
            $no++;
        }
    
        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    
        $sheet->setTitle('Data Stok');
    
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Stok_' . date('Y-m-d_H-i-s') . '.xlsx';
    
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
    
        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        set_time_limit(300);
    
        $stok = \App\Models\StokModel::with(['barang', 'user'])
            ->orderBy('stok_tanggal')
            ->get();
    
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('stok.export_pdf', ['stok' => $stok]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption("isRemoteEnabled", true);
    
        return $pdf->stream('Data Stok_' . date('Y-m-d H:i:s') . '.pdf');
    }
    
}
