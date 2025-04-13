@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Daftar Retur Penjualan</h3>
        <div class="card-tools">
            <button onclick="modalAction('{{ url('/retur_penjualan/import') }}')" class="btn btn-info"><i class="fas fa-file-import"></i> Import Excel</button>
            <a href="{{ url('/retur_penjualan/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export Excel</a>
            <a href="{{ url('/retur_penjualan/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i> Export PDF</a>
            <button onclick="modalAction('{{ url('/retur_penjualan/create_ajax') }}')" class="btn btn-success"><i class="fas fa-plus-circle"></i> Tambah Ajax</button>
        </div>
    </div>
    <div class="card-body">
        <div id="filter" class="form-horizontal filter-user p-2 border-bottom mb-2">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm row text-sm mb-0">
                        <label for="penjualan_id" class="col-md-1 col-form-label">Filter</label>
                        <div class="col-md-3">
                            <select class="form-control form-control-sm filter_penjualan" name="penjualan_id" id="penjualan_id">
                                <option value="">- Semua -</option>
                                @foreach($penjualan as $item)
                                    <option value="{{ $item->penjualan_id }}">{{ $item->penjualan_kode }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Kode Penjualan</small>
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

        <table class="table table-bordered table-striped table-hover table-sm" id="table-retur">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Penjualan</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Alasan</th>
                    <th>Tanggal Retur</th>
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

    var tableRetur;
    $(document).ready(function () {
        tableRetur = $('#table-retur').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('retur_penjualan/list') }}",
                type: "POST",
                dataType: "json",
                data: function (d) {
                    d.penjualan_id = $('#penjualan_id').val();
                    d._token = '{{ csrf_token() }}';
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
                    data: "penjualan_kode",
                    className: "",
                    width: "15%",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "barang_nama",
                    className: "",
                    width: "15%",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "jumlah",
                    className: "text-center",
                    width: "10%",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "alasan",
                    className: "",
                    width: "20%",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "tanggal_retur",
                    className: "text-center",
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

        $('.filter_penjualan').change(function () {
            tableRetur.draw();
        });

        $('#table-retur_filter input').unbind().bind().on('keyup', function (e) {
            if (e.keyCode == 13) {
                tableRetur.search(this.value).draw();
            }
        });

        $('#myModal').on('hidden.bs.modal', function () {
            tableRetur.ajax.reload();
        });
    });
</script>
@endpush
