@empty($retur)
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
                    Data yang anda cari tidak ditemukan
                </div>
                <a href="{{ url('/retur_penjualan') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/retur_penjualan/' . $retur->retur_id . '/delete_ajax') }}" method="POST" id="form-delete-retur">
        @csrf
        @method('DELETE')
        <div id="modal-master" class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Retur Penjualan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Konfirmasi !!!</h5>
                        Apakah Anda yakin ingin menghapus data retur penjualan berikut?
                    </div>
                    <table class="table table-sm table-bordered table-striped">
                        <tr>
                            <th class="text-right col-4">Kode Penjualan:</th>
                            <td class="col-8">{{ $retur->penjualan->penjualan_kode ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-right">Nama Barang:</th>
                            <td>{{ $retur->barang->nama_barang ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-right">Jumlah Retur:</th>
                            <td>{{ $retur->jumlah }}</td>
                        </tr>
                        <tr>
                            <th class="text-right">Alasan:</th>
                            <td>{{ $retur->alasan }}</td>
                        </tr>
                        <tr>
                            <th class="text-right">Tanggal Retur:</th>
                            <td>{{ $retur->tanggal_retur }}</td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </form>

    <script>
        $(document).ready(function () {
            $("#form-delete-retur").validate({
                rules: {},
                submitHandler: function (form) {
                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: $(form).serialize(),
                        success: function (response) {
                            if (response.status) {
                                $('#myModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                                tableReturPenjualan.ajax.reload(); // Ganti dengan ID datatable retur kamu
                            } else {
                                $('.error-text').text('');
                                $.each(response.msgField ?? {}, function (prefix, val) {
                                    $('#error-' + prefix).text(val[0]);
                                });
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: response.message
                                });
                            }
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Server Error',
                                text: 'Terjadi kesalahan pada server'
                            });
                        }
                    });
                    return false;
                },
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function (element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
@endempty
