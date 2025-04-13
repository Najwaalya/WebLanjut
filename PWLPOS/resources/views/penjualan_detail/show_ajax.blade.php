@if (empty($detail))
    <div id="modal-master" class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <strong>Data tidak ditemukan.</strong>
                </div>
            </div>
        </div>
    </div>
@else
    <div id="modal-master" class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered table-striped">
                    <tr>
                        <th class="text-right">ID Detail :</th>
                        <td>{{ $detail->detail_id }}</td>
                    </tr>
                    <tr>
                        <th class="text-right">Kode Penjualan :</th>
                        <td>{{ $detail->penjualan->penjualan_kode ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-right">Nama Barang :</th>
                        <td>{{ $detail->barang->nama_barang ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-right">Harga :</th>
                        <td>Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th class="text-right">Jumlah :</th>
                        <td>{{ $detail->jumlah }}</td>
                    </tr>
                    <tr>
                        <th class="text-right">Subtotal :</th>
                        <td>Rp {{ number_format($detail->harga * $detail->jumlah, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endif