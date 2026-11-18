<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'bill_no', 'investor_name', 'nominee_name', 'address', 'mobile', 'email', 'aadhaar_no', 'pan_no',
        'amount', 'amount_words', 'duration', 'from_date', 'to_date', 'income_proof', 'income_proof_file',
        'income_proof_original_name', 'self_declaration', 'special_note', 'bill_date', 'company', 'pdf_path',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'from_date' => 'date',
            'to_date' => 'date',
            'bill_date' => 'date',
            'company' => 'array',
        ];
    }

    public static function makeBillNo(int $id, \DateTimeInterface $date): string
    {
        return config('growup.bill_prefix').'-'.$date->format('Y').'-'.str_pad((string) $id, 5, '0', STR_PAD_LEFT);
    }
}
