<form id="form-create" action="{{ url('/penjualan/ajax') }}" method="POST">
    @csrf
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Transaksi Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label for="aksi">Aksi</label>
                    <select name="aksi" id="aksi" class="form-control">
                        <option value="baru">Buat Baru</option>
                        <option value="lama">Gunakan yang Sudah Ada</option>
                    </select>
                    <small class="text-danger error-text" id="error-aksi"></small>
                </div>

                <!-- Penjualan Baru -->
                <div id="form-penjualan-baru">
                    <div class="form-group">
                        <label>Nama Penjual</label>
                        <select name="user_id" class="form-control">
                            <option value="">- Pilih -</option>
                            @foreach($user as $u)
                                <option value="{{ $u->user_id }}">{{ $u->nama }}</option>
                            @endforeach
                        </select>
                        <small class="text-danger error-text" id="error-user_id"></small>
                    </div>

                    <div class="form-group">
                        <label>Kode Penjualan</label>
                        <input type="text" name="penjualan_kode" class="form-control">
                        <small class="text-danger error-text" id="error-penjualan_kode"></small>
                    </div>

                    <div class="form-group">
                        <label>Tanggal Penjualan</label>
                        <input type="datetime-local" name="penjualan_tanggal" class="form-control">
                        <small class="text-danger error-text" id="error-penjualan_tanggal"></small>
                    </div>

                    <div class="form-group">
                        <label>Nama Pembeli</label>
                        <input type="text" name="pembeli" class="form-control">
                        <small class="text-danger error-text" id="error-pembeli"></small>
                    </div>
                </div>

                <!-- Penjualan Lama -->
                <div id="pilih-penjualan-lama" style="display: none;">
                    <div class="form-group">
                        <label>Pilih Penjualan</label>
                        <select name="penjualan_id" class="form-control">
                            <option value="">- Pilih -</option>
                            @foreach($penjualan as $p)
                                <option value="{{ $p->penjualan_id }}">{{ $p->penjualan_kode }} - {{ $p->pembeli }}</option>
                            @endforeach
                        </select>
                        <small class="text-danger error-text" id="error-penjualan_id"></small>
                    </div>
                </div>

                <hr>

                <div id="detail-wrapper">
                    <div class="detail-item row mb-2">
                        <div class="col-md-6">
                            <label>Barang</label>
                            <select name="detail[0][barang_id]" class="form-control">
                                <option value="">- Pilih -</option>
                                @foreach($barang as $b)
                                    <option value="{{ $b->barang_id }}">{{ $b->nama_barang }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Jumlah</label>
                            <input type="number" name="detail[0][jumlah]" class="form-control" value="1" min="1">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-remove-detail">Hapus</button>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-success mb-3" id="add-detail">+ Tambah Barang</button>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
        </div>
    </div>
</form>

<script>
    let detailIndex = 1;

    $('#aksi').on('change', function () {
        if ($(this).val() === 'lama') {
            $('#form-penjualan-baru').hide().find('input, select').prop('required', false);
            $('#pilih-penjualan-lama').show().find('select').prop('required', true);
        } else {
            $('#form-penjualan-baru').show().find('input, select').prop('required', true);
            $('#pilih-penjualan-lama').hide().find('select').prop('required', false);
        }
    }).trigger('change');

    $('#add-detail').on('click', function () {
        let newItem = `
        <div class="detail-item row mb-2">
            <div class="col-md-6">
                <label>Barang</label>
                <select name="detail[${detailIndex}][barang_id]" class="form-control">
                    <option value="">- Pilih -</option>
                    @foreach($barang as $b)
                        <option value="{{ $b->barang_id }}">{{ $b->nama_barang }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>Jumlah</label>
                <input type="number" name="detail[${detailIndex}][jumlah]" class="form-control" value="1" min="1">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-remove-detail">Hapus</button>
            </div>
        </div>`;
        $('#detail-wrapper').append(newItem);
        detailIndex++;
    });

    $('#detail-wrapper').on('click', '.btn-remove-detail', function () {
        $(this).closest('.detail-item').remove();
    });

    $('#form-create').on('submit', function (e) {
        e.preventDefault();
        let form = this;

        $.ajax({
            url: form.action,
            method: form.method,
            data: $(form).serialize(),
            success: function (res) {
                $('.error-text').text('');
                if (res.status) {
                    Swal.fire('Sukses', res.message, 'success');
                    $('#myModal').modal('hide');
                    dataPenjualan.ajax.reload();
                } else {
                    $.each(res.msgField, function (key, val) {
                        $('#error-' + key).text(val[0]);
                    });
                    Swal.fire('Gagal', res.message, 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'Server error', 'error');
            }
        });
    });
</script>