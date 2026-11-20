{{--
    Corporate company profile PDF (Dompdf). Same content/sections as the company's profile document,
    with a branded design. All text comes from Company Settings.
    Top/bottom brand strips and page numbers are drawn in CompanyProfileController::decoratePages().
--}}
@php
    $c = $company;
    $focus = $c->pairs('business_focus');
    $associations = $c->pairs('associations');
    $values = $c->pairs('core_values');
    $missionPoints = $c->lines('mission');
    $contact = $c->contactParts();
    $accents = ['#0b5cc4', '#c8102e', '#0f7a1c'];
    $n = 0;
    // Numbered section heading: navy number badge + title + red/grey underline
    $head = function (string $title) use (&$n) {
        $n++;
        return '<table class="sh"><tr><td class="num">'.sprintf('%02d', $n).'</td><td class="ttl">'.e($title).'</td></tr></table>'
            .'<table class="uline"><tr><td class="r"></td><td class="g"></td></tr></table>';
    };
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $c->profileName() }} - Company Profile</title>
    <style>
        @page { margin: 50px 56px 64px; }
        body { font-family: Helvetica, Arial, sans-serif; color: #334155; font-size: 11.5px; line-height: 1.55; }
        table { width: 100%; border-collapse: collapse; }
        p { margin: 0 0 8px; }
        .pre { white-space: pre-line; }
        .dj { font-family: "DejaVu Sans", sans-serif; }

        /* ================= cover ================= */
        .cover { text-align: center; }
        .cover .logo-wrap { padding: 72px 0 34px; }
        .cover .logo { width: 250px; }
        .band { background: #1b2d4f; color: #fff; margin: 0 -56px; padding: 62px 70px 64px; }
        .band .title { font-size: 36px; font-weight: bold; line-height: 1.18; letter-spacing: .5px; }
        .band .divider { width: 70px; height: 4px; background: #c8102e; margin: 18px auto 16px; }
        .band .subtitle { font-size: 12.5px; color: #c9d6ea; letter-spacing: 4px; }
        .band .focus { display: inline-block; margin-top: 32px; padding: 8px 22px; border: 1px solid #5d7398; color: #fff; font-size: 12px; font-weight: bold; letter-spacing: 1.5px; }
        .band .tagline { font-size: 14px; color: #e3eaf5; margin-top: 30px; line-height: 1.65; font-style: italic; }
        .red-strip { margin: 0 -56px; height: 5px; background: #c8102e; }
        .assoc-card { margin: 52px 20px 0; border: 1px solid #dbe4f0; border-top: 4px solid #1b2d4f; background: #f6f9fd; padding: 18px 26px 20px; }
        .assoc-card .label { font-size: 15px; font-weight: bold; color: #1b2d4f; letter-spacing: 2px; }
        .assoc-card .text { font-size: 11.5px; color: #475569; margin-top: 8px; line-height: 1.6; }
        .cover .note { margin-top: 36px; font-size: 11px; color: #64748b; letter-spacing: .5px; }
        .cover .note .dash { color: #c8102e; }

        /* ================= section headings ================= */
        .sec { page-break-inside: avoid; margin-bottom: 18px; }
        .sh td { vertical-align: middle; }
        .sh .num { width: 32px; height: 30px; background: #1b2d4f; color: #fff; font-weight: bold; font-size: 13px; text-align: center; }
        .sh .ttl { padding-left: 11px; color: #1b2d4f; font-size: 18.5px; font-weight: bold; }
        .uline { margin: 6px 0 12px; }
        .uline td { height: 2px; padding: 0; }
        .uline .r { width: 54px; background: #c8102e; }
        .uline .g { background: #e3e9f2; }

        /* ================= content blocks ================= */
        .lead p:first-child { font-size: 12.5px; color: #1e293b; }
        .cards { border-collapse: separate; border-spacing: 8px 0; margin: 0 -8px; }
        .cards td { vertical-align: top; background: #f5f8fc; border: 1px solid #e1e8f2; padding: 12px 13px 13px; }
        .cards .ct { font-weight: bold; color: #1b2d4f; font-size: 12px; letter-spacing: .8px; text-transform: uppercase; margin-bottom: 6px; }
        .cards .cd { font-size: 10.5px; line-height: 1.5; color: #475569; }
        .cards-gap { height: 8px; }
        .assoc td { vertical-align: middle; padding: 11px 14px; border-bottom: 3px solid #fff; }
        .assoc .an { width: 37%; background: #1b2d4f; color: #fff; font-weight: bold; font-size: 12.5px; line-height: 1.3; }
        .assoc .ad { background: #eef3f9; font-size: 10.8px; color: #334155; }
        .note-sm { color: #7a8899; font-size: 9.5px; line-height: 1.5; margin-top: 8px; font-style: italic; }
        .quote { background: #eef3f9; border-left: 5px solid #1b2d4f; padding: 14px 18px 14px 16px; }
        .quote td { vertical-align: top; }
        .quote .qm { width: 30px; font-family: "DejaVu Serif", serif; color: #c8102e; font-size: 34px; line-height: 1; }
        .quote .qt { font-size: 12.5px; color: #1e293b; font-style: italic; line-height: 1.6; }
        .points td { vertical-align: top; padding: 5px 0; border-bottom: 1px solid #edf1f6; }
        .points tr:last-child td { border-bottom: 0; }
        .points .tick { width: 22px; font-family: "DejaVu Sans", sans-serif; color: #0f7a1c; font-size: 12px; font-weight: bold; line-height: 1.3; }
        .commit { border: 1px solid #dbe4f0; background: #fbfcfe; padding: 13px 16px; }
        .info { border: 1px dashed #cbd5e1; background: #f8fafc; padding: 11px 14px; color: #64748b; font-size: 9.6px; line-height: 1.55; }
        .enq { background: #1b2d4f; color: #fff; text-align: center; padding: 20px 26px 18px; page-break-inside: avoid; margin-top: 4px; }
        .enq .eh { font-size: 17px; font-weight: bold; letter-spacing: 3px; }
        .enq .divider { width: 56px; height: 3px; background: #c8102e; margin: 9px auto 12px; }
        .enq .et { color: #dbe4f0; font-size: 11px; line-height: 1.6; }
        .enq .contact { margin-top: 12px; padding-top: 10px; border-top: 1px solid #34496f; color: #ffffff; font-size: 10.5px; }
        .enq .link { color: #9fb6d9; font-size: 9.5px; margin-top: 6px; }

        .page-break { page-break-after: always; }
    </style>
</head>
<body>

{{-- ================= COVER PAGE ================= --}}
<div class="cover page-break">
    <div class="logo-wrap"><img src="{{ $logo }}" class="logo" alt="Logo"></div>

    <div class="band">
        <div class="title pre">{{ $c->profile_title ?: $c->company_name }}</div>
        <div class="divider"></div>
        @if ($c->profile_subtitle)<div class="subtitle">{{ $c->profile_subtitle }}</div>@endif
        @if ($c->profile_focus_line)<div class="focus">{{ $c->profile_focus_line }}</div>@endif
        @if ($c->profile_tagline)<div class="tagline pre">{{ $c->profile_tagline }}</div>@endif
    </div>
    <div class="red-strip"></div>

    @if ($c->associations_summary)
        <div class="assoc-card">
            <div class="label">BUSINESS ASSOCIATIONS</div>
            <div class="text">{{ $c->associations_summary }}</div>
        </div>
    @endif
    @if ($c->cover_note)
        <div class="note"><span class="dash">&mdash;&mdash;</span>&nbsp;&nbsp; {{ $c->cover_note }} &nbsp;&nbsp;<span class="dash">&mdash;&mdash;</span></div>
    @endif
</div>

{{-- ================= SECTIONS ================= --}}
@if ($c->about_us)
    <div class="sec lead">
        {!! $head('Company Overview') !!}
        @foreach ($c->paragraphs('about_us') as $para)
            <p class="pre">{{ $para }}</p>
        @endforeach
    </div>
@endif

@if ($focus)
    <div class="sec">
        {!! $head('Business Focus') !!}
        @foreach (array_chunk($focus, 3) as $r => $row)
            @if ($r > 0)<div class="cards-gap"></div>@endif
            <table class="cards">
                <tr>
                    @foreach ($row as $i => $item)
                        <td style="width: 33.33%; border-top: 4px solid {{ $accents[$i % 3] }};">
                            <div class="ct">{{ $item['title'] }}</div>
                            <div class="cd">{{ $item['text'] }}</div>
                        </td>
                    @endforeach
                    @for ($i = count($row); $i < 3; $i++)<td style="width: 33.33%; background: none; border: 0;"></td>@endfor
                </tr>
            </table>
        @endforeach
    </div>
@endif

@if ($associations)
    <div class="sec">
        {!! $head('Official Business Associations') !!}
        <table class="assoc">
            @foreach ($associations as $item)
                <tr><td class="an">{{ $item['title'] }}</td><td class="ad">{{ $item['text'] }}</td></tr>
            @endforeach
        </table>
        @if ($c->associations_note)<div class="note-sm">{{ $c->associations_note }}</div>@endif
    </div>
@endif

@if ($c->vision)
    <div class="sec">
        {!! $head('Our Vision') !!}
        <table class="quote"><tr><td class="qm">&ldquo;</td><td class="qt pre">{{ $c->vision }}</td></tr></table>
    </div>
@endif

@if ($missionPoints)
    <div class="sec">
        {!! $head('Our Mission') !!}
        <table class="points">
            @foreach ($missionPoints as $point)
                <tr><td class="tick">&#10004;</td><td>{{ $point }}</td></tr>
            @endforeach
        </table>
    </div>
@endif

@if ($values)
    <div class="sec">
        {!! $head('Core Values') !!}
        @foreach (array_chunk($values, 3) as $r => $row)
            @if ($r > 0)<div class="cards-gap"></div>@endif
            <table class="cards">
                <tr>
                    @foreach ($row as $i => $item)
                        <td style="width: 33.33%; border-top: 4px solid {{ $accents[($i + $r) % 3] }};">
                            <div class="ct">{{ $item['title'] }}</div>
                            <div class="cd">{{ $item['text'] }}</div>
                        </td>
                    @endforeach
                    @for ($i = count($row); $i < 3; $i++)<td style="width: 33.33%; background: none; border: 0;"></td>@endfor
                </tr>
            </table>
        @endforeach
    </div>
@endif

@if ($c->commitment)
    <div class="sec">
        {!! $head('Our Commitment') !!}
        <div class="commit pre">{{ $c->commitment }}</div>
    </div>
@endif

@if ($c->disclaimer)
    <div class="sec">
        {!! $head('Important Information') !!}
        <div class="info pre">{{ $c->disclaimer }}</div>
    </div>
@endif

<div class="enq">
    <div class="eh">CORPORATE ENQUIRIES</div>
    <div class="divider"></div>
    @if ($c->enquiries_text)<div class="et pre">{{ $c->enquiries_text }}</div>@endif
    @if ($contact)<div class="contact">{{ implode('   •   ', $contact) }}</div>@endif
    @if ($c->about_published)<div class="link">{{ route('about') }}</div>@endif
</div>

</body>
</html>
