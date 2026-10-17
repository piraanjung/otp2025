<!DOCTYPE html>
<html>
<head>
    <title>Print QR Codes</title>
    <style>
        @media print {
            .no-print { display: none; }
        }
        .qr-container {
            display: inline-block;
            border: 1px solid #ccc;
            padding: 15px;
            margin: 10px;
            text-align: center;
            width: 150px;
        }
        .bin-code {
            font-weight: bold;
            margin-top: 5px;
            font-family: sans-serif;
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()">กดที่นี่เพื่อเริ่มพิมพ์</button>
    </div>

    @foreach ($bins as $bin)
        <div class="qr-container">
            {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)->generate($bin->bin_code) !!}
            <div class="bin-code">{{ $bin->bin_code }}</div>
        </div>
    @endforeach
</body>
</html>
