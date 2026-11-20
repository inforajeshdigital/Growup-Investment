<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** Public "About us" page and downloadable company profile PDF. */
class CompanyProfileController extends Controller
{
    public function show(Request $request)
    {
        $company = CompanySetting::current();
        $this->abortIfHidden($company, $request);

        return view('about.show', [
            'company' => $company,
            'isPreview' => ! $company->about_published,
        ]);
    }

    public function pdf(Request $request)
    {
        $company = CompanySetting::current();
        $this->abortIfHidden($company, $request);

        $html = view('about.pdf', [
            'company' => $company,
            'logo' => CompanySetting::imageDataUri($company->logo_path, true),
        ])->render();

        $pdf = Pdf::setOption(['isFontSubsettingEnabled' => true])->loadHTML($html)->setPaper('a4', 'portrait');
        $pdf->render();
        $this->decoratePages(
            $pdf->getDomPDF(),
            strtoupper($company->profileName()).' | COMPANY PROFILE',
            implode('   •   ', array_filter([$company->email, $company->mobile, $company->website])) ?: $company->profileName()
        );

        $filename = Str::slug($company->company_name ?: 'company').'-profile.pdf';

        return $request->boolean('inline') ? $pdf->stream($filename) : $pdf->download($filename);
    }

    /**
     * Brand strips drawn on the finished pages:
     * cover = navy contact strip at the bottom; other pages = navy/red bar on top + footer with page number.
     */
    private function decoratePages(\Dompdf\Dompdf $dompdf, string $label, string $coverStrip): void
    {
        $dompdf->getCanvas()->page_script(function (int $page, int $pages, $canvas, $fontMetrics) use ($label, $coverStrip) {
            $navy = [0.106, 0.176, 0.31];
            $red = [0.784, 0.063, 0.18];
            $grey = [0.48, 0.53, 0.6];
            $font = $fontMetrics->getFont('Helvetica');
            $bold = $fontMetrics->getFont('Helvetica', 'bold');
            $w = $canvas->get_width();
            $h = $canvas->get_height();

            if ($page === 1) {
                $canvas->filled_rectangle(0, $h - 44, $w, 44, $navy);
                $canvas->filled_rectangle(0, $h - 47, $w, 3, $red);
                $size = 8.5;
                $tw = $fontMetrics->getTextWidth($coverStrip, $font, $size);
                $canvas->text(($w - $tw) / 2, $h - 27, $coverStrip, $font, $size, [0.86, 0.9, 0.95]);

                return;
            }

            // Top brand bar
            $canvas->filled_rectangle(0, 0, $w, 7, $navy);
            $canvas->filled_rectangle(0, 7, $w, 2, $red);

            // Footer
            $y = $h - 34;
            $pageText = 'Page '.$page.' of '.$pages;
            $canvas->filled_rectangle(40, $y - 10, 36, 1.5, $red);
            $canvas->line(76, $y - 9.25, $w - 40, $y - 9.25, [0.86, 0.89, 0.93], 0.75);
            $canvas->text(40, $y, $label, $bold, 7, $navy);
            $canvas->text($w - 40 - $fontMetrics->getTextWidth($pageText, $font, 7.5), $y, $pageText, $font, 7.5, $grey);
        });
    }

    /** Public company logo (for the About page and link previews on WhatsApp etc.). */
    public function logo()
    {
        $path = CompanySetting::current()->logo_path;

        if ($path && Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->response($path, null, ['Cache-Control' => 'public, max-age=86400']);
        }

        return response()->file(public_path('images/logo.png'), ['Cache-Control' => 'public, max-age=86400']);
    }

    /** Until the admin publishes it, only the logged-in admin can see the page (as a preview). */
    private function abortIfHidden(CompanySetting $company, Request $request): void
    {
        abort_unless($company->about_published || $request->user(), 404);
    }
}
