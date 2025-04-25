@extends('layouts.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Penjualan</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('/penjualan/import') }}')" class="btn btn-info">Import Penjualan</button>
                <a href="{{ url('/penjualan/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export Penjualan (Excel)</a>
                <a href="{{ url('/penjualan/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i> Export Penjualan (PDF)</a>
                <button onclick="modalAction('{{ url('/penjualan/create_ajax') }}')" class="btn btn-success">Tambah Data Ajax</button>
            </div>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div id="filter" class="form-horizontal filter-date p-2 border-bottom mb-2">
                <div class="row align-items-center">
                    <label class="col-md-1 col-form-label">Filter</label>

                    <div class="col-md-3">
                        <select name="filter_kode_penjualan" class="form-control form-control-sm filter_kode_penjualan">
                            <option value="">- Semua -</option>
                            @foreach($penjualan as $p)
                                <option value="{{ $p->penjualan_id }}">{{ $p->penjualan_kode }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Kode Penjualan</small>
                    </div>
                    <div class="col-md-3">
                        <select name="filter_barang" class="form-control form-control-sm filter_barang">
                            <option value="">- Semua -</option>
                            @foreach($barang as $b)
                                <option value="{{ $b->barang_id }}">{{ $b->nama_barang }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Nama Barang</small>
                    </div>
                </div>
            </div>

            <table class="table table-bordered table-sm table-striped table-hover" id="table_penjualan">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">ID Detail Penjualan</th>
                        <th class="text-center">Kode Penjualan</th>
                        <th class="text-center">Tanggal Penjualan</th>
                        <th class="text-center">Nama Barang</th>
                        <th class="text-center">Jumlah Barang</th>
                        <th class="text-center">Total Harga</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div id="myModal" class="modal fade animate shake" tabindex="-1" data-backdrop="static" data-keyboard="false" data-width="75%"></div>
@endsection

@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function () {
                $('#myModal').modal('show');
            });
        }

        var dataPenjualan;

        $(document).ready(function () {
            dataPenjualan = $('#table_penjualan').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('penjualan/list') }}",
                    type: "POST",
                    data: function (d) {
                        d.filter_barang = $('.filter_barang').val();
                        d.filter_kode_penjualan = $('.filter_kode_penjualan').val();
                    }
                },
                columns: [
                    {
                        data: "DT_RowIndex",
                        className: "text-center",
                        width: "4%",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "detail_id",
                        className: "text-center",
                        width: "12%",
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: "penjualan.penjualan_kode",
                        className: "text-center",
                        width: "10%",
                        orderable: true,
                        searchable: true
                    },
                    
                    {
                        data: "penjualan.penjualan_tanggal",
                        className: "text-center",
                        width: "13%",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "barang.nama_barang",
                        className: "",
                        width: "25%",
                        orderable: false,
                        searchable: true,
                    },
                    {
                        data: "jumlah",
                        className: "text-center",
                        width: "9%",
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: "harga",
                        className: "text-right",
                        width: "15%",
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: "aksi",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#table_penjualan_filter input').unbind().bind('keyup', function (e) {
                if (e.keyCode == 13) {
                    dataPenjualan.search(this.value).draw();
                }
            });

            $('.filter_kode_penjualan, .filter_barang').change(function () {
                dataPenjualan.draw();
            });

            $('#penjualan_id, #barang_id, #user_id').change(function () {
                dataPenjualan.ajax.reload();
            });
        });
    </script>
@endpush