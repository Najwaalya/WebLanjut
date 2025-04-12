@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Daftar Penjualan</h3>
        <div class="card-tools">
        <button onclick="modalAction('{{ url('/penjualan/import') }}')" class="btn btn-info"><i class="fas fa-file-import"></i> Import Penjualan</button>
            <a href="{{ url('/penjualan/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export Excel</a>
            <a href="{{ url('/penjualan/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i> Export PDF</a>
            <button onclick="modalAction('{{ url('penjualan/create_ajax') }}')" class="btn btn-success"><i class="fas fa-plus-circle"></i> Tambah Ajax</button>
        </div>
    </div>
    <div class="card-body">
        <div id="filter" class="form-horizontal filter-user p-2 border-bottom mb-2">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm row text-sm mb-0">
                        <label for="user_id" class="col-md-1 col-form-label">Filter</label>
                        <div class="col-md-3">
                            <select class="form-control form-control-sm filter_user" name="user_id" id="user_id">
                                <option value="">- Semua -</option>
                                @foreach($user as $item)
                                    <option value="{{ $item->user_id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Nama User</small>
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

        <table class="table table-bordered table-striped table-hover table-sm" id="table-penjualan">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama User</th>
                    <th>Pembeli</th>
                    <th>Kode Penjualan</th>
                    <th>Tanggal Penjualan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('js')
<script>
    function modalAction(url = '') {
        $('#myModal').load(url, function () {
            $('#myModal').modal('show');
        });
    }

    var tablePenjualan;
    $(document).ready(function () {
        tablePenjualan = $('#table-penjualan').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('penjualan/list') }}",
                type: "POST",
                dataType: "json",
                data: function (d) {
                    d.user_id = $('#user_id').val();
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
                    data: "user.nama",  // Update kolom pertama menjadi nama user
                    className: "",
                    width: "20%",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "pembeli",
                    className: "",
                    width: "20%",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "penjualan_kode",
                    className: "",
                    width: "20%",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "penjualan_tanggal",
                    className: "",
                    width: "20%",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "aksi",
                    className: "text-center",
                    width: "15%",
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $('.filter_user').change(function () {
            tablePenjualan.draw();
        });

        $('#table-penjualan_filter input').unbind().bind().on('keyup', function (e) {
            if (e.keyCode == 13) {
                tablePenjualan.search(this.value).draw();
            }
        });
    });
</script>
@endpush