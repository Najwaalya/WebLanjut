<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReturPenjualanModel;
use App\Models\PenjualanModel;
use App\Models\BarangModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

class ReturPenjualanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Data Retur Penjualan',
            'list'  => ['Home', 'Retur Penjualan']
        ];

        $page = (object) [
            'title' => 'Daftar retur penjualan barang oleh pelanggan'
        ];

        $activeMenu = 'retur_penjualan';

        $penjualan = PenjualanModel::all();
        $barang = BarangModel::all();

        return view('retur_penjualan.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'penjualan' => $penjualan,
            'barang' => $barang
        ]);
    }

    public function list(Request $request)
    {
        $retur = ReturPenjualanModel::with(['penjualan', 'barang'])
            ->select('retur_id', 'penjualan_id', 'barang_id', 'jumlah', 'alasan', 'tanggal_retur');

        if ($request->penjualan_id) {
            $retur->where('penjualan_id', $request->penjualan_id);
        }

        return DataTables::of($retur)
            ->addIndexColumn()
            ->addColumn('penjualan_kode', function ($r) {
                return $r->penjualan->penjualan_kode ?? '-';
            })
            ->addColumn('barang_nama', function ($r) {
                return $r->barang->nama_barang ?? '-';  // Perbaikan nama kolom
            })
            ->addColumn('aksi', function ($r) {
                $btn = '<button onclick="modalAction(\'' . url('/retur_penjualan/' . $r->retur_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/retur_penjualan/' . $r->retur_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/retur_penjualan/' . $r->retur_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax()
    {
        $penjualan = PenjualanModel::select('penjualan_id', 'penjualan_kode')->get();
        $barang = BarangModel::select('barang_id', 'nama_barang')->get();  // Pastikan mengambil nama_barang

        return view('retur_penjualan.create_ajax', compact('penjualan', 'barang'));
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'penjualan_id'   => 'required|exists:t_penjualan,penjualan_id',
                'barang_id'      => 'required|exists:m_barang,barang_id',
                'jumlah'         => 'required|numeric|min:1',
                'alasan'         => 'required|min:3',
                'tanggal_retur'  => 'required|date',
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
                ReturPenjualanModel::create($request->only([
                    'penjualan_id', 'barang_id', 'jumlah', 'alasan', 'tanggal_retur'
                ]));

                return response()->json([
                    'status' => true,
                    'message' => 'Data retur penjualan berhasil disimpan'
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
        $retur = ReturPenjualanModel::find($id);
        return view('retur_penjualan.confirm_ajax', ['retur' => $retur]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $retur = ReturPenjualanModel::find($id);

                if ($retur) {
                    $retur->delete();

                    return response()->json([
                        'status' => true,
                        'message' => 'Data retur penjualan berhasil dihapus'
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data retur penjualan tidak ditemukan'
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Error deleting retur penjualan: ' . $e->getMessage());

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
        $retur = \App\Models\ReturPenjualanModel::with(['barang', 'penjualan'])->find($id);
        $barang = \App\Models\BarangModel::all();
        $penjualan = \App\Models\PenjualanModel::all();
    
        return view('retur_penjualan.edit_ajax', [
            'retur' => $retur,
            'barang' => $barang,
            'penjualan' => $penjualan
        ]);
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'penjualan_id'   => 'required|exists:t_penjualan,penjualan_id',
                'barang_id'      => 'required|exists:m_barang,barang_id',
                'jumlah'         => 'required|integer|min:1',
                'alasan'         => 'required|string|max:255',
                'tanggal_retur'  => 'required|date',
            ];
    
            $validator = Validator::make($request->all(), $rules);
    
            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }
    
            $retur = \App\Models\ReturPenjualanModel::find($id);
            if ($retur) {
                $retur->update($request->all());
    
                return response()->json([
                    'status'  => true,
                    'message' => 'Data retur penjualan berhasil diupdate'
                ]);
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data retur penjualan tidak ditemukan'
                ]);
            }
        }
    
        return redirect('/');
    }   
    
    public function show_ajax(string $id)
    {
        $retur = \App\Models\ReturPenjualanModel::with(['barang', 'penjualan'])->find($id);
    
        if (!$retur) {
            return response()->json(['status' => false, 'message' => 'Data retur penjualan tidak ditemukan.'], 404);
        }
    
        return view('retur_penjualan.show_ajax', ['retur' => $retur]);
    }

    public function import()
    {
        return view('retur_penjualan.import');
    }
    
    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_retur' => ['required', 'mimes:xlsx', 'max:1024']
            ];
    
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }
    
            $file = $request->file('file_retur');
    
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
                            'penjualan_id'   => $value['A'],
                            'barang_id'      => $value['B'],
                            'jumlah'         => (int)$value['C'],
                            'alasan'         => $value['D'],
                            'tanggal_retur'  => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['E'])->format('Y-m-d'),
                            'created_at'     => now(),
                            'updated_at'     => now(),
                        ];
                    }
                }
    
                if (count($insert) > 0) {
                    ReturPenjualanModel::insertOrIgnore($insert);
                }
    
                return response()->json([
                    'status' => true,
                    'message' => 'Data retur penjualan berhasil diimport'
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
        $retur = ReturPenjualanModel::select('penjualan_id', 'barang_id', 'jumlah', 'alasan', 'tanggal_retur')
                    ->orderBy('tanggal_retur')
                    ->with(['penjualan', 'barang']) // Relasi ke penjualan dan barang
                    ->get();
    
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Penjualan');
        $sheet->setCellValue('C1', 'Nama Barang');
        $sheet->setCellValue('D1', 'Jumlah');
        $sheet->setCellValue('E1', 'Alasan');
        $sheet->setCellValue('F1', 'Tanggal Retur');
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
    
        $no = 1;
        $baris = 2;
        foreach ($retur as $value) {
            $sheet->setCellValue('A' . $baris, $no++);
            $sheet->setCellValue('B' . $baris, $value->penjualan->penjualan_kode ?? '-');
            $sheet->setCellValue('C' . $baris, $value->barang->nama_barang ?? '-');
            $sheet->setCellValue('D' . $baris, $value->jumlah);
            $sheet->setCellValue('E' . $baris, $value->alasan);
            $sheet->setCellValue('F' . $baris, $value->tanggal_retur);
            $baris++;
        }
    
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    
        $sheet->setTitle('Data Retur Penjualan');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Retur_Penjualan_' . date('Y-m-d_H-i-s') . '.xlsx';
    
        // Set header
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');
    
        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        set_time_limit(300);
        $retur = ReturPenjualanModel::select('penjualan_id', 'barang_id', 'jumlah', 'alasan', 'tanggal_retur')
                    ->orderBy('tanggal_retur')
                    ->with(['penjualan', 'barang'])
                    ->get();
    
        $pdf = Pdf::loadView('retur_penjualan.export_pdf', ['retur' => $retur]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption("isRemoteEnabled", true);
    
        return $pdf->stream('Data_Retur_Penjualan_' . date('Y-m-d_H-i-s') . '.pdf');
    }    
    
}