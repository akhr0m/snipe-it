@extends('layouts.default')

@section('title')
Cari Laporan BAST
@stop

@section('content')

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Cari Laporan BAST</h3>
            </div>
            <div class="box-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="number" class="col-md-3 control-label">Nomor BAST</label>
                        <div class="col-md-7">
                            <input class="form-control" type="text" name="number" id="number"
                                placeholder="Contoh: 00021/BAST/IT/HO/IX/2025" required>
                            <div id="error-message" style="color: red; margin-top: 5px; display: none;"></div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <div class="col-md-9 col-md-offset-3">
                            <button type="button" id="findBastBtn" class="btn btn-primary">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="margin-top: 20px;">
    <div class="col-md-12">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Riwayat Laporan BAST</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-striped table-hover" id="bastReportsTable">
                    <thead>
                        <tr>
                            <th data-field="no" data-sortable="true" style="width: 50px; text-align: center;">No</th>
                            <th data-field="report_number" data-sortable="true">No BAST</th>
                            <th data-field="name" data-sortable="true">Nama User</th>
                            <th data-field="actions" style="width: 100px; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bastReports as $index => $report)
                            <tr>
                                <td style="text-align: center;">{{ $index + 1 }}</td>
                                <td>{{ $report->report_number }}</td>
                                <td>
                                    {{ $report->recipient ? $report->recipient->first_name . ' ' . $report->recipient->last_name : 'User Tidak Ditemukan / Dihapus' }}
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ url('/bast-report/view/' . $report->id) }}" class="btn btn-sm btn-info"
                                        title="Lihat Dokumen" target="_blank" rel="noopener noreferrer">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('moar_scripts')
    <script>
        $(document).ready(function () {
            $('#bastReportsTable').bootstrapTable({
                search: true,               // Memunculkan kotak pencarian
                pagination: true,           // Memunculkan fitur halaman (pagination)
                sidePagination: 'client',   // Wajib ada agar membaca data lokal dari HTML
                pageSize: 15,               // Jumlah data per halaman
                pageList: [15, 30, 50, 100]
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const findButton = document.getElementById('findBastBtn');
            const errorMessageDiv = document.getElementById('error-message');

            if (findButton) {
                findButton.addEventListener('click', function () {
                    // Sembunyikan pesan error lama
                    errorMessageDiv.style.display = 'none';

                    const bastNumber = document.getElementById('number').value;

                    if (!bastNumber) {
                        errorMessageDiv.innerText = 'Silakan masukkan Nomor BAST yang ingin dicari.';
                        errorMessageDiv.style.display = 'block';
                        return;
                    }

                    const checkUrl = "{{ route('bast.check') }}?number=" + encodeURIComponent(bastNumber);

                    fetch(checkUrl)
                        .then(response => response.json())
                        .then(data => {
                            if (data.found) {
                                const reportUrl = "{{ route('bast.find') }}?number=" + encodeURIComponent(bastNumber);
                                window.open(reportUrl, '_blank');
                            } else {
                                errorMessageDiv.innerText = 'Laporan BAST tidak ditemukan. Mohon periksa kembali nomor yang Anda masukkan.';
                                errorMessageDiv.style.display = 'block';
                            }
                        })
                        .catch(error => {
                            console.error('Fetch Error:', error);
                            errorMessageDiv.innerText = 'Terjadi kesalahan saat menghubungi server.';
                            errorMessageDiv.style.display = 'block';
                        });
                });
            }
        });
    </script>
@endsection