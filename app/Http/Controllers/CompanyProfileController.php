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
        $this->addFooter($pdf->getDomPDF(), strtoupper($company->profileName()).' | COMPANY PROFILE');

        $filename = Str::slug($company->company_name ?: 'company').'-profile.pdf';

        return $request->boolean('inline') ? $pdf->stream($filename) : $pdf->download($filename);
    }

    /** Footer line with the company label and page number on every page except the cover. */
    private function addFooter(\Dompdf\Dompdf $dompdf, string $label): void
    {
        $dompdf->getCanvas()->page_script(function (int $page, int $pages, $canvas, $fontMetrics) use ($label) {
            if ($page === 1) {
                return;
            }

            $font = $fontMetrics->getFont('Helvetica');
            $grey = [0.48, 0.53, 0.6];
            $width = $canvas->get_width();
            $y = $canvas->get_height() - 36;
            $pageText = 'Page '.$page;

            $canvas->line(40, $y - 8, $width - 40, $y - 8, [0.88, 0.91, 0.94], 0.75);
            $canvas->text(40, $y, $label, $font, 7.5, $grey);
            $canvas->text($width - 40 - $fontMetrics->getTextWidth($pageText, $font, 7.5), $y, $pageText, $font, 7.5, $grey);
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
