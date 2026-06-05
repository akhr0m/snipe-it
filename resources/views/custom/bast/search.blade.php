@extends('layouts/default')

@section('title')
Cari Laporan BAST
@parent
@stop

@section('content')
<style>
    /* Hover effect for bootstrap-table rows */
    #bastReportsTable tbody tr:hover {
        background-color: #f5f5f5 !important;
        cursor: pointer;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Riwayat Laporan BAST</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table
                        data-cookie-id-table="bastReportsTable"
                        data-id-table="bastReportsTable"
                        data-side-pagination="server"
                        data-sort-order="desc"
                        data-sort-name="created_at"
                        data-search="true"
                        id="bastReportsTable"
                        class="table table-striped snipe-table"
                        data-url="{{ route('bast.api.index') }}"
                        data-export-options='{
                            "fileName": "export-bast-reports-{{ date('Y-m-d') }}",
                            "ignoreColumn": ["actions"]
                        }'>
                        <thead>
                            <tr>
                                <th data-field="id" data-visible="false" data-sortable="true">ID</th>
                                <th data-field="sequence" data-formatter="rowNumberFormatter" style="width: 50px;">No</th>
                                <th data-field="bast_number" data-sortable="true" data-searchable="true">No BAST</th>
                                <th data-field="username" data-sortable="true" data-searchable="true">Nama User</th>
                                <th data-field="user_email" data-sortable="true" data-searchable="true">Email</th>
                                <th data-field="user_nik" data-sortable="true" data-searchable="true">NIK</th>
                                <th data-field="date_printed" data-sortable="true">Tanggal Cetak</th>
                                <th data-field="actions" data-formatter="bastActionsFormatter" style="text-align: center; width: 100px;">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('moar_scripts')
@include ('partials.bootstrap-table', ['exportFile' => 'bast-reports-export', 'search' => true])
<script nonce="{{ csrf_token() }}">
    function rowNumberFormatter(value, row, index) {
        var tableOptions = $('#bastReportsTable').bootstrapTable('getOptions');
        return index + 1 + (tableOptions.pageSize * (tableOptions.pageNumber - 1));
    }

    function bastActionsFormatter(value, row) {
        return '<a href="{{ config('app.url') }}/bast-report/print/' + row.id + '" class="btn btn-xs btn-info" target="_blank" title="Lihat / Cetak BAST"><i class="fa fa-eye"></i></a>';
    }
</script>
@stop
