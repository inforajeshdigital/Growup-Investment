<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\CompanySetting;
use App\Support\AmountInWords;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BillController extends Controller
{
    public const DEFAULT_DECLARATION = 'I hereby declare that the information given above is true and correct to the best of my knowledge, and the invested amount is from my own legal source of income.';

    private const DRAFT_KEY = 'bill_draft';

    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));

        $bills = Bill::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('investor_name', 'like', "%{$search}%")
                        ->orWhere('bill_no', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('bills.index', compact('bills', 'search'));
    }

    public function create(Request $request)
    {
        $draft = $request->boolean('edit') ? $request->session()->get(self::DRAFT_KEY) : null;

        if (! $draft) {
            $request->session()->forget(self::DRAFT_KEY);
        }

        $defaults = $draft ?? [
            'bill_date' => now()->format('Y-m-d'),
            'self_declaration' => self::DEFAULT_DECLARATION,
        ];

        return view('bills.create', [
            'd' => $defaults,
            'hasDraftFile' => ! empty($draft['income_proof_tmp']),
            'draftFileName' => $draft['income_proof_original_name'] ?? null,
        ]);
    }

    /** Validate the form and keep it in the session for preview. */
    public function preview(Request $request)
    {
        $data = $this->validated($request);
        $old = $request->session()->get(self::DRAFT_KEY, []);

        if ($request->hasFile('income_proof_file')) {
            if (! empty($old['income_proof_tmp'])) {
                Storage::disk('local')->delete($old['income_proof_tmp']);
            }
            $file = $request->file('income_proof_file');
            $data['income_proof_tmp'] = $file->store('tmp', 'local');
            $data['income_proof_original_name'] = $file->getClientOriginalName();
        } elseif ($request->boolean('keep_income_proof_file') && ! empty($old['income_proof_tmp'])) {
            $data['income_proof_tmp'] = $old['income_proof_tmp'];
            $data['income_proof_original_name'] = $old['income_proof_original_name'] ?? null;
        }

        $request->session()->put(self::DRAFT_KEY, $data);

        return redirect()->route('bills.preview.show');
    }

    public function showPreview(Request $request)
    {
        $draft = $request->session()->get(self::DRAFT_KEY);
        if (! $draft) {
            return redirect()->route('bills.create');
        }

        $company = CompanySetting::current()->snapshot();

        return view('bills.preview', $this->receiptVars($draft, $company) + ['draft' => $draft]);
    }

    /** Final generation: save the bill, give it a number and create the PDF. */
    public function store(Request $request)
    {
        $draft = $request->session()->get(self::DRAFT_KEY);
        if (! $draft) {
            return redirect()->route('bills.create')->with('error', 'Nothing to generate. Please fill the form again.');
        }

        $bill = DB::transaction(function () use ($draft) {
            $attributes = collect($draft)->except(['income_proof_tmp'])->all();
            $attributes['company'] = CompanySetting::current()->snapshot();
            $attributes['amount_words'] = AmountInWords::convert($attributes['amount']);

            if (! empty($draft['income_proof_tmp']) && Storage::disk('local')->exists($draft['income_proof_tmp'])) {
                $final = 'income-proofs/'.basename($draft['income_proof_tmp']);
                Storage::disk('local')->move($draft['income_proof_tmp'], $final);
                $attributes['income_proof_file'] = $final;
            } else {
                $attributes['income_proof_original_name'] = null;
            }

            $bill = Bill::create($attributes);
            $bill->bill_no = Bill::makeBillNo($bill->id, $bill->bill_date);
            $bill->save();

            return $bill;
        });

        $request->session()->forget(self::DRAFT_KEY);
        $this->storePdf($bill);

        return redirect()->route('bills.show', $bill)->with('success', "Bill {$bill->bill_no} generated successfully.");
    }

    public function show(Bill $bill)
    {
        return view('bills.show', $this->receiptVars($this->billArray($bill), $bill->company) + ['bill' => $bill]);
    }

    public function print(Bill $bill)
    {
        return view('bills.pdf', $this->receiptVars($this->billArray($bill), $bill->company) + ['autoPrint' => true]);
    }

    public function pdf(Bill $bill)
    {
        if (! $bill->pdf_path || ! Storage::disk('local')->exists($bill->pdf_path)) {
            $this->storePdf($bill);
        }

        return Storage::disk('local')->download($bill->pdf_path, $bill->bill_no.'.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function incomeProof(Bill $bill)
    {
        abort_unless($bill->income_proof_file && Storage::disk('local')->exists($bill->income_proof_file), 404);

        return Storage::disk('local')->download(
            $bill->income_proof_file,
            $bill->income_proof_original_name ?: basename($bill->income_proof_file)
        );
    }

    private function storePdf(Bill $bill): void
    {
        $html = view('bills.pdf', $this->receiptVars($this->billArray($bill), $bill->company))->render();
        $pdf = Pdf::setOption(['isFontSubsettingEnabled' => true])
            ->loadHTML($html)
            ->setPaper('a4', 'portrait');

        $path = 'bills/'.$bill->bill_no.'.pdf';
        Storage::disk('local')->put($path, $pdf->output());
        $bill->forceFill(['pdf_path' => $path])->save();
    }

    private function validated(Request $request): array
    {
        $request->merge([
            'aadhaar_no' => preg_replace('/\D/', '', (string) $request->input('aadhaar_no')),
            'pan_no' => strtoupper(trim((string) $request->input('pan_no'))),
            'mobile' => preg_replace('/[^\d+]/', '', (string) $request->input('mobile')),
        ]);

        $data = $request->validate([
            'investor_name' => ['required', 'string', 'max:255'],
            'nominee_name' => ['nullable', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
            'mobile' => ['required', 'regex:/^\+?\d{10,13}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'aadhaar_no' => ['required', 'digits:12'],
            'pan_no' => ['required', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/'],
            'amount' => ['required', 'numeric', 'min:1', 'max:9999999999999'],
            'duration' => ['required', 'string', 'max:100'],
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'income_proof' => ['required', 'string', 'max:255'],
            'income_proof_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'self_declaration' => ['required', 'string', 'max:2000'],
            'special_note' => ['nullable', 'string', 'max:2000'],
            'bill_date' => ['required', 'date'],
        ], [
            'mobile.regex' => 'Enter a valid mobile number (10 digits).',
            'pan_no.regex' => 'Enter a valid PAN, e.g. ABCDE1234F.',
            'aadhaar_no.digits' => 'Aadhaar number must be 12 digits.',
        ]);

        unset($data['income_proof_file']);
        $data['amount'] = round((float) $data['amount'], 2);
        $data['amount_words'] = AmountInWords::convert($data['amount']);

        return $data;
    }

    private function billArray(Bill $bill): array
    {
        return array_merge($bill->only($bill->getFillable()), [
            'amount' => (float) $bill->amount,
            'from_date' => $bill->from_date->format('Y-m-d'),
            'to_date' => $bill->to_date->format('Y-m-d'),
            'bill_date' => $bill->bill_date->format('Y-m-d'),
        ]);
    }

    /** Everything the receipt template needs, from bill data + company snapshot. */
    private function receiptVars(array $b, array $c): array
    {
        $fmt = fn ($date) => $date ? Carbon::parse($date)->format('d-m-Y') : '';

        $b['from_date_f'] = $fmt($b['from_date'] ?? null);
        $b['to_date_f'] = $fmt($b['to_date'] ?? null);
        $b['bill_date_f'] = $fmt($b['bill_date'] ?? null);
        $b['amount_f'] = number_format((float) $b['amount'], 2);

        return [
            'b' => $b,
            'c' => $c,
            'logo' => CompanySetting::imageDataUri($c['logo_path'] ?? null, true),
            'signature' => CompanySetting::imageDataUri($c['signature_path'] ?? null),
        ];
    }
}
