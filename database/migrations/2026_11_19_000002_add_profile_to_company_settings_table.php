<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->text('about_us')->nullable();
            $table->text('mission')->nullable();
            $table->text('vision')->nullable();
            $table->text('services')->nullable();      // one per line
            $table->text('highlights')->nullable();    // "Why choose us", one per line
            $table->text('key_people')->nullable();    // "Name - Designation", one per line
            $table->string('cin_no', 30)->nullable();
            $table->string('established_year', 10)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->boolean('about_published')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropColumn([
                'about_us', 'mission', 'vision', 'services', 'highlights', 'key_people', 'cin_no',
                'established_year', 'whatsapp', 'facebook', 'instagram', 'linkedin', 'about_published',
            ]);
        });
    }
};
