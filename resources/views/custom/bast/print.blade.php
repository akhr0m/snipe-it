<!doctype html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Berita Acara Serah Terima Aset</title>
    <link rel="stylesheet" href="{{ url(mix('css/dist/all.css')) }}">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #fff;
            color: #000;
            margin: 0;
            padding: 20px;
            font-size: 11px;
            line-height: 1.3;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            padding: 10px;
        }
        /* Header Logo Section */
        .header-logo {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .logo-img {
            height: 45px;
            margin-right: 15px;
        }
        .logo-text {
            flex-grow: 1;
        }
        .logo-text h3 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
            color: #008000; /* Green theme matching RMK logo */
        }
        .logo-text p {
            margin: 2px 0 0 0;
            font-size: 8.5px;
            color: #555;
            line-height: 1.2;
        }
        /* Title */
        .doc-title {
            text-align: center;
            margin-bottom: 15px;
        }
        .doc-title h2 {
            margin: 0;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-title p {
            margin: 3px 0 0 0;
            font-size: 11px;
            font-weight: bold;
        }
        /* Meta Info Grid */
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 15px;
        }
        .meta-col table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-col td {
            padding: 3px 0;
            vertical-align: middle;
            font-size: 10.5px;
        }
        .meta-col td.meta-label {
            width: 140px;
            color: #000 !important;
            font-weight: normal;
        }
        .meta-col td.separator {
            width: 15px;
            text-align: center;
        }
        /* Content Table */
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.items-table th, table.items-table td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 10.5px;
            text-align: left;
        }
        table.items-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        table.items-table td.center {
            text-align: center;
        }
        /* Signature Blocks */
        .sig-block {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .sig-block td {
            border: 1px solid #000;
            width: 33.33%;
            padding: 3px;
            font-size: 10px;
            vertical-align: top;
        }
        .sig-block td.title-cell {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
            padding: 4px;
        }
        .sig-height {
            height: 60px;
        }
        .sig-name {
            text-align: center;
            font-weight: bold;
        }
        .sig-title {
            text-align: center;
            font-size: 8px;
            color: #555;
            margin-top: 2px;
        }
        /* Notes Box */
        .notes-box {
            border: 1px solid #000;
            padding: 6px;
            min-height: 25px;
            margin-bottom: 15px;
            font-size: 10px;
            background-color: #fafafa;
        }
        .editable {
            background-color: #ffffe0;
            border: 1px dashed #ff8c00;
            padding: 2px;
        }
        /* Print actions bar */
        .action-bar {
            text-align: center;
            margin-top: 30px;
            padding: 15px;
            background-color: #f7f7f7;
            border-top: 1px solid #ddd;
        }
        /* Success Toast Notification */
        #success-toast {
            visibility: hidden;
            min-width: 250px;
            background-color: #2b542c;
            color: #fff;
            text-align: center;
            border-radius: 4px;
            padding: 12px 24px;
            position: fixed;
            z-index: 9999;
            left: 50%;
            bottom: 30px;
            transform: translateX(-50%);
            font-size: 13px;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        #success-toast.show {
            visibility: visible;
            -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }
        @-webkit-keyframes fadein {
            from {bottom: 0; opacity: 0;} 
            to {bottom: 30px; opacity: 1;}
        }
        @keyframes fadein {
            from {bottom: 0; opacity: 0;}
            to {bottom: 30px; opacity: 1;}
        }
        @-webkit-keyframes fadeout {
            from {bottom: 30px; opacity: 1;} 
            to {bottom: 0; opacity: 0;}
        }
        @keyframes fadeout {
            from {bottom: 30px; opacity: 1;}
            to {bottom: 0; opacity: 0;}
        }

        @media print {
            body {
                padding: 10px;
                font-size: 10px;
            }
            .action-bar, .hidden-print {
                display: none !important;
            }
            .editable {
                background-color: transparent !important;
                border: none !important;
                padding: 0 !important;
            }
            .container {
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
            }
            .notes-box {
                background-color: transparent !important;
            }
        }
    </style>
</head>
<body>

@php
    $logoUrl = 'https://rmkgi.com/assets/images/Logo-RMK.png';
    
    $isReprint = isset($report);
    $bastNum = $isReprint ? $report->bast_number : $bastNumber;
    
    $admName = $isReprint ? $report->admin_name : $admin->present()->fullName();
    $admDept = $isReprint ? $report->admin_department : ($admin->department ? $admin->department->name : 'IT');
    $admTitle = $isReprint ? $report->admin_title : ($admin->jobtitle ?: 'IT OFFICER');
    $admLoc = $isReprint ? $report->admin_location : ($admin->location ? $admin->location->name : 'Wisma RMK (JAKARTA)');
    
    $usrName = $isReprint ? $report->username : $user->present()->fullName();
    $usrDept = $isReprint ? $report->user_department : ($user->department ? $user->department->name : '');
    $usrJob = $isReprint ? $report->user_jobtitle : $user->jobtitle;
    $usrNik = $isReprint ? $report->user_nik : $user->employee_num;
    $usrLoc = $isReprint ? $report->user_location : ($user->location ? $user->location->name : '');
    
    $datePrintedStr = $dateFormatted;
    $perihalText = $isReprint ? $report->perihal : 'Penyerahan Aset (Inventaris Kantor)';
    $notesText = $isReprint ? $report->notes : '';
    $returnNotesText = $isReprint ? $report->return_notes : '';
    
    $items = [];
    if ($isReprint) {
        $items = $report->assets_data;
    } else {
        foreach ($assets as $asset) {
            $items[] = [
                'id' => $asset->id,
                'name' => ($asset->model->manufacturer ? $asset->model->manufacturer->name . ' ' : '') . $asset->model->name . ($asset->name ? ' - ' . $asset->name : ''),
                'qty' => 1,
                'serial' => $asset->serial,
                'keterangan' => $asset->keterangan,
            ];
        }
    }
@endphp

<div class="container">
    <!-- Header Logo Section -->
    <div class="header-logo">
        <img src="{{ $logoUrl }}" class="logo-img" alt="Logo">
        <div class="logo-text">
            <h3>RMK Group</h3>
            <p>Jalan Puri Kencana Blok M4 No.1 RT.002/RW.07, Kel. Kembangan Selatan,<br>Kec. Kembangan, Kota Jakarta Barat 11610</p>
        </div>
    </div>

    <!-- Document Title -->
    <div class="doc-title">
        <h2>Berita Acara Serah Terima</h2>
        <p>{{ $bastNum }}</p>
    </div>

    <!-- Meta Information Grid -->
    <div class="meta-grid">
        <!-- Left Side (Admin Info) -->
        <div class="meta-col">
            <table>
                <tr>
                    <td class="meta-label">Nama / Departemen</td>
                    <td class="separator">:</td>
                    <td>{{ $admName }} / {{ $admDept }}</td>
                </tr>
                <tr>
                    <td class="meta-label">NIK</td>
                    <td class="separator">:</td>
                    <td>
                        <span id="admin-nik-field" {!! !$isReprint ? 'class="editable" contenteditable="true"' : '' !!} style="min-width: 150px; display: inline-block;">{{ $isReprint ? $report->admin_nik : '' }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="meta-label">Lokasi</td>
                    <td class="separator">:</td>
                    <td>{{ $admLoc }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Hari / Tanggal</td>
                    <td class="separator">:</td>
                    <td>{{ $datePrintedStr }}</td>
                </tr>
            </table>
        </div>

        <!-- Right Side (User Info) -->
        <div class="meta-col">
            <table>
                <tr>
                    <td class="meta-label">Tujuan / Jabatan (Departemen)</td>
                    <td class="separator">:</td>
                    <td>{{ $usrName }} - {{ $usrJob }} {{ $usrDept ? '('.$usrDept.')' : '' }}</td>
                </tr>
                <tr>
                    <td class="meta-label">NIK</td>
                    <td class="separator">:</td>
                    <td>
                        <span id="user-nik-field" {!! !$isReprint ? 'class="editable" contenteditable="true"' : '' !!} style="min-width: 150px; display: inline-block;">{{ $isReprint ? $report->user_nik : '' }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="meta-label">Lokasi</td>
                    <td class="separator">:</td>
                    <td>{{ $usrLoc ?: '-' }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Perihal</td>
                    <td class="separator">:</td>
                    <td>
                        <span id="perihal-field" {!! !$isReprint ? 'class="editable" contenteditable="true"' : '' !!} style="min-width: 200px; display: inline-block;">{{ $perihalText }}</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table" id="assets-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Nama Barang</th>
                <th style="width: 50px;">Qty</th>
                <th style="width: 150px;">Serial Number</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $index => $item)
                <tr data-id="{{ $item['id'] }}">
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="asset-name">{{ $item['name'] }}</td>
                    <td class="center asset-qty">{{ $item['qty'] }}</td>
                    <td class="asset-serial">{{ $item['serial'] ?: '-' }}</td>
                    <td class="asset-keterangan {!! !$isReprint ? 'editable' : '' !!}" {!! !$isReprint ? 'contenteditable="true"' : '' !!}>{{ $item['keterangan'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Penyerahan signature block -->
    <table class="sig-block">
        <tr>
            <td colspan="3" class="title-cell">PENYERAHAN</td>
        </tr>
        <tr>
            <td class="center" style="font-weight: bold;">Diserahkan Oleh</td>
            <td class="center" style="font-weight: bold;">Mengetahui</td>
            <td class="center" style="font-weight: bold;">Diterima Oleh</td>
        </tr>
        <tr class="sig-height">
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>
                <div class="sig-name">{{ $admName }}</div>
                <div class="sig-title">{{ $admTitle }}</div>
            </td>
            <td>
                <div class="sig-name" style="color: #777; font-weight: normal; font-style: italic;">Atasan Langsung YBS</div>
            </td>
            <td>
                <div class="sig-name">{{ $usrName }}</div>
                <div class="sig-title">{{ $usrJob }}</div>
            </td>
        </tr>
    </table>

    <div class="notes-label" style="font-weight: bold; margin-bottom: 2px;">Notes:</div>
    <div class="notes-box {!! !$isReprint ? 'editable' : '' !!}" id="notes-field" {!! !$isReprint ? 'contenteditable="true"' : '' !!}>{{ $notesText }}</div>

    <!-- Pengembalian signature block -->
    <table class="sig-block">
        <tr>
            <td colspan="3" class="title-cell">PENGEMBALIAN</td>
        </tr>
        <tr>
            <td class="center" style="font-weight: bold;">Diserahkan Oleh</td>
            <td class="center" style="font-weight: bold;">Mengetahui</td>
            <td class="center" style="font-weight: bold;">Diterima Oleh</td>
        </tr>
        <tr class="sig-height">
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>
                <div class="sig-name">{{ $usrName }}</div>
                <div class="sig-title">{{ $usrJob }}</div>
            </td>
            <td>
                <div class="sig-name" style="color: #777; font-weight: normal; font-style: italic;">HRGA</div>
            </td>
            <td>
                <div class="sig-name">{{ $admName }}</div>
                <div class="sig-title">{{ $admTitle }}</div>
            </td>
        </tr>
    </table>

    <div class="notes-label" style="font-weight: bold; margin-bottom: 2px;">Notes:</div>
    <div class="notes-box {!! !$isReprint ? 'editable' : '' !!}" id="notes-return-field" {!! !$isReprint ? 'contenteditable="true"' : '' !!}>{{ $returnNotesText }}</div>

    <!-- Print Action Bar (Hidden during printing) -->
    <div class="action-bar hidden-print">
        @if (!$isReprint)
            <button class="btn btn-success" id="btn-save-print" style="padding: 6px 20px; font-size: 12px; font-weight: bold;">
                <i class="fa fa-print"></i> Cetak Laporan
            </button>
        @else
            <button class="btn btn-primary" onclick="window.print()" style="padding: 6px 20px; font-size: 12px; font-weight: bold;">
                <i class="fa fa-print"></i> Cetak Ulang
            </button>
        @endif
        <button class="btn btn-default" onclick="window.close()" style="padding: 6px 20px; font-size: 12px; margin-left: 10px;">
            Tutup
        </button>
    </div>
</div>

<!-- Success Toast -->
<div id="success-toast">BAST berhasil disimpan.</div>

<!-- Scripts -->
<script src="{{ url(mix('js/dist/all.js')) }}"></script>
<script>
    function showToast(message) {
        var toast = document.getElementById("success-toast");
        toast.innerText = message;
        toast.className = "show";
        setTimeout(function(){ toast.className = toast.className.replace("show", ""); }, 3000);
    }

    $(document).ready(function() {
        $('#btn-save-print').click(function() {
            var assets = [];
            $('#assets-table tbody tr').each(function() {
                var id = $(this).data('id');
                var name = $(this).find('.asset-name').text().trim();
                var qty = $(this).find('.asset-qty').text().trim();
                var serial = $(this).find('.asset-serial').text().trim();
                var keterangan = $(this).find('.asset-keterangan').text().trim();
                assets.push({
                    id: id,
                    name: name,
                    qty: qty,
                    serial: serial,
                    keterangan: keterangan
                });
            });

            var notesContent = $('#notes-field').text().trim();
            var returnNotes = $('#notes-return-field').text().trim();

            $.ajax({
                url: "{{ route('bast.save') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    bast_number: "{{ $bastNum }}",
                    user_id: "{{ !$isReprint ? $user->id : '' }}",
                    perihal: $('#perihal-field').text().trim(),
                    admin_nik: $('#admin-nik-field').text().trim(),
                    user_nik: $('#user-nik-field').text().trim(),
                    notes: notesContent,
                    return_notes: returnNotes,
                    assets: assets
                },
                success: function(response) {
                    showToast("BAST berhasil disimpan.");
                    setTimeout(function() {
                        window.print();
                    }, 1000);
                },
                error: function(xhr) {
                    alert(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : "Gagal menyimpan BAST.");
                }
            });
        });
    });
</script>
</body>
</html>
