{{--
    Investment receipt, modelled on the printed GIT receipt book.
    Uses only table layout + simple CSS so it renders the same in the browser, in print and in Dompdf.
    Expects: $b (bill fields), $c (company snapshot), $logo (data URI), $signature (data URI|null)
--}}
<style>
    .rcpt { position: relative; font-family: Helvetica, Arial, sans-serif; color: #1d1d1d; font-size: 13px; background: #fff; }
    .rcpt table { width: 100%; border-collapse: collapse; }
    .rcpt td { vertical-align: bottom; padding: 0; }
    .rcpt .rs { font-family: "DejaVu Sans", Arial, sans-serif; }
    .rcpt .watermark { position: absolute; top: 300px; left: 50%; width: 380px; margin-left: -190px; opacity: 0.07; }
    .rcpt .title { text-align: center; font-weight: bold; font-size: 14px; letter-spacing: .3px; margin: 0 0 6px; }
    .rcpt .logo { width: 150px; }
    .rcpt .tagline { color: #0f7a1c; font-size: 8px; font-weight: bold; letter-spacing: .4px; margin-top: 2px; }
    .rcpt .cname { font-family: "DejaVu Serif", "Times New Roman", Times, serif; color: #c8102e; font-size: 23px; line-height: 1.25; text-align: center; text-transform: uppercase; vertical-align: middle; padding-left: 8px; }
    .rcpt .cmeta { text-align: center; font-size: 10.5px; color: #444; margin-top: 4px; font-family: Helvetica, Arial, sans-serif; text-transform: none; }
    .rcpt .rule { border-bottom: 2px solid #3a3a3a; height: 1px; margin: 10px 0 4px; }
    .rcpt .billno td { font-size: 12px; padding: 4px 0 6px; vertical-align: top; }
    .rcpt .fields td { padding-top: 13px; }
    .rcpt .lbl { font-weight: bold; white-space: nowrap; text-transform: uppercase; padding-right: 4px; width: 1%; }
    .rcpt .val { border-bottom: 1.5px dotted #555; padding-left: 4px; padding-bottom: 1px; font-size: 13.5px; }
    .rcpt .sub { font-weight: bold; text-transform: uppercase; white-space: nowrap; padding: 0 4px 0 12px; width: 1%; }
    .rcpt .multi { line-height: 1.45; }
    .rcpt .foot td { vertical-align: top; font-size: 11.5px; }
    .rcpt .foot .right { text-align: right; }
    .rcpt .foot-company { font-family: "DejaVu Serif", "Times New Roman", Times, serif; color: #c8102e; font-size: 10px; text-transform: uppercase; }
    .rcpt .sign-img { max-height: 48px; max-width: 170px; margin: 4px 0 2px; }
    .rcpt .sign-space { height: 40px; }
    .rcpt .auth { font-weight: bold; font-size: 12px; }
    .rcpt .footer-text { text-align: center; font-size: 10.5px; color: #555; margin-top: 10px; }
</style>

<div class="rcpt">
    <img src="{{ $logo }}" class="watermark" alt="">

    <div class="title">{{ $c['header_text'] ?: 'Investment Receipt' }}</div>

    <table>
        <tr>
            <td style="width: 160px; vertical-align: middle;">
                <img src="{{ $logo }}" class="logo" alt="Logo">
                @if (! empty($c['logo_path']) && ! empty($c['tagline']))
                    <div class="tagline">{{ $c['tagline'] }}</div>
                @endif
            </td>
            <td class="cname">
                {{ $c['company_name'] }}
                @php
                    $meta = array_filter([
                        ! empty($c['mobile']) ? 'Mob: '.$c['mobile'] : null,
                        ! empty($c['website']) ? $c['website'] : null,
                    ]);
                    $ids = array_filter([
                        ! empty($c['pan_no']) ? 'PAN: '.$c['pan_no'] : null,
                        ! empty($c['gst_no']) ? 'GSTIN: '.$c['gst_no'] : null,
                    ]);
                @endphp
                @if ($meta)<div class="cmeta">{{ implode('  |  ', $meta) }}</div>@endif
                @if ($ids)<div class="cmeta">{{ implode('  |  ', $ids) }}</div>@endif
            </td>
        </tr>
    </table>

    <div class="rule"></div>

    <table class="billno">
        <tr>
            <td><strong>RECEIPT NO:</strong> {{ $b['bill_no'] ?? 'Will be generated' }}</td>
            <td style="text-align: right;"><strong>DATE:</strong> {{ $b['bill_date_f'] }}</td>
        </tr>
    </table>

    <table class="fields"><tr><td class="lbl">Investor Name:</td><td class="val">{{ $b['investor_name'] }}</td></tr></table>
    <table class="fields"><tr><td class="lbl">Nominee Name:</td><td class="val">{{ $b['nominee_name'] ?: '-' }}</td></tr></table>
    <table class="fields"><tr><td class="lbl">Address:</td><td class="val multi">{{ $b['address'] }}</td></tr></table>
    <table class="fields"><tr><td class="lbl">Mobile No:</td><td class="val">{{ $b['mobile'] }}</td></tr></table>
    <table class="fields"><tr><td class="lbl">Email ID:</td><td class="val">{{ $b['email'] ?: '-' }}</td></tr></table>
    <table class="fields"><tr><td class="lbl">Aadhar Card No:</td><td class="val">{{ trim(chunk_split($b['aadhaar_no'], 4, ' ')) }}</td></tr></table>
    <table class="fields"><tr><td class="lbl">PAN Card No:</td><td class="val">{{ $b['pan_no'] }}</td></tr></table>
    <table class="fields"><tr><td class="lbl">Amount:</td><td class="val"><span class="rs">&#8377;</span> {{ $b['amount_f'] }}</td></tr></table>
    <table class="fields"><tr><td class="lbl">In Words:</td><td class="val">{{ $b['amount_words'] }}</td></tr></table>
    <table class="fields">
        <tr>
            <td class="lbl">Duration:</td>
            <td class="val" style="width: 30%;">{{ $b['duration'] }}</td>
            <td class="sub">From:</td>
            <td class="val">{{ $b['from_date_f'] }}</td>
            <td class="sub">To:</td>
            <td class="val">{{ $b['to_date_f'] }}</td>
        </tr>
    </table>
    <table class="fields"><tr><td class="lbl">Income Proof:</td><td class="val">{{ $b['income_proof'] }}</td></tr></table>
    <table class="fields"><tr><td class="lbl">Self Declaration:</td><td class="val multi">{{ $b['self_declaration'] }}</td></tr></table>
    @if (! empty($b['special_note']))
        <table class="fields"><tr><td class="lbl">Special Note:</td><td class="val multi">{{ $b['special_note'] }}</td></tr></table>
    @endif

    <table class="fields" style="margin-top: 14px;">
        <tr>
            <td class="lbl" style="vertical-align: bottom;">Date:</td>
            <td style="padding-left: 6px; vertical-align: bottom;">{{ $b['bill_date_f'] }}</td>
            <td style="text-align: right; vertical-align: bottom;">
                @if ($signature)
                    <img src="{{ $signature }}" class="sign-img" alt="Signature">
                @else
                    <div class="sign-space"></div>
                @endif
            </td>
        </tr>
    </table>

    <div class="rule" style="margin-top: 6px;"></div>

    <table class="foot">
        <tr>
            <td style="width: 58%;">
                @if (! empty($c['address'])){{ $c['address'] }}<br>@endif
                @if (! empty($c['email']))E-mail: {{ $c['email'] }}@endif
            </td>
            <td class="right">
                <div class="foot-company">{{ $c['company_name'] }}</div>
                @if (! empty($c['authorized_person']))<div style="margin-top: 6px;">{{ $c['authorized_person'] }}</div>@endif
                <div class="auth" style="margin-top: 6px;">AUTHORISED SIGNATORY</div>
            </td>
        </tr>
    </table>

    @if (! empty($c['footer_text']))
        <div class="footer-text">{{ $c['footer_text'] }}</div>
    @endif
</div>
