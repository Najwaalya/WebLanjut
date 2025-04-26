@empty($stok)
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data stok tidak ditemukan.
                </div>
                <a href="{{ url('/stok') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Data Stok</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered table-striped">
                    <tr>
                        <th class="text-right col-4">ID Stok:</th>
                        <td class="col-8">{{ $stok->stok_id }}</td>
                    </tr>
                    <tr>
                        <th class="text-right">Tanggal Stok:</th>
                        <td>{{ \Carbon\Carbon::parse($stok->stok_tanggal)->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <th class="text-right">Nama Barang:</th>
                        <td>{{ $stok->barang->nama_barang ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-right">Jumlah Stok:</th>
                        <td>{{ $stok->stok_jumlah }}</td>
                    </tr>
                    <tr>
                        <th class="text-right">User Input:</th>
                        <td>{{ $stok->user->nama ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endempty