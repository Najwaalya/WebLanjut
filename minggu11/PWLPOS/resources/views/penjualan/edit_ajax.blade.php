@empty($penjualanDetail)
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data yang anda cari tidak ditemukan
                </div>
                <a href="{{ url('/penjualan') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/penjualan/' . $penjualanDetail->detail_id . '/update_ajax') }}" method="POST" id="form-edit">
        @csrf
        @method('PUT')
        <div id="modal-master" class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Penjualan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="penjualan_kode">Kode Penjualan</label>
                        <input value="{{ $penjualanDetail->penjualan->penjualan_kode }}" type="text" name="penjualan_kode" id="penjualan_kode" class="form-control" required>
                        <small class="text-danger error-text" id="error-penjualan_kode"></small>
                    </div>

                    <div class="form-group">
                        <label for="user_id">Nama Penjual</label>
                        <select name="user_id" class="form-control" required>
                            <option value="">- Pilih User -</option>
                            @foreach($user as $u)
                                <option value="{{ $u->user_id }}" {{ $penjualanDetail->penjualan->user_id == $u->user_id ? 'selected' : '' }}>
                                    {{ $u->nama }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-danger error-text" id="error-user_id"></small>
                    </div>

                    <div class="form-group">
                        <label for="penjualan_tanggal">Tanggal Penjualan</label>
                        <input value="{{ \Carbon\Carbon::parse($penjualanDetail->penjualan->penjualan_tanggal)->format('Y-m-d\TH:i') }}" type="datetime-local" name="penjualan_tanggal" id="penjualan_tanggal" class="form-control" required>
                        <small class="text-danger error-text" id="error-penjualan_tanggal"></small>
                    </div>

                    <div class="form-group">
                        <label for="pembeli">Nama Pembeli</label>
                        <input value="{{ $penjualanDetail->penjualan->pembeli }}" type="text" name="pembeli" id="pembeli" class="form-control" required>
                        <small class="text-danger error-text" id="error-pembeli"></small>
                    </div>

                    <div class="form-group">
                        <label for="barang_id">Nama Barang</label>
                        <select name="barang_id" id="barang_id" class="form-control" required>
                            <option value="">- Pilih Barang -</option>
                            @foreach($barang as $b)
                                <option value="{{ $b->barang_id }}" {{ $penjualanDetail->barang_id == $b->barang_id ? 'selected' : '' }}>
                                    {{ $b->nama_barang }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-danger error-text" id="error-barang_id"></small>
                    </div>

                    <div class="form-group">
                        <label for="jumlah">Jumlah</label>
                        <input value="{{ $penjualanDetail->jumlah }}" type="text" name="jumlah" id="jumlah" class="form-control" required>
                        <small class="text-danger error-text" id="error-jumlah"></small>
                    </div>

                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        $(document).ready(function () {
            $("#form-edit").validate({
                rules: {
                    user_id: { required: true },
                    pembeli: { required: true, minlength: 3 },
                    penjualan_kode: { required: true, minlength: 3 },
                    penjualan_tanggal: { required: true },
                    barang_id: { required: true },
                    jumlah: { required: true, number: true, min: 1 }
                },
                
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
                                dataPenjualan.ajax.reload();
                            } else {
                                $('.error-text').text('');
                                $.each(response.msgField, function (prefix, val) {
                                    $('#error-' + prefix.replaceAll('[', '_').replaceAll(']', '')).text(val[0]);
                                });
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: response.message
                                });
                            }
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