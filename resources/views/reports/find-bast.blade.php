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
                            <input class="form-control" type="text" name="number" id="number" placeholder="Contoh: 00021/BAST/IT/HO/IX/2025" required>
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
            </div> </div> </div> </div> @stop

{{-- ▼▼▼ BLOK SCRIPT DIPINDAHKAN KE SINI ▼▼▼ --}}
@section('moar_scripts')
<script>
    // JEJAK 1: Pesan ini HARUS muncul di Console saat halaman dimuat.
    console.log('Skrip Find BAST: Berhasil dimuat.');

    document.addEventListener('DOMContentLoaded', function() {
        // JEJAK 2: Pesan ini HARUS muncul di Console setelah semua elemen HTML siap.
        console.log('Skrip Find BAST: DOM siap, mencari tombol.');

        const findButton = document.getElementById('findBastBtn');
        const errorMessageDiv = document.getElementById('error-message');

        if (findButton) {
            // JEJAK 3: Pesan ini HARUS muncul jika tombolnya berhasil ditemukan.
            console.log('Skrip Find BAST: Tombol "Cari" ditemukan, event listener dipasang.');

            findButton.addEventListener('click', function() {
                // JEJAK 4: Pesan ini HARUS muncul TEPAT saat Anda mengklik tombol "Cari".
                console.log('Skrip Find BAST: Tombol "Cari" DIKLIK!');

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
        } else {
            // JEJAK ERROR: Pesan ini akan muncul jika tombol dengan ID "findBastBtn" TIDAK ditemukan.
            console.error('Skrip Find BAST: GAGAL! Tombol dengan ID "findBastBtn" tidak ditemukan!');
        }
    });
</script>
@endsection
