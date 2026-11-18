<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo_path')->nullable();
            $table->string('company_name');
            $table->text('address')->nullable();
            $table->string('mobile', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('pan_no', 20)->nullable();
            $table->string('gst_no', 30)->nullable();
            $table->string('website')->nullable();
            $table->string('tagline')->nullable();
            $table->string('header_text')->nullable();
            $table->text('footer_text')->nullable();
            $table->string('authorized_person')->nullable();
            $table->string('signature_path')->nullable();
            $table->timestamps();
        });

        // Defaults taken from the printed receipt book
        DB::table('company_settings')->insert([
            'company_name' => 'GROWUP INVESTMENT TRADING PRIVATE LIMITED',
            'address' => 'F4/ 01, D-6, Wave Silver Tower Sector-18, Noida 201301',
            'email' => 'gitfinancialy.trading5@gmail.com',
            'tagline' => 'YOUR WEALTH IS OUR CONCERN',
            'header_text' => 'Investment Receipt',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
