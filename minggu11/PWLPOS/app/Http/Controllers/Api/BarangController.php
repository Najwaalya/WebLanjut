<?php
 
 namespace App\Http\Controllers\Api;
 
 use App\Http\Controllers\Controller;
 use App\Models\BarangModel;
 use Illuminate\Http\Request;
 use Illuminate\Support\Facades\Validator;
 
 
 class BarangController extends Controller
 {
     public function index()
     {
         return BarangModel::all();
     }
 
     public function store(Request $request)
     {
         //$barang = BarangModel::create($request->all());
         //return response()->json($barang, 201);

         // set validation 
         $validator = Validator::make($request->all(), [
            'kategori_id' => 'required|exists:m_kategori,kategori_id',
            'kode_barang' => 'required|unique:m_barang',
            'nama_barang' => 'required',
            'harga_beli' => 'required',
            'harga_jual' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // if validations fails 
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // save image file to storage
        $image = $request->file('image');
        $image->storeAs('public/barang', $image->hashName());
        
        // create barang 
        $barang = BarangModel::create([
            'kategori_id' => $request->kategori_id,
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'image' => $image->hashName(),
        ]);

        // return response JSON barang is created 
        if ($barang) {
            return response()->json([
                'success' => true,
                'barang' => $barang,
            ], 201);
        }

        // return JSON process insert failed 
        return response()->json([
            'success' => false,
        ], 409);
     }
 
     public function show(BarangModel $barang)
     {
         return BarangModel::find($barang);
     }
 
     public function update(Request $request, BarangModel $barang)
     {
         $barang->update($request->all());
         return BarangModel::find($barang);
     }
 
     public function destroy(BarangModel $barang)
     {
         $barang->delete();
         return response()->json([
             'success' => true,
             'message' => 'Data terhapus',
         ]);
     }
 }