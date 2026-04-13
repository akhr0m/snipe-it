<!DOCTYPE html>
<html>
<head>
    @php
        $recipientFullName = trim(collect([data_get($user, 'first_name'), data_get($user, 'last_name')])->filter()->implode(' '));
        $adminFullName = trim(collect([data_get($adminUser, 'first_name'), data_get($adminUser, 'last_name')])->filter()->implode(' '));
    @endphp
    <title>Berita Acara Serah Terima - {{ $recipientFullName }}</title>
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
    @php
        $isPreview = isset($reportNumber) && !isset($newReportRecord);
        $reportHeaderName = data_get($reportHeader ?? [], 'site_name', $settings->site_name ?: config('app.name', 'Snipe-IT'));
        $reportHeaderLines = collect([
            data_get($adminUser, 'location.address'),
            data_get($adminUser, 'location.address2'),
            collect([
                data_get($adminUser, 'location.city'),
                data_get($adminUser, 'location.state'),
                data_get($adminUser, 'location.zip'),
            ])->filter()->implode(', '),
            data_get($adminUser, 'location.country'),
        ])->filter()->values();
        $reportHeaderText = $reportHeaderLines->implode("\n");
        $reportLogo = data_get($reportHeader ?? [], 'logo', $settings->logo);
        $recipientRole = data_get($user, 'jobtitle') ?: data_get($user, 'department.name');
        $recipientDepartment = data_get($user, 'department.name');
        $adminDepartment = data_get($adminUser, 'department.name') ?: 'IT';
    @endphp

    <table style="width: 100%; border: none; margin-bottom: 15px;">
        <tr>
            <td style="width: 120px; vertical-align: top;">
                @if ($reportLogo)
                    <img src="{{ asset('uploads/'.$reportLogo) }}" alt="Logo" style="width: 100px;">
                @endif
            </td>
            <td style="vertical-align: middle;">
                <h2 style="margin: 0; font-size: 18px;">{{ $reportHeaderName }}</h2>
                @if ($reportHeaderText !== '')
                    <p style="margin: 0; font-size: 11px;">
                        {!! nl2br(e($reportHeaderText)) !!}
                    </p>
                @endif
            </td>
        </tr>
    </table>
    <hr style="border-top: 2px solid black; margin-bottom: 20px;">
    <div class="center-text">
        <h3>BERITA ACARA SERAH TERIMA</h3>
<p>{{ $reportNumber ?? $newReportRecord->report_number }}</p>
    </div>

    <table class="header-table">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <table class="info-table">
                    <tr>
                        <td style="width: 120px;">Tujuan / Jabatan(Departemen)</td>
                        <td>: {{ $adminFullName }} / {{ $adminDepartment }}</td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>: {{ data_get($adminUser, 'employee_num') }}</td>
                    </tr>
                    <tr>
                        <td>Lokasi</td>
                        <td>: {{ data_get($adminUser, 'location.name', '') }}</td>
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
                        <td>: {{ $recipientFullName }} - {{ $recipientRole }}@if($recipientDepartment) ({{ $recipientDepartment }})@endif</td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>: {{ data_get($user, 'employee_num') }}</td>
                    </tr>
                    <tr>
                        <td>Lokasi</td>
                        <td>: {{ data_get($user, 'location.name', '') }}</td>
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
                    <td>{{ data_get($asset, 'name') }}</td>
                    <td class="center-text">1</td>
                    <td>{{ data_get($asset, 'serial') }}</td>
                    <td>{{ data_get($asset, 'notes') }}</td>
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
                    <b>{{ $adminFullName }}</b><br>
                    <i>{{ data_get($adminUser, 'jobtitle', 'IT Department') }}</i>
                </td>
                <td style="border: 1px solid black; padding: 5px;">
                    <b></b><br>
                    <i>Atasan Langsung YBS</i>
                </td>
                <td style="border: 1px solid black; padding: 5px;">
                    <b>{{ $recipientFullName }}</b><br>
                    <i>{{ data_get($user, 'jobtitle', '') }}</i>
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
                    <b>{{ $recipientFullName }}</b><br>
                    <i>{{ data_get($user, 'jobtitle', '') }}</i>
                </td>
                <td style="border: 1px solid black; padding: 5px;">
                    <b></b><br>
                    <i>HRGA</i>
                </td>
                <td style="border: 1px solid black; padding: 5px;">
                    <b>{{ $adminFullName }}</b><br>
                    <i>{{ data_get($adminUser, 'jobtitle', 'IT Department') }}</i>
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
        @if (!empty($showSavedMessage))
            <div style="margin-bottom: 12px; padding: 10px 14px; border: 1px solid #1e7e34; background: #eaf7ed; color: #1e4620; display: inline-block;">
                BAST berhasil disimpan.
            </div>
            <br>
        @endif
        @if ($isPreview)
            <form action="{{ route('users.bast_report.print', $user) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit">Cetak Laporan</button>
            </form>
        @else
            <button type="button" onclick="window.print()">Cetak Laporan</button>
        @endif
    </div>

    @if (!empty($autoPrint))
        <script>
            window.addEventListener('load', function () {
                window.print();
            });
        </script>
    @endif

</body>
</html>
