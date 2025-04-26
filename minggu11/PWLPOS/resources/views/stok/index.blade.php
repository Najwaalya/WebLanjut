@extends('layouts.template')
 
@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title ?? 'Daftar Stok Barang' }}</h3>
        <div class="card-tools">
            <button onclick="modalAction('{{ url('/stok/import') }}')" class="btn btn-info">
                <i class="fas fa-file-import"></i> Import Stok
            </button>
            <a href="{{ url('/stok/export_excel') }}" class="btn btn-primary">
                <i class="fa fa-file-excel"></i> Export Excel
            </a>
            <a href="{{ url('/stok/export_pdf') }}" class="btn btn-warning">
                <i class="fa fa-file-pdf"></i> Export PDF
            </a>
            <button onclick="modalAction('{{ url('/stok/create_ajax') }}')" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Tambah Ajax
            </button>
        </div>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <table class="table table-bordered table-striped table-hover table-sm" id="table_stok">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>User Input</th>
                    <th>Tanggal Stok</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
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

    var dataStok;
    $(document).ready(function () {
        dataStok = $('#table_stok').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('stok/list') }}",
                type: "POST",
                dataType: "json",
                data: function (d) {
                    d._token = "{{ csrf_token() }}";
                }
            },
            columns: [
                { data: "DT_RowIndex", className: "text-center", width: "5%", orderable: false, searchable: false },
                { data: "barang_nama", width: "20%", orderable: true, searchable: true },
                { data: "user_nama", width: "20%", orderable: true, searchable: true },
                { data: "stok_tanggal", width: "15%", className: "text-center", orderable: true },
                { data: "stok_jumlah", width: "10%", className: "text-center", orderable: true },
                { data: "aksi", className: "text-center", width: "15%", orderable: false, searchable: false }
            ]
        });

        $('#table_stok_filter input').unbind().bind().on('keyup', function (e) {
            if (e.keyCode == 13) {
                dataStok.search(this.value).draw();
            }
        });

        $('#myModal').on('hidden.bs.modal', function () {
            dataStok.ajax.reload();
        });
    });
</script>
@endpush
