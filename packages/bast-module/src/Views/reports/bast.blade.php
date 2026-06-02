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
            padding: 4px 6px;
        }
    </style>
</head>
<body>

@php
    $isPreview = $isPreview ?? true;
    $newReportRecord = $newReportRecord ?? null;
@endphp

<table class="header-table">
    <tr>
        <td style="width: 70%;">
            <h3>{{ data_get($reportHeader, 'site_name', config('app.name')) }}</h3>
        </td>
        <td style="text-align: right; width: 30%;">
            @if(!empty(data_get($reportHeader, 'logo')))
                <img src="{{ data_get($reportHeader, 'logo') }}" alt="logo" style="max-height: 60px;">
            @endif
        </td>
    </tr>
</table>

<table class="main-table">
    <thead>
    <tr>
        <th>No</th>
        <th>Asset Tag</th>
        <th>Nama</th>
        <th>Serial</th>
    </tr>
    </thead>
    <tbody>
    @foreach($assets as $i => $asset)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ data_get($asset, 'asset_tag') }}</td>
            <td>{{ data_get($asset, 'name') }}</td>
            <td>{{ data_get($asset, 'serial') }}</td>
        </tr>
    @endforeach

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
