<?php
 
 namespace App\DataTables;
 
 use App\Models\BarangModel;
 use Illuminate\Database\Eloquent\Builder as QueryBuilder;
 use Yajra\DataTables\EloquentDataTable;
 use Yajra\DataTables\Html\Builder as HtmlBuilder;
 use Yajra\DataTables\Html\Button;
 use Yajra\DataTables\Html\Column;
 use Yajra\DataTables\Html\Editor\Editor;
 use Yajra\DataTables\Html\Editor\Fields;
 use Yajra\DataTables\Services\DataTable;
 
 class BarangDataTable extends DataTable
 {
     /**
      * Build the DataTable class.
      *
      * @param QueryBuilder $query Results from query() method.
      */
     public function dataTable(QueryBuilder $query): EloquentDataTable
     {
         return (new EloquentDataTable($query))
            ->addColumn('action', function ($row) {
                $editUrl = url('/barang/edit', $row->barang_id);
                $deleteUrl = url('/barang/delete', $row->barang_id);
                $csrfToken = csrf_token();
 
                return '
                <div class="d-flex gap-2">
                    <a href="' . $editUrl . '" class="btn btn-warning btn-sm d-flex align-items-center px-3">
                        Edit
                    </a>
                    <form action="' . $deleteUrl . '" method="POST" style="margin:0;">
                        <input type="hidden" name="_method" value="POST">
                        <input type="hidden" name="_token" value="' . $csrfToken . '">
                        <button type="submit" class="btn btn-danger btn-sm d-flex align-items-center px-3"
                            onclick="return confirm(\'Yakin ingin menghapus?\')">
                            Delete
                        </button>
                    </form>
                </div>
                ';
            })
         ->rawColumns(['action'])
         ->setRowId('barang_id');
     }
 
     /**
      * Get the query source of dataTable.
      */
     public function query(BarangModel $model): QueryBuilder
     {
        return $model->newQuery()->select('barang_id', 'kategori_id', 'kode_barang', 'nama_barang', 'harga_beli', 'harga_jual');
     }
 
     /**
      * Optional method if you want to use the html builder.
      */
     public function html(): HtmlBuilder
     {
         return $this->builder()
                     ->setTableId('barang-table')
                     ->columns($this->getColumns())
                     ->minifiedAjax()
                     //->dom('Bfrtip')
                     ->orderBy(1)
                     ->selectStyleSingle()
                     ->buttons([
                         Button::make('excel'),
                         Button::make('csv'),
                         Button::make('pdf'),
                         Button::make('print'),
                         Button::make('reset'),
                         Button::make('reload')
                     ]);
     }
 
     /**
      * Get the dataTable columns definition.
      */
     public function getColumns(): array
     {
         return [
             Column::make('barang_id'),
             Column::make('kategori_id'),
             Column::make('kode_barang'),
             Column::make('nama_barang'),
             Column::make('harga_beli'),
             Column::make('harga_jual'),
             Column::computed('action')
                   ->exportable(false)
                   ->printable(false)
                   ->width(60)
                   ->addClass('text-center'),
         ];
     }
 
     /**
      * Get the filename for export.
      */
     protected function filename(): string
     {
         return 'Barang_' . date('YmdHis');
     }
 }