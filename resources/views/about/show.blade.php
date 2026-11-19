@php
    $c = $company;
    $services = $c->lines('services');
    $highlights = $c->lines('highlights');
    $people = $c->people();
    $summary = \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', $c->about_us ?: $c->tagline ?: $c->company_name)), 180);
    $socials = array_filter([
        'facebook' => \App\Models\CompanySetting::fullUrl($c->facebook),
        'instagram' => \App\Models\CompanySetting::fullUrl($c->instagram),
        'linkedin' => \App\Models\CompanySetting::fullUrl($c->linkedin),
    ]);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About Us - {{ $c->company_name }}</title>
    <meta name="description" content="{{ $summary }}">
    @if ($isPreview)<meta name="robots" content="noindex, nofollow">@endif
    {{-- Link preview on WhatsApp / Facebook / LinkedIn --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $c->company_name }}">
    <meta property="og:description" content="{{ $summary }}">
    <meta property="og:url" content="{{ route('about') }}">
    <meta property="og:image" content="{{ route('company.logo') }}">
    <meta name="twitter:card" content="summary">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --blue: #0b5cc4; --blue-dark: #083f88; --red: #c8102e; --green: #0f7a1c; }
        body { background: #f5f7fb; color: #1f2937; }
        .hero { background: linear-gradient(135deg, var(--blue) 0%, var(--blue-dark) 100%); color: #fff; padding: 56px 0 96px; }
        .hero .logo-box { background: #fff; border-radius: 16px; padding: 14px 18px; display: inline-block; box-shadow: 0 8px 24px rgba(0,0,0,.18); }
        .hero .logo-box img { max-height: 90px; max-width: 220px; }
        .hero h1 { font-family: Georgia, "Times New Roman", serif; letter-spacing: .5px; }
        .hero .tagline { color: #cfe2ff; letter-spacing: 2px; text-transform: uppercase; font-size: .85rem; }
        .lift { margin-top: -60px; }
        .section-card { background: #fff; border-radius: 14px; box-shadow: 0 2px 14px rgba(15,23,42,.07); padding: 28px; }
        .section-title { font-weight: 700; color: var(--blue-dark); margin-bottom: 14px; }
        .section-title::after { content: ""; display: block; width: 44px; height: 3px; background: var(--red); margin-top: 8px; border-radius: 2px; }
        .mv-card { border-left: 4px solid var(--blue); }
        .mv-card.vision { border-left-color: var(--green); }
        .service { background: #f1f6ff; border-radius: 10px; padding: 14px 16px; height: 100%; }
        .service i { color: var(--blue); }
        .tick i { color: var(--green); }
        .person { text-align: center; }
        .person .avatar { width: 64px; height: 64px; border-radius: 50%; background: var(--blue); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700; }
        .contact-item i { color: var(--blue); width: 22px; }
        .btn-git { background: var(--red); color: #fff; border: 0; }
        .btn-git:hover { background: #a50d26; color: #fff; }
        .preview-bar { background: #fff3cd; color: #664d03; text-align: center; padding: 8px; font-size: .9rem; }
        footer { color: #6b7280; font-size: .85rem; }
        .white-space { white-space: pre-line; }
    </style>
</head>
<body>
@if ($isPreview)
    <div class="preview-bar"><i class="bi bi-eye-slash"></i> Preview: this page is hidden from the public. Turn on "Make About page public" in Company Settings to share it.</div>
@endif

<header class="hero text-center">
    <div class="container">
        <div class="logo-box mb-3"><img src="{{ route('company.logo') }}" alt="{{ $c->company_name }} logo"></div>
        <h1 class="h2 fw-bold mb-2">{{ $c->company_name }}</h1>
        @if ($c->tagline)<div class="tagline mb-3">{{ $c->tagline }}</div>@endif
        <div class="d-flex flex-wrap justify-content-center gap-2">
            @if ($c->mobile)<a href="tel:{{ preg_replace('/[^\d+]/', '', $c->mobile) }}" class="btn btn-light btn-sm"><i class="bi bi-telephone"></i> Call</a>@endif
            @if ($c->whatsappUrl())<a href="{{ $c->whatsappUrl() }}" target="_blank" rel="noopener" class="btn btn-success btn-sm"><i class="bi bi-whatsapp"></i> WhatsApp</a>@endif
            @if ($c->email)<a href="mailto:{{ $c->email }}" class="btn btn-light btn-sm"><i class="bi bi-envelope"></i> Email</a>@endif
            <a href="{{ route('about.pdf') }}" class="btn btn-git btn-sm"><i class="bi bi-file-earmark-pdf"></i> Company Profile PDF</a>
            <button type="button" class="btn btn-outline-light btn-sm" id="shareBtn"><i class="bi bi-share"></i> Share</button>
        </div>
    </div>
</header>

<main class="container lift pb-5">
    <div class="row g-4">
        <div class="col-lg-8">
            @if ($c->about_us)
                <section class="section-card mb-4">
                    <h2 class="h4 section-title">About Us</h2>
                    <p class="mb-0 white-space">{{ $c->about_us }}</p>
                </section>
            @endif

            @if ($c->mission || $c->vision)
                <div class="row g-4 mb-4">
                    @if ($c->mission)
                        <div class="{{ $c->vision ? 'col-md-6' : 'col-12' }}">
                            <section class="section-card mv-card h-100">
                                <h2 class="h5 fw-bold"><i class="bi bi-bullseye text-primary"></i> Our Mission</h2>
                                <p class="mb-0 white-space">{{ $c->mission }}</p>
                            </section>
                        </div>
                    @endif
                    @if ($c->vision)
                        <div class="{{ $c->mission ? 'col-md-6' : 'col-12' }}">
                            <section class="section-card mv-card vision h-100">
                                <h2 class="h5 fw-bold"><i class="bi bi-eye text-success"></i> Our Vision</h2>
                                <p class="mb-0 white-space">{{ $c->vision }}</p>
                            </section>
                        </div>
                    @endif
                </div>
            @endif

            @if ($services)
                <section class="section-card mb-4">
                    <h2 class="h4 section-title">Our Services</h2>
                    <div class="row g-3">
                        @foreach ($services as $service)
                            <div class="col-sm-6"><div class="service"><i class="bi bi-check2-circle me-2"></i>{{ $service }}</div></div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($highlights)
                <section class="section-card mb-4">
                    <h2 class="h4 section-title">Why Choose Us</h2>
                    <ul class="list-unstyled mb-0 tick">
                        @foreach ($highlights as $item)
                            <li class="mb-2"><i class="bi bi-patch-check-fill me-2"></i>{{ $item }}</li>
                        @endforeach
                    </ul>
                </section>
            @endif

            @if ($people)
                <section class="section-card mb-4">
                    <h2 class="h4 section-title">Our Team</h2>
                    <div class="row g-4">
                        @foreach ($people as $person)
                            <div class="col-6 col-md-4 person">
                                <div class="avatar mb-2">{{ mb_strtoupper(mb_substr($person['name'], 0, 1)) }}</div>
                                <div class="fw-semibold">{{ $person['name'] }}</div>
                                @if ($person['role'])<div class="text-muted small">{{ $person['role'] }}</div>@endif
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if (! $c->about_us && ! $services && ! $highlights && ! $c->mission && ! $c->vision && ! $people)
                <section class="section-card mb-4 text-muted">Company details will be added soon.</section>
            @endif
        </div>

        <div class="col-lg-4">
            <aside class="section-card mb-4">
                <h2 class="h5 section-title">Contact</h2>
                @if ($c->address)
                    <div class="contact-item d-flex mb-3"><i class="bi bi-geo-alt-fill mt-1"></i><div>{{ $c->address }}@if ($c->mapUrl())<br><a href="{{ $c->mapUrl() }}" target="_blank" rel="noopener" class="small">View on Google Maps</a>@endif</div></div>
                @endif
                @if ($c->mobile)
                    <div class="contact-item d-flex mb-3"><i class="bi bi-telephone-fill mt-1"></i><a href="tel:{{ preg_replace('/[^\d+]/', '', $c->mobile) }}">{{ $c->mobile }}</a></div>
                @endif
                @if ($c->email)
                    <div class="contact-item d-flex mb-3"><i class="bi bi-envelope-fill mt-1"></i><a href="mailto:{{ $c->email }}" class="text-break">{{ $c->email }}</a></div>
                @endif
                @if ($c->websiteUrl())
                    <div class="contact-item d-flex mb-3"><i class="bi bi-globe2 mt-1"></i><a href="{{ $c->websiteUrl() }}" target="_blank" rel="noopener" class="text-break">{{ $c->website }}</a></div>
                @endif
                @if ($socials)
                    <div class="d-flex gap-2 mt-2">
                        @foreach ($socials as $network => $link)
                            <a href="{{ $link }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm" aria-label="{{ ucfirst($network) }}"><i class="bi bi-{{ $network }}"></i></a>
                        @endforeach
                    </div>
                @endif
            </aside>

            @if ($c->cin_no || $c->gst_no || $c->established_year)
                <aside class="section-card mb-4">
                    <h2 class="h5 section-title">Company Details</h2>
                    <table class="table table-sm mb-0 small">
                        @if ($c->established_year)<tr><th class="fw-semibold">Established</th><td>{{ $c->established_year }}</td></tr>@endif
                        @if ($c->cin_no)<tr><th class="fw-semibold">CIN</th><td class="text-break">{{ $c->cin_no }}</td></tr>@endif
                        @if ($c->gst_no)<tr><th class="fw-semibold">GSTIN</th><td>{{ $c->gst_no }}</td></tr>@endif
                    </table>
                </aside>
            @endif
        </div>
    </div>
</main>

<footer class="text-center pb-4">
    &copy; {{ date('Y') }} {{ $c->company_name }}. All rights reserved.
</footer>

<div class="toast-container position-fixed bottom-0 start-50 translate-middle-x p-3">
    <div id="copiedToast" class="toast text-bg-dark border-0" role="status"><div class="toast-body">Link copied</div></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('shareBtn').addEventListener('click', function () {
    var data = { title: @json($c->company_name), text: @json($c->company_name.' - About Us'), url: @json(route('about')) };
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
