<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_no', 40)->nullable()->unique();
            $table->string('investor_name');
            $table->string('nominee_name')->nullable();
            $table->text('address');
            $table->string('mobile', 20);
            $table->string('email')->nullable();
            $table->string('aadhaar_no', 20);
            $table->string('pan_no', 10);
            $table->decimal('amount', 15, 2);
            $table->string('amount_words');
            $table->string('duration', 100);
            $table->date('from_date');
            $table->date('to_date');
            $table->string('income_proof');
            $table->string('income_proof_file')->nullable();
            $table->string('income_proof_original_name')->nullable();
            $table->text('self_declaration');
            $table->text('special_note')->nullable();
            $table->date('bill_date');
            // Copy of the company settings at the moment the bill was generated
            $table->json('company');
            $table->string('pdf_path')->nullable();
            $table->timestamps();

            $table->index('investor_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
