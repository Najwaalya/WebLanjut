@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Daftar Detail Penjualan</h3>
        <div class="card-tools">
            <button onclick="modalAction('{{ url('/penjualan_detail/import') }}')" class="btn btn-info">
                <i class="fas fa-file-import"></i> Import Stok
            </button>
            <a href="{{ url('/penjualan_detail/export_excel') }}" class="btn btn-primary">
                <i class="fa fa-file-excel"></i> Export Excel
            </a>
            <a href="{{ url('/penjualan_detail/export_pdf') }}" class="btn btn-warning">
                <i class="fa fa-file-pdf"></i> Export PDF
            </a>
            <button onclick="modalAction('{{ url('/penjualan_detail/create_ajax') }}')" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Tambah Ajax
            </button>
        </div>
    </div>
    <div class="card-body">
        <div id="filter" class="form-horizontal filter-user p-2 border-bottom mb-2">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm row text-sm mb-0">
                        <label for="penjualan_id" class="col-md-2 col-form-label">Filter Kode Penjualan</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" id="penjualan_id" placeholder="Masukkan ID Penjualan">
                            <small class="form-text text-muted">Contoh: 1, 2, dst.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <table class="table table-bordered table-striped table-hover table-sm" id="table-detail-penjualan">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Kode Penjualan</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" aria-hidden="true"></div>
@endsection

@push('js')
<script>
    function modalAction(url = '') {
        $('#myModal').load(url, function () {
            $('#myModal').modal('show');
        });
    }

    var tableDetail;
    $(document).ready(function () {
    tableDetail = $('#table-detail-penjualan').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ url('penjualan_detail/list') }}", // Pastikan URL sudah benar
            type: "POST", // Metode POST yang benar
            dataType: "json",
            data: function (d) {
                d.penjualan_id = $('#penjualan_id').val(); // Mengirimkan filter
            },
            error: function(xhr, status, error) {
                console.log("AJAX Error:", status, error);  // Debugging error
                alert("AJAX request failed: " + error);  // Menampilkan alert untuk error AJAX
            }
        },

        columns: [
            {
                data: "DT_RowIndex",
                className: "text-center",
                width: "5%",
                orderable: false,
                searchable: false
            },
            {
                data: "nama_barang",
                className: "",
                width: "15%",
            },
            {
                data: "kode_penjualan",
                className: "",
                width: "20%",
                orderable: true,
                searchable: true
            },
            {
                data: "harga",
                className: "",
                width: "15%",
                orderable: true,
                searchable: true
            },
            {
                data: "jumlah",
                className: "",
                width: "10%",
                orderable: true,
                searchable: true
            },
            {
                data: "subtotal",
                className: "",
                width: "15%",
                orderable: true,
                searchable: true
            },
            {
                data: "aksi",
                className: "text-center",
                width: "20%",
                orderable: false,
                searchable: false
            }
        ]
    });

    // Filter untuk kode penjualan
    $('#penjualan_id').on('keyup change', function () {
        tableDetail.draw();
    });

    $('#table-detail-penjualan_filter input').unbind().bind().on('keyup', function (e) {
        if (e.keyCode == 13) {
            tableDetail.search(this.value).draw();
        }
    });
});
</script>
@endpush
