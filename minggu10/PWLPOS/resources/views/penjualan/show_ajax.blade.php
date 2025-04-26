@empty($penjualanDetail)
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
                    Data yang anda cari tidak ditemukan.
                </div>
                <a href="{{ url('/penjualan') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="25%">ID Detail Penjualan</th>
                        <td>{{ $penjualanDetail->detail_id }}</td>
                    </tr>
                    <tr>
                        <th width="25%">ID Penjualan</th>
                        <td>{{ $penjualanDetail->penjualan->penjualan_id }}</td>
                    </tr>
                    <tr>
                        <th>Nama Penjual</th>
                        <td>{{ $penjualanDetail->penjualan->user->nama }}</td>
                    </tr>
                    <tr>
                        <th>Kode Penjualan</th>
                        <td>{{ $penjualanDetail->penjualan->penjualan_kode }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Penjualan</th>
                        <td>{{ $penjualanDetail->penjualan->penjualan_tanggal }}</td>
                    </tr>
                    <tr>
                        <th>Nama Pembeli</th>
                        <td>{{ $penjualanDetail->penjualan->pembeli }}</td>
                    </tr>
                    <tr>
                        <th>Nama Barang</th>
                        <td>{{ $penjualanDetail->barang->nama_barang }}</td>
                    </tr>
                    <tr>
                        <th>Harga Barang</th>
                        <td>Rp {{ number_format($penjualanDetail->barang->harga_jual, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Jumlah Barang</th>
                        <td>{{ $penjualanDetail->jumlah }}</td>
                    </tr>
                    <tr>
                        <th>Total Harga</th>
                        <td>Rp {{ number_format($penjualanDetail->harga, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-primary">Tutup</button>
            </div>
        </div>
    </div>
@endempty