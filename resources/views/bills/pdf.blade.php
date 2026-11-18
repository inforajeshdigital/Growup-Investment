<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $b['bill_no'] ?? 'Investment Receipt' }}</title>
    <style>
        @page { size: A4 portrait; margin: 32px 40px; }
        body { margin: 0; background: #fff; }
        /* A4 sheet: more room between lines, like the printed receipt book */
        .sheet .rcpt { font-size: 14px; }
        .sheet .rcpt .val { font-size: 14.5px; }
        .sheet .rcpt .fields td { padding-top: 22px; }
        .sheet .rcpt .title { font-size: 16px; margin-bottom: 10px; }
        .sheet .rcpt .logo { width: 170px; }
        .sheet .rcpt .cname { font-size: 26px; }
        .sheet .rcpt .watermark { top: 380px; width: 440px; margin-left: -220px; }
        .sheet .rcpt .sign-space { height: 60px; }
        @if (! empty($autoPrint))
            .toolbar { font-family: Arial, sans-serif; text-align: center; padding: 12px; background: #f1f3f6; }
            .toolbar button, .toolbar a { font-size: 14px; padding: 6px 16px; margin: 0 4px; cursor: pointer; }
            .sheet { max-width: 720px; margin: 20px auto; }
            @media print { .toolbar { display: none; } .sheet { margin: 0 auto; } }
        @endif
    </style>
</head>
<body>
@if (! empty($autoPrint))
    <div class="toolbar">
        <button type="button" onclick="window.print()">Print</button>
        <a href="javascript:window.close()">Close</a>
    </div>
@endif
<div class="sheet">
    @include('bills._receipt')
</div>
@if (! empty($autoPrint))
    <script>window.addEventListener('load', function () { setTimeout(function () { window.print(); }, 300); });</script>
@endif
</body>
</html>
