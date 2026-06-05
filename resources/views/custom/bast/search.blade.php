@extends('layouts/default')

@section('title', 'Cari Laporan BAST')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Cari Laporan BAST</h3>
            </div>
            <div class="box-body">
                <form action="{{ route('bast.search') }}" method="GET" class="form-horizontal">
                    <div class="form-group">
                        <label for="search" class="col-sm-3 control-label">Nomor BAST</label>
                        <div class="col-sm-6">
                            <input type="text" name="search" id="search" class="form-control" 
                                   value="{{ request('search') }}" 
                                   placeholder="Contoh: 00021/BAST/IT/HO/IX/2025">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-search"></i> Cari
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="box box-default" style="margin-top: 20px;">
            <div class="box-header with-border">
                <h3 class="box-title">Riwayat Laporan BAST</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>No BAST</th>
                                <th>Nama User</th>
                                <th style="width: 100px; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reports as $index => $report)
                                <tr>
                                    <td>{{ $reports->firstItem() + $index }}</td>
                                    <td>{{ $report->bast_number }}</td>
                                    <td>{{ $report->username }}</td>
                                    <td style="text-align: center;">
                                        <a href="{{ route('bast.reprint', $report->id) }}" 
                                           class="btn btn-xs btn-info" 
                                           target="_blank" 
                                           title="Lihat / Cetak BAST">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Data BAST tidak ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="pull-right">
                    {{ $reports->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop
