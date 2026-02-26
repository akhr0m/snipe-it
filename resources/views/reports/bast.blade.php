<!DOCTYPE html>
<html>
<head>
    <title>Berita Acara Serah Terima - {{ $user->present()->fullName() }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
        }
        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: top;
        }
        .header-table {
            width: 100%;
            border: none;
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
        }
        .info-table td {
            padding: 2px 0;
        }
        .center-text {
            text-align: center;
        }
        .no-border {
            border: none;
        }
        .asset-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .asset-table th, .asset-table td {
            border: 1px solid black;
            padding: 5px;
            text-align: left;
        }
        .signature-table {
            width: 100%;
            text-align: center;
            margin-top: 40px;
        }
        .signature-table td {
            padding-top: 60px;
        }

        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <table style="width: 100%; border: none; margin-bottom: 15px;">
        <tr>
            <td style="width: 120px; vertical-align: top;">
                @if ($settings->logo)
                    <img src="{{ asset('uploads/'.$settings->logo) }}" alt="Logo" style="width: 100px;">
                @endif
            </td>
            <td style="vertical-align: middle;">
                <h2 style="margin: 0; font-size: 18px;">RMK Group</h2>
                <p style="margin: 0; font-size: 11px;">
                    Jalan Puri Kencana Blok M4 No.1 RT.002/RW.07, Kel. Kembangan Selatan,<br>
                    Kec. Kembangan, Kota Jakarta Barat 11610
                </p>
            </td>
        </tr>
    </table>
    <hr style="border-top: 2px solid black; margin-bottom: 20px;">
    <div class="center-text">
        <h3>BERITA ACARA SERAH TERIMA</h3>
<p>{{ $newReportRecord->report_number }}</p>
    </div>

    <table class="header-table">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <table class="info-table">
                    <tr>
                        <td style="width: 120px;">Tujuan / Jabatan(Departemen)</td>
                        <td>: {{ $adminUser->present()->fullName() }} / {{ $adminUser->department?->name ?? 'IT' }}</td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>: {{ $adminUser->employee_num }}</td>
                    </tr>
                    <tr>
                        <td>Lokasi</td>
                        <td>: {{ $adminUser->location?->name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>Hari / Tanggal</td>
                        <td>: {{ $todayFormatted }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <table class="info-table">
                    <tr>
                        <td style="width: 120px;">Tujuan / Jabatan(Departemen)</td>
                        <td>: {{ $user->present()->fullName() }} - {{ $user->jobtitle ?? $user->department?->name }}</td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>: {{ $user->employee_num }}</td>
                    </tr>
                    <tr>
                        <td>Lokasi</td>
                        <td>: {{ $user->location?->name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>Perihal</td>
                        <td>: Penyerahan Aset (Inventaris Kantor)</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="asset-table">
        <thead>
            <tr>
                <th class="center-text">No</th>
                <th>Nama Barang</th>
                <th class="center-text">Qty</th>
                <th>Serial Number</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @if($assets->count() > 0)
                @foreach($assets as $key => $asset)
                <tr>
                    <td class="center-text">{{ $key + 1 }}</td>
                    <td>{{ $asset->name }}</td>
                    <td class="center-text">1</td>
                    <td>{{ $asset->serial }}</td>
                    <td>{{ $asset->notes }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" class="center-text">Tidak ada aset yang di-assign kepada pengguna ini.</td>
                </tr>
            @endif
        </tbody>
    </table>

    <table class="signature-table" style="margin-top: 50px; border-collapse: collapse; width: 100%;">
        <thead>
            <tr>
                <th colspan="3" style="border: 1px solid black; padding: 5px; text-align: center; font-weight: bold;">
                    PENYERAHAN
                </th>
            </tr>
            <tr>
                <th style="border: 1px solid black; padding: 5px; text-align: center; font-weight: bold; width: 33%;">Diserahkan Oleh</th>
                <th style="border: 1px solid black; padding: 5px; text-align: center; font-weight: bold; width: 34%;">Mengetahui</th>
                <th style="border: 1px solid black; padding: 5px; text-align: center; font-weight: bold; width: 33%;">Diterima Oleh</th>
            </tr>


        </thead>
        <tbody>
            <tr style="text-align: center;">
                <td style="border: 1px solid black; padding: 5px; height: 80px;"></td>
                <td style="border: 1px solid black; padding: 5px;"></td>
                <td style="border: 1px solid black; padding: 5px;"></td>
            </tr>
            <tr style="text-align: center;">
                <td style="border: 1px solid black; padding: 5px;">
                    <b>{{ $adminUser->present()->fullName() }}</b><br>
                    <i>{{ $adminUser->jobtitle ?? 'IT Department' }}</i>
                </td>
                <td style="border: 1px solid black; padding: 5px;">
                    <b></b><br>
                    <i>Atasan Langsung YBS</i>
                </td>
                <td style="border: 1px solid black; padding: 5px;">
                    <b>{{ $user->present()->fullName() }}</b><br>
                    <i>{{ $user->jobtitle ?? '' }}</i>
                </td>
            </tr>

        <tr>
            <td colspan="3" style="border: none; text-align: left; padding-top: 10px;">
                <strong>Notes:</strong>
                <div style="border: 1px solid #000; min-height: 30px; margin-top: 2px;">&nbsp;</div>
            </td>
        </tr>

        </tbody>
    </table>

    <table class="signature-table" style="margin-top: 20px; border-collapse: collapse; width: 100%;">
        <thead>
            <tr>
                <th colspan="3" style="border: 1px solid black; padding: 5px; text-align: center; font-weight: bold;">
                    PENGEMBALIAN
                </th>
            </tr>
            <tr>
                <th style="border: 1px solid black; padding: 5px; text-align: center; font-weight: bold; width: 33%;">Diserahkan Oleh</th>
                <th style="border: 1px solid black; padding: 5px; text-align: center; font-weight: bold; width: 34%;">Mengetahui</th>
                <th style="border: 1px solid black; padding: 5px; text-align: center; font-weight: bold; width: 33%;">Diterima Oleh</th>
            </tr>
        </thead>
        <tbody>
            <tr style="text-align: center;">
                <td style="border: 1px solid black; padding: 5px; height: 80px;"></td>
                <td style="border: 1px solid black; padding: 5px;"></td>
                <td style="border: 1px solid black; padding: 5px;"></td>
            </tr>
            <tr style="text-align: center;">
                <td style="border: 1px solid black; padding: 5px;">
                    <b>{{ $user->present()->fullName() }}</b><br>
                    <i>{{ $user->jobtitle ?? '' }}</i>
                </td>
                <td style="border: 1px solid black; padding: 5px;">
                    <b></b><br>
                    <i>HRGA</i>
                </td>
                <td style="border: 1px solid black; padding: 5px;">
                    <b>{{ $adminUser->present()->fullName() }}</b><br>
                    <i>{{ $adminUser->jobtitle ?? 'IT Department' }}</i>
                </td>
            </tr>

        <tr>
            <td colspan="3" style="border: none; text-align: left; padding-top: 10px;">
                <strong>Notes:</strong>
                <div style="border: 1px solid #000; min-height: 30px; margin-top: 2px;">&nbsp;</div>
            </td>
        </tr>

        </tbody>
    </table>
    
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()">Cetak Laporan</button>
    </div>

</body>
</html>
