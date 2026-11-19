@php
    $c = $company;
    $services = $c->lines('services');
    $highlights = $c->lines('highlights');
    $people = $c->people();
    $serviceRows = array_chunk($services, 2);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $c->company_name }} - Company Profile</title>
    <style>
        @page { margin: 36px 42px 60px; }
        body { font-family: Helvetica, Arial, sans-serif; color: #1f2937; font-size: 12.5px; line-height: 1.5; }
        table { width: 100%; border-collapse: collapse; }
        .head td { vertical-align: middle; }
        .logo { width: 150px; }
        .cname { font-family: "DejaVu Serif", "Times New Roman", serif; color: #c8102e; font-size: 22px; line-height: 1.25; text-transform: uppercase; }
        .tagline { color: #0f7a1c; font-size: 10px; font-weight: bold; letter-spacing: 1.5px; margin-top: 4px; }
        .doc-title { color: #0b5cc4; font-size: 11px; letter-spacing: 2px; text-transform: uppercase; margin-top: 6px; }
        .rule { border-bottom: 3px solid #0b5cc4; margin: 12px 0 18px; }
        h2 { color: #083f88; font-size: 15px; margin: 18px 0 8px; padding-bottom: 4px; border-bottom: 1px solid #dbe4f3; }
        p { margin: 0 0 6px; }
        .pre { white-space: pre-line; }
        .box { background: #f1f6ff; border-left: 3px solid #0b5cc4; padding: 10px 12px; }
        .box.green { border-left-color: #0f7a1c; background: #f0f8f1; }
        .box-title { font-weight: bold; margin-bottom: 4px; }
        .svc td { width: 50%; padding: 6px 10px 6px 0; vertical-align: top; }
        .svc .item { background: #f1f6ff; padding: 7px 10px; }
        .bullet { color: #0f7a1c; font-weight: bold; }
        .details td { padding: 5px 8px; border: 1px solid #dbe4f3; }
        .details td.k { width: 30%; background: #f5f7fb; font-weight: bold; }
        .footer { position: fixed; bottom: -40px; left: 0; right: 0; font-size: 9.5px; color: #6b7280; border-top: 1px solid #dbe4f3; padding-top: 6px; }
    </style>
</head>
<body>
<div class="footer">
    <table>
        <tr>
            <td>{{ $c->company_name }}@if ($c->email) &middot; {{ $c->email }}@endif @if ($c->mobile) &middot; {{ $c->mobile }}@endif</td>
            @if ($c->about_published)<td style="text-align: right;">{{ route('about') }}</td>@endif
        </tr>
    </table>
</div>

<table class="head">
    <tr>
        <td style="width: 165px;"><img src="{{ $logo }}" class="logo" alt="Logo"></td>
        <td>
            <div class="cname">{{ $c->company_name }}</div>
            {{-- The default logo already has the tagline in it --}}
            @if ($c->tagline && $c->logo_path)<div class="tagline">{{ $c->tagline }}</div>@endif
            <div class="doc-title">Company Profile</div>
        </td>
    </tr>
</table>
<div class="rule"></div>

@if ($c->about_us)
    <h2>About Us</h2>
    <p class="pre">{{ $c->about_us }}</p>
@endif

@if ($c->mission || $c->vision)
    <table style="margin-top: 14px;">
        <tr>
            @if ($c->mission)
                <td style="vertical-align: top; {{ $c->vision ? 'width: 50%; padding-right: 8px;' : '' }}">
                    <div class="box"><div class="box-title">Our Mission</div><div class="pre">{{ $c->mission }}</div></div>
                </td>
            @endif
            @if ($c->vision)
                <td style="vertical-align: top; {{ $c->mission ? 'width: 50%; padding-left: 8px;' : '' }}">
                    <div class="box green"><div class="box-title">Our Vision</div><div class="pre">{{ $c->vision }}</div></div>
                </td>
            @endif
        </tr>
    </table>
@endif

@if ($services)
    <h2>Our Services</h2>
    <table class="svc">
        @foreach ($serviceRows as $row)
            <tr>
                @foreach ($row as $service)
                    <td><div class="item"><span style="font-family: 'DejaVu Sans'; color: #0b5cc4;">&#10003;</span>&nbsp; {{ $service }}</div></td>
                @endforeach
                @if (count($row) === 1)<td></td>@endif
            </tr>
        @endforeach
    </table>
@endif

@if ($highlights)
    <h2>Why Choose Us</h2>
    @foreach ($highlights as $item)
        <p><span class="bullet">&#8226;</span>&nbsp; {{ $item }}</p>
    @endforeach
@endif

@if ($people)
    <h2>Our Team</h2>
    <table class="details">
        @foreach ($people as $person)
            <tr><td class="k">{{ $person['name'] }}</td><td>{{ $person['role'] ?: '-' }}</td></tr>
        @endforeach
    </table>
@endif

<h2>Company Details &amp; Contact</h2>
<table class="details">
    @if ($c->established_year)<tr><td class="k">Established</td><td>{{ $c->established_year }}</td></tr>@endif
    @if ($c->cin_no)<tr><td class="k">CIN</td><td>{{ $c->cin_no }}</td></tr>@endif
    @if ($c->gst_no)<tr><td class="k">GSTIN</td><td>{{ $c->gst_no }}</td></tr>@endif
    @if ($c->address)<tr><td class="k">Address</td><td>{{ $c->address }}</td></tr>@endif
    @if ($c->mobile)<tr><td class="k">Phone</td><td>{{ $c->mobile }}</td></tr>@endif
    @if ($c->whatsapp)<tr><td class="k">WhatsApp</td><td>{{ $c->whatsapp }}</td></tr>@endif
    @if ($c->email)<tr><td class="k">Email</td><td>{{ $c->email }}</td></tr>@endif
    @if ($c->website)<tr><td class="k">Website</td><td>{{ $c->website }}</td></tr>@endif
    @if ($c->about_published)<tr><td class="k">Online Profile</td><td>{{ route('about') }}</td></tr>@endif
</table>
</body>
</html>
