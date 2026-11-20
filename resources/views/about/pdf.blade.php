{{--
    Corporate company profile PDF (Dompdf). Layout follows the company's profile document:
    cover page, then numbered sections. All text comes from Company Settings.
--}}
@php
    $c = $company;
    $focus = $c->pairs('business_focus');
    $associations = $c->pairs('associations');
    $values = $c->pairs('core_values');
    $missionPoints = $c->lines('mission');
    $contact = $c->contactParts();
    $n = 0; // section number
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $c->profileName() }} - Company Profile</title>
    <style>
        @page { margin: 50px 62px 66px; }
        body { font-family: Helvetica, Arial, sans-serif; color: #2d3748; font-size: 12px; line-height: 1.5; }
        table { width: 100%; border-collapse: collapse; }
        p { margin: 0 0 9px; }
        .pre { white-space: pre-line; }
        .navy { color: #1b2d4f; }

        /* ---- cover ---- */
        .cover { text-align: center; padding-top: 58px; }
        .cover .logo { width: 235px; margin-bottom: 10px; }
        .band { background: #1b2d4f; color: #fff; padding: 46px 30px 48px; margin: 0 -8px; }
        .band .title { font-size: 35px; font-weight: bold; line-height: 1.2; letter-spacing: .3px; }
        .band .subtitle { font-size: 14px; color: #dfe6f0; margin-top: 14px; letter-spacing: .3px; }
        .band .focus { font-size: 14px; color: #dfe6f0; margin-top: 36px; }
        .band .tagline { font-size: 14px; color: #e9eef5; margin-top: 36px; line-height: 1.65; }
        .cover h3 { font-size: 19.5px; color: #1b2d4f; margin: 42px 0 8px; }
        .cover .summary { font-size: 12px; line-height: 1.55; }
        .cover .note { font-size: 12px; margin-top: 30px; }

        /* ---- sections ---- */
        h2 { font-size: 19.5px; color: #1b2d4f; margin: 18px 0 8px; }
        h2.first { margin-top: 0; }
        .grid td { border: 1px solid #d6e0ea; vertical-align: top; width: 33.33%; }
        .grid td.head { background: #edf2f7; color: #1b2d4f; font-weight: bold; font-size: 13px; text-transform: uppercase; padding: 12px 12px 10px; }
        .grid td.body { background: #f4f7fa; font-size: 11.5px; line-height: 1.5; padding: 12px; }
        .assoc td { border: 1px solid #d6e0ea; background: #f6f8fb; vertical-align: top; padding: 12px; }
        .assoc td.name { width: 36%; color: #1b2d4f; font-weight: bold; font-size: 13.5px; line-height: 1.3; }
        .assoc td.desc { font-size: 11.5px; line-height: 1.5; }
        .muted { color: #7a8899; font-size: 10.5px; line-height: 1.5; }
        .points p { margin-bottom: 8px; }
        .enquiries { text-align: center; margin-top: 22px; }
        .enquiries h3 { font-size: 19.5px; color: #1b2d4f; margin: 0 0 10px; }

        .page-break { page-break-after: always; }
        .new-page { page-break-before: always; }

    </style>
</head>
<body>

{{-- ================= COVER PAGE ================= --}}
<div class="cover page-break">
    <img src="{{ $logo }}" class="logo" alt="Logo">
    <div class="band">
        <div class="title pre">{{ $c->profile_title ?: $c->company_name }}</div>
        @if ($c->profile_subtitle)<div class="subtitle">{{ $c->profile_subtitle }}</div>@endif
        @if ($c->profile_focus_line)<div class="focus">{{ $c->profile_focus_line }}</div>@endif
        @if ($c->profile_tagline)<div class="tagline pre">{{ $c->profile_tagline }}</div>@endif
    </div>
    @if ($c->associations_summary)
        <h3>BUSINESS ASSOCIATIONS</h3>
        <div class="summary">{{ $c->associations_summary }}</div>
    @endif
    @if ($c->cover_note)<div class="note">{{ $c->cover_note }}</div>@endif
</div>

{{-- ================= PAGE 2: overview, focus, associations ================= --}}
@if ($c->about_us)
    <h2 class="first">{{ ++$n }}. Company Overview</h2>
    @foreach ($c->paragraphs('about_us') as $para)
        <p class="pre">{{ $para }}</p>
    @endforeach
@endif

@if ($focus)
    <h2 class="{{ $n === 0 ? 'first' : '' }}">{{ ++$n }}. Business Focus</h2>
    <table class="grid">
        @foreach (array_chunk($focus, 3) as $row)
            <tr>
                @foreach ($row as $item)<td class="head">{{ $item['title'] }}</td>@endforeach
                @for ($i = count($row); $i < 3; $i++)<td class="head"></td>@endfor
            </tr>
            <tr>
                @foreach ($row as $item)<td class="body">{{ $item['text'] }}</td>@endforeach
                @for ($i = count($row); $i < 3; $i++)<td class="body"></td>@endfor
            </tr>
        @endforeach
    </table>
@endif

@if ($associations)
    <h2>{{ ++$n }}. Official Business Associations</h2>
    <table class="assoc">
        @foreach ($associations as $item)
            <tr><td class="name">{{ $item['title'] }}</td><td class="desc">{{ $item['text'] }}</td></tr>
        @endforeach
    </table>
    @if ($c->associations_note)<p class="muted" style="margin-top: 12px;">{{ $c->associations_note }}</p>@endif
@endif

{{-- ================= PAGE 3: vision ... enquiries ================= --}}
<div class="{{ $n > 0 ? 'new-page' : '' }}"></div>

@if ($c->vision)
    <h2 class="first">{{ ++$n }}. Our Vision</h2>
    <p class="pre">{{ $c->vision }}</p>
@endif

@if ($missionPoints)
    <h2>{{ ++$n }}. Our Mission</h2>
    <div class="points">
        @foreach ($missionPoints as $point)
            <p>&bull; {{ $point }}</p>
        @endforeach
    </div>
@endif

@if ($values)
    <h2>{{ ++$n }}. Core Values</h2>
    <table class="grid">
        @foreach (array_chunk($values, 3) as $row)
            <tr>
                @foreach ($row as $item)<td class="head">{{ $item['title'] }}</td>@endforeach
                @for ($i = count($row); $i < 3; $i++)<td class="head"></td>@endfor
            </tr>
            <tr>
                @foreach ($row as $item)<td class="body">{{ $item['text'] }}</td>@endforeach
                @for ($i = count($row); $i < 3; $i++)<td class="body"></td>@endfor
            </tr>
        @endforeach
    </table>
@endif

@if ($c->commitment)
    <h2>{{ ++$n }}. Our Commitment</h2>
    <p class="pre">{{ $c->commitment }}</p>
@endif

@if ($c->disclaimer)
    <h2>{{ ++$n }}. Important Information</h2>
    <p class="muted pre">{{ $c->disclaimer }}</p>
@endif

<div class="enquiries">
    <h3>CORPORATE ENQUIRIES</h3>
    @if ($c->enquiries_text)<div class="pre">{{ $c->enquiries_text }}</div>@endif
</div>
@if ($contact)
    <p class="muted" style="margin-top: 16px; font-size: 10.5px;">{{ implode(' • ', $contact) }}</p>
@endif
@if ($c->about_published)
    <p class="muted" style="font-size: 11px;">Online profile: {{ route('about') }}</p>
@endif

</body>
</html>
