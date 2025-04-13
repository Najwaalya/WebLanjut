<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\DetailPenjualanModel;
use App\Models\PenjualanModel;
use App\Models\BarangModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

class DetailPenjualanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Detail Penjualan',
            'list' => ['Home', 'Penjualan', 'Detail']
        ];
    
        $page = (object) [
            'title' => 'Daftar detail penjualan barang'
        ];
    
        $activeMenu = 'penjualan_detail';
    
        return view('penjualan_detail.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list(Request $request)
    {
        // Menyaring data berdasarkan penjualan_id jika ada
        $detail = DetailPenjualanModel::with(['barang', 'penjualan'])
            ->select('detail_id', 'penjualan_id', 'barang_id', 'harga', 'jumlah');
    
        if ($request->filled('penjualan_id')) {
            $detail->where('penjualan_id', $request->penjualan_id);
        }
    
        return DataTables::of($detail)
            ->addIndexColumn()
            ->addColumn('nama_barang', function ($d) {
                return $d->barang->nama_barang ?? '-';
            })
            ->addColumn('kode_penjualan', function ($d) {
                return $d->penjualan->penjualan_kode ?? '-';
            })
            ->addColumn('subtotal', function ($d) {
                return number_format($d->harga * $d->jumlah);
            })
            ->addColumn('aksi', function ($d) {
                $btn = '<button onclick="modalAction(\'' . url('/penjualan_detail/' . $d->detail_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan_detail/' . $d->detail_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan_detail/' . $d->detail_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax()
    {
        $barang = BarangModel::select('barang_id', 'nama_barang')->get();
        $penjualan = PenjualanModel::select('penjualan_id', 'penjualan_kode')->get();
    
        return view('penjualan_detail.create_ajax', compact('barang', 'penjualan'));
    }
    
    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'penjualan_id' => 'required|exists:t_penjualan,penjualan_id',
                'barang_id' => 'required|exists:m_barang,barang_id',
                'harga' => 'required|numeric|min:0',
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
    
            try {
                DetailPenjualanModel::create([
                    'penjualan_id' => $request->penjualan_id,
                    'barang_id' => $request->barang_id,
                    'harga' => $request->harga,
                    'jumlah' => $request->jumlah,
                ]);
    
                return response()->json([
                    'status' => true,
                    'message' => 'Data detail penjualan berhasil disimpan'
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

    public function show_ajax(string $id)
    {
        $detail = DetailPenjualanModel::with(['barang', 'penjualan'])->find($id);
    
        return view('penjualan_detail.show_ajax', ['detail' => $detail]);
    }
    
    public function edit_ajax(string $id)
    {
        $detail = DetailPenjualanModel::with(['barang', 'penjualan'])->find($id);
        $barang = BarangModel::select('barang_id', 'nama_barang')->get();
        $penjualan = PenjualanModel::select('penjualan_id', 'penjualan_kode')->get();
    
        return view('penjualan_detail.edit_ajax', [
            'detail' => $detail,
            'barang' => $barang,
            'penjualan' => $penjualan,
        ]);
    }

    public function update_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'penjualan_id' => 'required|exists:t_penjualan,penjualan_id',
                'barang_id' => 'required|exists:m_barang,barang_id',
                'harga' => 'required|numeric|min:0',
                'jumlah' => 'required|integer|min:1',
            ];
    
            $validator = \Validator::make($request->all(), $rules);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }
    
            $detail = DetailPenjualanModel::find($id);
            if ($detail) {
                $detail->update([
                    'penjualan_id' => $request->penjualan_id,
                    'barang_id' => $request->barang_id,
                    'harga' => $request->harga,
                    'jumlah' => $request->jumlah,
                ]);
    
                return response()->json([
                    'status' => true,
                    'message' => 'Data detail penjualan berhasil diperbarui'
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
    
    public function confirm_ajax(string $id)
    {
        $detail = DetailPenjualanModel::with(['barang', 'penjualan'])->find($id);
        return view('penjualan_detail.confirm_ajax', ['detail' => $detail]);
    }
    
    public function delete_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $detail = DetailPenjualanModel::find($id);
    
                if ($detail) {
                    $detail->delete();
    
                    return response()->json([
                        'status' => true,
                        'message' => 'Data detail penjualan berhasil dihapus'
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data detail penjualan tidak ditemukan'
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Error deleting detail penjualan: ' . $e->getMessage());
    
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

    public function import()
    {
        return view('penjualan_detail.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_detail' => ['required', 'mimes:xlsx', 'max:1024']
            ];
    
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }
    
            $file = $request->file('file_detail');
    
            try {
                $reader = IOFactory::createReader('Xlsx');
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray(null, false, true, true); // hasil array asosiatif berdasarkan kolom A, B, C...
    
                $insert = [];
    
                if (count($data) > 1) {
                    foreach ($data as $row => $value) {
                        if ($row > 1) {
                            $insert[] = [
                                'penjualan_id' => $value['A'], // pastikan ini ID penjualan yang valid
                                'barang_id'    => $value['B'], // pastikan ini ID barang yang valid
                                'harga'        => (int) $value['C'],
                                'jumlah'       => (int) $value['D'],
                                'created_at'   => now(),
                                'updated_at'   => now(),
                            ];
                        }
                    }
    
                    if (count($insert) > 0) {
                        \App\Models\DetailPenjualanModel::insertOrIgnore($insert);
                    }
    
                    return response()->json([
                        'status' => true,
                        'message' => 'Data detail penjualan berhasil diimport'
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Tidak ada data yang diimport'
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error("Import detail penjualan gagal: " . $e->getMessage());
    
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat membaca file: ' . $e->getMessage()
                ]);
            }
        }
    
        return redirect('/');
    }
    
    public function export_excel()
    {
        $detail = DetailPenjualanModel::with(['barang', 'penjualan'])
            ->orderBy('penjualan_id')
            ->get();
    
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'ID Penjualan');
        $sheet->setCellValue('C1', 'Kode Barang');
        $sheet->setCellValue('D1', 'Nama Barang');
        $sheet->setCellValue('E1', 'Harga');
        $sheet->setCellValue('F1', 'Jumlah');
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
    
        $no = 1;
        $baris = 2;
        foreach ($detail as $item) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $item->penjualan_id);
            $sheet->setCellValue('C' . $baris, $item->barang->kode_barang ?? '-');
            $sheet->setCellValue('D' . $baris, $item->barang->nama_barang ?? '-');
            $sheet->setCellValue('E' . $baris, $item->harga);
            $sheet->setCellValue('F' . $baris, $item->jumlah);
            $no++;
            $baris++;
        }
    
        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    
        $sheet->setTitle('Detail Penjualan');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Detail_Penjualan_' . date('Y-m-d_H-i-s') . '.xlsx';
    
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
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
    
        $detail = DetailPenjualanModel::with(['barang', 'penjualan'])
            ->orderBy('penjualan_id')
            ->get();
    
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('penjualan_detail.export_pdf', ['detail' => $detail]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption("isRemoteEnabled", true);
    
        return $pdf->stream('Detail_Penjualan_' . date('Y-m-d_H-i-s') . '.pdf');
    }    


}
