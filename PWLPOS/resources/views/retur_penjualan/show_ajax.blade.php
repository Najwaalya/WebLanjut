@empty($retur)
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data yang anda cari tidak ditemukan
                </div>
                <a href="{{ url('/retur_penjualan') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Retur Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered table-striped">
                    <tr>
                        <th class="text-right col-3">ID Retur :</th>
                        <td class="col-9">{{ $retur->retur_id }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Tanggal Retur :</th>
                        <td class="col-9">{{ $retur->tanggal_retur }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Kode Penjualan :</th>
                        <td class="col-9">{{ $retur->penjualan->penjualan_kode ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Nama Barang :</th>
                        <td class="col-9">{{ $retur->barang->nama_barang ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Jumlah :</th>
                        <td class="col-9">{{ $retur->jumlah }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Alasan :</th>
                        <td class="col-9">{{ $retur->alasan }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endempty
