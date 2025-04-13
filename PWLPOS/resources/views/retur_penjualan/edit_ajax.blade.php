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
    <form action="{{ url('/retur_penjualan/' . $retur->retur_id . '/update_ajax') }}" method="POST" id="form-edit">
        @csrf
        @method('PUT')
        <div id="modal-master" class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Retur Penjualan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tanggal Retur</label>
                        <input type="date" name="tanggal_retur" class="form-control" value="{{ $retur->tanggal_retur }}" required>
                        <small id="error-tanggal_retur" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Penjualan</label>
                        <select name="penjualan_id" class="form-control" required>
                            <option value="">- Pilih Penjualan -</option>
                            @foreach($penjualan as $p)
                                <option value="{{ $p->penjualan_id }}" {{ $retur->penjualan_id == $p->penjualan_id ? 'selected' : '' }}>
                                    {{ $p->penjualan_kode }}
                                </option>
                            @endforeach
                        </select>
                        <small id="error-penjualan_id" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Barang</label>
                        <select name="barang_id" class="form-control" required>
                            <option value="">- Pilih Barang -</option>
                            @foreach($barang as $b)
                                <option value="{{ $b->barang_id }}" {{ $retur->barang_id == $b->barang_id ? 'selected' : '' }}>
                                    {{ $b->nama_barang }}
                                </option>
                            @endforeach
                        </select>
                        <small id="error-barang_id" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Jumlah</label>
                        <input type="number" name="jumlah" class="form-control" value="{{ $retur->jumlah }}" required min="1">
                        <small id="error-jumlah" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Alasan Retur</label>
                        <input type="text" name="alasan" class="form-control" value="{{ $retur->alasan }}" required>
                        <small id="error-alasan" class="error-text form-text text-danger"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </form>

    <script>
        $(document).ready(function () {
            $("#form-edit").validate({
                rules: {
                    tanggal_retur: { required: true, date: true },
                    penjualan_id: { required: true },
                    barang_id: { required: true },
                    jumlah: { required: true, number: true, min: 1 },
                    alasan: { required: true, minlength: 3 }
                },
                submitHandler: function (form) {
                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: $(form).serialize(),
                        success: function (response) {
                            if (response.status) {
                                $('#modal-master').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                                dataReturPenjualan.ajax.reload(); // Ganti sesuai nama variabel datatable kamu
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
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Server Error',
                                text: 'Terjadi kesalahan saat menghubungi server.'
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
