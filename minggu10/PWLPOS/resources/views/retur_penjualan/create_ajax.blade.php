<form action="{{ url('/retur_penjualan/ajax') }}" method="POST" id="form-tambah-retur">
    @csrf
    <div id="modal-retur" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Retur Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Penjualan</label>
                    <select name="penjualan_id" class="form-control" required>
                        <option value="">- Pilih Penjualan -</option>
                        @foreach($penjualan as $p)
                            <option value="{{ $p->penjualan_id }}">{{ $p->penjualan_kode }}</option>
                        @endforeach
                    </select>
                    <small id="error-penjualan_id" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Barang</label>
                    <select name="barang_id" class="form-control" required>
                        <option value="">- Pilih Barang -</option>
                        @foreach($barang as $b)
                            <option value="{{ $b->barang_id }}">{{ $b->nama_barang }}</option>
                        @endforeach
                    </select>
                    <small id="error-barang_id" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Jumlah Retur</label>
                    <input type="number" name="jumlah" class="form-control" required min="1">
                    <small id="error-jumlah" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Alasan</label>
                    <input type="text" name="alasan" class="form-control" required>
                    <small id="error-alasan" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Tanggal Retur</label>
                    <input type="date" name="tanggal_retur" class="form-control" required>
                    <small id="error-tanggal_retur" class="error-text form-text text-danger"></small>
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
        $("#form-tambah-retur").validate({
            rules: {
                penjualan_id: { required: true },
                barang_id: { required: true },
                jumlah: { required: true, min: 1 },
                alasan: { required: true, minlength: 3 },
                tanggal_retur: { required: true, date: true },
            },

            submitHandler: function (form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function (response) {
                        if (response.status) {
                            $('#modal-retur').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            if (typeof dataReturPenjualan !== 'undefined') {
                                dataReturPenjualan.ajax.reload();
                            }
                        } else {
                            $('.error-text').text('');
                            $.each(response.msgField, function (prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Server Error',
                            text: 'Terjadi kesalahan di server. Silakan cek console.'
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
