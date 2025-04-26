@empty($penjualanDetail)
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">Kesalahan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger mb-3">
                <h5><i class="fas fa-ban"></i> Data tidak ditemukan!</h5>
                Penjualan detail dengan ID tersebut tidak tersedia.
            </div>
            <div class="text-right">
                <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@else
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <form id="form-delete" method="POST" action="{{ url('/penjualan/' . $penjualanDetail->detail_id . '/delete_ajax') }}">
            @csrf
            @method('DELETE')

            <div class="modal-header bg-warning">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="alert alert-warning">
                    <h5><i class="fas fa-exclamation-triangle"></i> Yakin ingin menghapus data ini?</h5>
                </div>

                <table class="table table-bordered table-sm">
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(function () {
        $('#form-delete').on('submit', function(e) {
            e.preventDefault();
            let form = this;

            $.ajax({
                url: form.action,
                type: form.method,
                data: $(form).serialize(),
                success: function(res) {
                    if (res.status) {
                        $('#myModal').modal('hide');
                        Swal.fire('Berhasil', res.message, 'success');
                        dataPenjualan.ajax.reload();
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Terjadi kesalahan pada server.', 'error');
                }
            });
        });
    });
</script>
@endempty