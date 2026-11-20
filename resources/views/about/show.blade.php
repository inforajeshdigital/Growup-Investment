@php
    $c = $company;
    $focus = $c->pairs('business_focus');
    $associations = $c->pairs('associations');
    $values = $c->pairs('core_values');
    $missionPoints = $c->lines('mission');
    $summary = \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', $c->about_us ?: $c->profile_tagline ?: $c->company_name)), 180);
    $socials = array_filter([
        'facebook' => \App\Models\CompanySetting::fullUrl($c->facebook),
        'instagram' => \App\Models\CompanySetting::fullUrl($c->instagram),
        'linkedin' => \App\Models\CompanySetting::fullUrl($c->linkedin),
    ]);
    $phone = preg_replace('/[^\d+]/', '', (string) $c->mobile);
    $n = 0;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $c->profileName() }} - Company Profile</title>
    <meta name="description" content="{{ $summary }}">
    @if ($isPreview)<meta name="robots" content="noindex, nofollow">@endif
    {{-- Link preview on WhatsApp / Facebook / LinkedIn --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $c->profileName() }} - {{ $c->profile_subtitle ?: 'Company Profile' }}">
    <meta property="og:description" content="{{ $summary }}">
    <meta property="og:url" content="{{ route('about') }}">
    <meta property="og:image" content="{{ route('company.logo') }}">
    <meta name="twitter:card" content="summary">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --navy: #1b2d4f; --navy-2: #24406f; --line: #d6e0ea; --soft: #edf2f7; --soft-2: #f4f7fa; --muted: #6b7a8c; --red: #c8102e; }
        body { background: #f5f7fb; color: #2d3748; font-family: Helvetica, Arial, sans-serif; }
        .top { background: #fff; padding: 28px 0 0; text-align: center; }
        .top img { max-width: 190px; max-height: 120px; }
        .band { background: var(--navy); color: #fff; text-align: center; padding: 44px 16px 48px; }
        .band h1 { font-weight: 700; font-size: clamp(1.7rem, 4.5vw, 2.6rem); line-height: 1.2; white-space: pre-line; margin: 0; }
        .band .subtitle { color: #dfe6f0; letter-spacing: .5px; margin-top: 12px; }
        .band .focus { color: #dfe6f0; margin-top: 22px; letter-spacing: .5px; }
        .band .tagline { color: #e9eef5; margin-top: 22px; white-space: pre-line; }
        .assoc-summary { background: #fff; text-align: center; padding: 30px 16px 34px; border-bottom: 1px solid var(--line); }
        .assoc-summary h2 { color: var(--navy); font-weight: 700; font-size: 1.3rem; }
        .content { max-width: 960px; }
        .sec { background: #fff; border-radius: 10px; padding: 26px 28px; margin-bottom: 22px; box-shadow: 0 1px 8px rgba(15,23,42,.06); }
        .sec h2 { color: var(--navy); font-weight: 700; font-size: 1.45rem; margin-bottom: 14px; }
        .grid3 { display: grid; grid-template-columns: repeat(3, 1fr); border: 1px solid var(--line); }
        .grid3 .cell { border: 1px solid var(--line); margin: -1px 0 0 -1px; display: flex; flex-direction: column; }
        .grid3 .cell .b { flex: 1; }
        .grid3 .cell .h { background: var(--soft); color: var(--navy); font-weight: 700; text-transform: uppercase; padding: 12px 14px; }
        .grid3 .cell .b { background: var(--soft-2); padding: 12px 14px; font-size: .92rem; }
        @media (max-width: 767px) { .grid3 { grid-template-columns: 1fr; } .sec { padding: 20px 16px; } }
        .assoc { border: 1px solid var(--line); }
        .assoc .row-item { display: flex; border-top: 1px solid var(--line); background: #f6f8fb; }
        .assoc .row-item:first-child { border-top: 0; }
        .assoc .name { flex: 0 0 36%; padding: 12px 14px; color: var(--navy); font-weight: 700; border-right: 1px solid var(--line); }
        .assoc .desc { flex: 1; padding: 12px 14px; font-size: .92rem; }
        @media (max-width: 575px) { .assoc .row-item { flex-direction: column; } .assoc .name { border-right: 0; } }
        .muted { color: var(--muted); font-size: .85rem; }
        .pre { white-space: pre-line; }
        .enq { text-align: center; }
        .enq h2 { color: var(--navy); font-weight: 700; }
        .contact a { text-decoration: none; }
        .btn-navy { background: var(--navy); color: #fff; }
        .btn-navy:hover { background: var(--navy-2); color: #fff; }
        .actions .btn { margin: 4px; }
        .preview-bar { background: #fff3cd; color: #664d03; text-align: center; padding: 8px; font-size: .9rem; }
        footer { color: var(--muted); font-size: .85rem; }
    </style>
</head>
<body>
@if ($isPreview)
    <div class="preview-bar"><i class="bi bi-eye-slash"></i> Preview: this page is hidden from the public. Turn on "Make About page public" in Company Settings to share it.</div>
@endif

<div class="top"><img src="{{ route('company.logo') }}" alt="{{ $c->company_name }} logo"></div>

<header class="band">
    <h1>{{ $c->profile_title ?: $c->company_name }}</h1>
    @if ($c->profile_subtitle)<div class="subtitle">{{ $c->profile_subtitle }}</div>@endif
    @if ($c->profile_focus_line)<div class="focus">{{ $c->profile_focus_line }}</div>@endif
    @if ($c->profile_tagline)<div class="tagline">{{ $c->profile_tagline }}</div>@endif
    <div class="actions mt-4">
        <a href="{{ route('about.pdf') }}" class="btn btn-light btn-sm"><i class="bi bi-file-earmark-pdf"></i> Download Company Profile</a>
        @if ($c->whatsappUrl())<a href="{{ $c->whatsappUrl() }}" target="_blank" rel="noopener" class="btn btn-success btn-sm"><i class="bi bi-whatsapp"></i> WhatsApp</a>@endif
        <button type="button" class="btn btn-outline-light btn-sm" id="shareBtn"><i class="bi bi-share"></i> Share</button>
    </div>
</header>

@if ($c->associations_summary || $c->cover_note)
    <section class="assoc-summary">
        @if ($c->associations_summary)
            <h2>BUSINESS ASSOCIATIONS</h2>
            <p class="mb-0 mx-auto" style="max-width: 760px;">{{ $c->associations_summary }}</p>
        @endif
        @if ($c->cover_note)<p class="mt-3 mb-0">{{ $c->cover_note }}</p>@endif
    </section>
@endif

<main class="container content py-4">
    @if ($c->about_us)
        <section class="sec">
            <h2>{{ ++$n }}. Company Overview</h2>
            @foreach ($c->paragraphs('about_us') as $para)
                <p class="pre">{{ $para }}</p>
            @endforeach
        </section>
    @endif

    @if ($focus)
        <section class="sec">
            <h2>{{ ++$n }}. Business Focus</h2>
            <div class="grid3">
                @foreach ($focus as $item)
                    <div class="cell"><div class="h">{{ $item['title'] }}</div><div class="b">{{ $item['text'] }}</div></div>
                @endforeach
            </div>
        </section>
    @endif

    @if ($associations)
        <section class="sec">
            <h2>{{ ++$n }}. Official Business Associations</h2>
            <div class="assoc">
                @foreach ($associations as $item)
                    <div class="row-item"><div class="name">{{ $item['title'] }}</div><div class="desc">{{ $item['text'] }}</div></div>
                @endforeach
            </div>
            @if ($c->associations_note)<p class="muted mt-3 mb-0">{{ $c->associations_note }}</p>@endif
        </section>
    @endif

    @if ($c->vision)
        <section class="sec">
            <h2>{{ ++$n }}. Our Vision</h2>
            <p class="mb-0 pre">{{ $c->vision }}</p>
        </section>
    @endif

    @if ($missionPoints)
        <section class="sec">
            <h2>{{ ++$n }}. Our Mission</h2>
            @foreach ($missionPoints as $point)
                <p class="{{ $loop->last ? 'mb-0' : '' }}">&bull; {{ $point }}</p>
            @endforeach
        </section>
    @endif

    @if ($values)
        <section class="sec">
            <h2>{{ ++$n }}. Core Values</h2>
            <div class="grid3">
                @foreach ($values as $item)
                    <div class="cell"><div class="h">{{ $item['title'] }}</div><div class="b">{{ $item['text'] }}</div></div>
                @endforeach
            </div>
        </section>
    @endif

    @if ($c->commitment)
        <section class="sec">
            <h2>{{ ++$n }}. Our Commitment</h2>
            <p class="mb-0 pre">{{ $c->commitment }}</p>
        </section>
    @endif

    @if ($c->disclaimer)
        <section class="sec">
            <h2>{{ ++$n }}. Important Information</h2>
            <p class="muted mb-0 pre">{{ $c->disclaimer }}</p>
        </section>
    @endif

    <section class="sec enq">
        <h2>CORPORATE ENQUIRIES</h2>
        @if ($c->enquiries_text)<p class="pre">{{ $c->enquiries_text }}</p>@endif
        <div class="contact d-flex flex-wrap justify-content-center gap-3 mt-3">
            @if ($c->address)<span><i class="bi bi-geo-alt-fill text-primary"></i> @if ($c->mapUrl())<a href="{{ $c->mapUrl() }}" target="_blank" rel="noopener">{{ $c->address }}</a>@else{{ $c->address }}@endif</span>@endif
            @if ($c->email)<span><i class="bi bi-envelope-fill text-primary"></i> <a href="mailto:{{ $c->email }}">{{ $c->email }}</a></span>@endif
            @if ($c->websiteUrl())<span><i class="bi bi-globe2 text-primary"></i> <a href="{{ $c->websiteUrl() }}" target="_blank" rel="noopener">{{ $c->website }}</a></span>@endif
            @if ($c->mobile)<span><i class="bi bi-telephone-fill text-primary"></i> <a href="tel:{{ $phone }}">{{ $c->mobile }}</a></span>@endif
            @if ($c->whatsappUrl())<span><i class="bi bi-whatsapp text-success"></i> <a href="{{ $c->whatsappUrl() }}" target="_blank" rel="noopener">{{ $c->whatsapp }}</a></span>@endif
        </div>
        @if ($c->cin_no || $c->gst_no || $c->established_year)
            <div class="muted mt-3">
                {{ implode('  •  ', array_filter([
                    $c->established_year ? 'Established: '.$c->established_year : null,
                    $c->cin_no ? 'CIN: '.$c->cin_no : null,
                    $c->gst_no ? 'GSTIN: '.$c->gst_no : null,
                ])) }}
            </div>
        @endif
        @if ($socials)
            <div class="d-flex justify-content-center gap-2 mt-3">
                @foreach ($socials as $network => $link)
                    <a href="{{ $link }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm" aria-label="{{ ucfirst($network) }}"><i class="bi bi-{{ $network }}"></i></a>
                @endforeach
            </div>
        @endif
        <a href="{{ route('about.pdf') }}" class="btn btn-navy mt-4"><i class="bi bi-file-earmark-pdf"></i> Download Company Profile (PDF)</a>
    </section>
</main>

<footer class="text-center pb-4">&copy; {{ date('Y') }} {{ $c->company_name }}. All rights reserved.</footer>

<div class="toast-container position-fixed bottom-0 start-50 translate-middle-x p-3">
    <div id="copiedToast" class="toast text-bg-dark border-0" role="status"><div class="toast-body">Link copied</div></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('shareBtn').addEventListener('click', function () {
    var data = { title: @json($c->profileName()), text: @json($c->profileName().' - Company Profile'), url: @json(route('about')) };
    if (navigator.share) { navigator.share(data).catch(function () {}); return; }
    if (navigator.clipboard) {
        navigator.clipboard.writeText(data.url).then(function () { bootstrap.Toast.getOrCreateInstance(document.getElementById('copiedToast')).show(); });
    } else {
        window.open('https://wa.me/?text=' + encodeURIComponent(data.text + ': ' + data.url), '_blank');
    }
});
</script>
</body>
</html>
