<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\KategoriModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = KategoriModel::all();

        $breadcrumb = (object) [
            'title' => 'Daftar Kategori',
            'list'  => ['Home', 'Kategori']
        ];

        $page = (object) [
            'title' => 'Daftar kategori yang terdaftar dalam sistem'
        ];

        $activeMenu = 'kategori';

        return view('kategori.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }

    public function list(Request $request)
    {
        $kategori = KategoriModel::select(
            'kategori_id',
            'kategori_kode',
            'kategori_nama'
        );

        $kategori_id = $request->input('kategori_id');
        if (!empty($kategori_id)) {
            $kategori->where('kategori_id', $kategori_id);
        }

        return DataTables::of($kategori)
            ->addIndexColumn()
            ->addColumn('aksi', function ($kategori) {
                $btn = '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
 
     public function create()
     {
         $breadcrumb = (object) [
             'title' => 'Tambah Kategori',
             'list' => ['Home', 'Kategori', 'Tambah']
         ];
 
         $page = (object) [
             'title' => 'Tambah kategori baru'
         ];
 
         $activeMenu = 'kategori';
 
         return view('kategori.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
     }
 
     public function store(Request $request)
     {
         $request->validate([
             'kategori_kode' => 'required|string|min:3|unique:m_kategori,kategori_kode',
             'kategori_nama' => 'required|string|max:100'
         ]);
 
         KategoriModel::create([
             'kategori_kode' => $request->kategori_kode,
             'kategori_nama' => $request->kategori_nama
         ]);
 
         return redirect('/kategori')->with('success', 'Data kategori berhasil disimpan');
     }

     public function create_ajax()
    {
        $kategori = KategoriModel::all();

        return view('kategori.create_ajax', ['kategori' => $kategori]);
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kategori_kode' => 'required|string|max:6|regex:/^[A-Z0-9]+$/',
                'kategori_nama' => 'required|string|min:3|max:50|regex:/^[a-zA-Z\s]+$/',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            KategoriModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data Kategori berhasil disimpan'
            ]);
        }
        redirect('/');
    }
 
     public function show(string $id)
     {
         $kategori = KategoriModel::findOrFail($id);
 
         $breadcrumb = (object) [
             'title' => 'Detail Kategori',
             'list'  => ['Home', 'Kategori', 'Detail']
         ];
 
         $page = (object) [
             'title' => 'Detail kategori'
         ];
 
         $activeMenu = 'kategori';
 
         return view('kategori.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
     }

     public function show_ajax(string $id)
     {
         $kategori = KategoriModel::find($id);
         return view('kategori.show_ajax', ['kategori' => $kategori]);
     }  
 
     public function edit(string $id)
     {
         $kategori = KategoriModel::findOrFail($id);
 
         $breadcrumb = (object) [
             'title' => 'Edit Kategori',
             'list'  => ['Home', 'Kategori', 'Edit']
         ];
 
         $page = (object) [
             'title' => 'Edit kategori'
         ];
 
         $activeMenu = 'kategori';
 
         return view('kategori.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
     }

     public function edit_ajax($id)
     {
         $kategori = KategoriModel::find($id);
 
         return view('kategori.edit_ajax', ['kategori' => $kategori]);
     }
 
     public function update(Request $request, string $id)
     {
         $request->validate([
             'kategori_kode' => 'required|string|min:3|unique:m_kategori,kategori_kode,' . $id . ',kategori_id',
             'kategori_nama' => 'required|string|max:100'
         ]);
 
         $kategori = KategoriModel::findOrFail($id);
 
         $kategori->update([
             'kategori_kode' => $request->kategori_kode,
             'kategori_nama' => $request->kategori_nama
         ]);
 
         return redirect('/kategori')->with('success', 'Data kategori berhasil diubah');
     }

     public function update_ajax(Request $request, $id)
     {
         if ($request->ajax() || $request->wantsJson()) {
             $rules = [
                 'kategori_kode' => 'required|unique:m_kategori,kategori_kode,' . $id . ',kategori_id',
                 'kategori_nama' => 'required'
             ];
 
             $validator = Validator::make($request->all(), $rules);
 
             if ($validator->fails()) {
                 return response()->json([
                     'status' => false,
                     'message' => 'Validasi Gagal',
                     'msgField' => $validator->errors(),
                 ]);
             }
 
             KategoriModel::find($id)->update($request->all());
             return response()->json([
                 'status' => true,
                 'message' => 'Data user berhasil diubah'
             ]);
         }
         redirect('/');
     }
     
     public function destroy(string $id)
     {
         $check = KategoriModel::find($id);
         if (!$check) {
             return redirect('/kategori')->with('error', 'Data kategori tidak ditemukan');
         }
 
         try {
             KategoriModel::destroy($id);
             return redirect('/kategori')->with('success', 'Data kategori berhasil dihapus');
         } catch (\Illuminate\Database\QueryException $e) {
             return redirect('/kategori')->with('error', 'Data kategori gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
         }
     }

     public function confirm_ajax(string $id)
    {
        $kategori = KategoriModel::find($id);

        return view('kategori.confirm_ajax', ['kategori' => $kategori]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $check = KategoriModel::find($id);
                if ($check) {
                    $check->delete();
                    return response()->json([
                        'status' => true,
                        'message' => 'Data berhasil dihapus'
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data tidak ditemukan'
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error deleting user: ' . $e->getMessage());
                if (str_contains($e->getMessage(), 'SQLSTATE[23000]')) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data tidak dapat dihapus karena masih terkait dengan data lain di sistem'
                    ]);
                }
            }
        }
        return redirect('/');
    }

    public function import() 
    { 
        return view('kategori.import');  // Ganti tampilan dengan tampilan kategori
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_kategori' => ['required', 'mimes:xlsx', 'max:1024']  // Ganti 'file_level' menjadi 'file_kategori'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $file = $request->file('file_kategori');  // Ganti 'file_level' menjadi 'file_kategori'

            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, false, true, true);

            $insert = [];
            if (count($data) > 1) {
                foreach ($data as $row => $value) {
                    if ($row > 1) {
                        // Pastikan kolom yang diimpor sesuai dengan kolom yang ada pada tabel m_kategori
                        $insert[] = [
                            'kategori_kode' => $value['A'],  // Pastikan kolom 'A' berisi kode kategori
                            'kategori_nama' => $value['B'],  // Pastikan kolom 'B' berisi nama kategori
                            'created_at' => now(),
                            'updated_at' => now()  // Menambahkan updated_at jika diperlukan
                        ];
                    }
                }

                if (count($insert) > 0) {
                    KategoriModel::insertOrIgnore($insert);  // Menggunakan insertOrIgnore agar data duplikat tidak ditambahkan
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data Kategori berhasil diimport'
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
        $kategori = KategoriModel::select('kategori_kode', 'kategori_nama')
                    ->orderBy('kategori_kode')
                    ->get();
    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Kategori');
        $sheet->setCellValue('C1', 'Nama Kategori');
    
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
    
        $no = 1;
        $baris = 2;
        foreach ($kategori as $item) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $item->kategori_kode);
            $sheet->setCellValue('C' . $baris, $item->kategori_nama);
            $baris++;
            $no++;
        }
    
        foreach (range('A', 'C') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    
        $sheet->setTitle('Data Kategori');
    
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Kategori_' . date('Y-m-d_H-i-s') . '.xlsx';
    
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
    
        $kategori = KategoriModel::select('kategori_kode', 'kategori_nama')
            ->orderBy('kategori_kode')
            ->get();
    
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('kategori.export_pdf', ['kategori' => $kategori]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption("isRemoteEnabled", true);
    
        return $pdf->stream('Data Kategori_' . date('Y-m-d H:i:s') . '.pdf');
    }
    
}