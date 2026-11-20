<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Company profile now follows the "Corporate Company Profile" document:
 * cover page + Overview, Business Focus, Associations, Vision, Mission,
 * Core Values, Commitment, Important Information, Corporate Enquiries.
 * All text is editable in Company Settings; it starts with the document's content.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->string('profile_title')->nullable();
            $table->string('profile_subtitle')->nullable();
            $table->string('profile_focus_line')->nullable();
            $table->text('profile_tagline')->nullable();
            $table->text('associations_summary')->nullable();
            $table->string('cover_note')->nullable();
            $table->text('business_focus')->nullable();   // "TITLE | Description" per line
            $table->text('associations')->nullable();     // "Name | Description" per line
            $table->text('associations_note')->nullable();
            $table->text('core_values')->nullable();      // "TITLE | Description" per line
            $table->text('commitment')->nullable();
            $table->text('disclaimer')->nullable();
            $table->text('enquiries_text')->nullable();
        });

        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropColumn(['services', 'highlights', 'key_people']);
        });

        DB::table('company_settings')->update(self::defaults() + ['updated_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropColumn([
                'profile_title', 'profile_subtitle', 'profile_focus_line', 'profile_tagline', 'associations_summary',
                'cover_note', 'business_focus', 'associations', 'associations_note', 'core_values', 'commitment',
                'disclaimer', 'enquiries_text',
            ]);
            $table->text('services')->nullable();
            $table->text('highlights')->nullable();
            $table->text('key_people')->nullable();
        });
    }

    /** Content of the Growup Investment Trading Pvt. Ltd. corporate company profile. */
    public static function defaults(): array
    {
        return [
            'profile_title' => "GROWUP INVESTMENT\nTRADING PVT. LTD.",
            'profile_subtitle' => 'CORPORATE COMPANY PROFILE',
            'profile_focus_line' => 'INVESTMENT • SHARE MARKET • TRADING',
            'profile_tagline' => "Building business relationships through\nprofessionalism, knowledge and growth.",
            'associations_summary' => 'Official business partner association with First Mahi Investment Holding & Trading Co. and association with Richax Pvt. Ltd. (USA-based), as represented by the company.',
            'cover_note' => 'Prepared for clients, investors and business partners',
            'about_us' => "Growup Investment Trading Pvt. Ltd. is a business organization focused on investment-related activities, share-market engagement and trading. The company aims to develop professional relationships with clients, investors and business partners through a growth-oriented approach, clear communication and responsible business practices.\n\nThe company’s approach emphasizes informed decision-making, disciplined processes and an understanding of the opportunities and risks associated with financial markets. Its objective is to build lasting business relationships while supporting a professional and service-focused environment.",
            'business_focus' => implode("\n", [
                'INVESTMENT | Investment-related business activities and opportunities, approached with a focus on research and informed decisions.',
                'SHARE MARKET | Engagement with share-market concepts, market information and the importance of understanding market movements.',
                'TRADING | Trading-related activities supported by disciplined processes, risk awareness and professional conduct.',
            ]),
            'associations' => implode("\n", [
                'First Mahi Investment Holding & Trading Co. | Official business partner association, as represented by Growup Investment Trading Pvt. Ltd.',
                'Richax Pvt. Ltd. (USA) | USA-based company associated with Growup Investment Trading Pvt. Ltd., as represented by the company.',
            ]),
            'associations_note' => 'Association wording in this profile reflects the information supplied for its preparation. Use of partner names or logos should be supported by relevant written authorization.',
            'vision' => 'To build a trusted and growth-oriented business presence by encouraging professional excellence, responsible market participation and strong domestic and international business relationships.',
            'mission' => implode("\n", [
                'Develop professional and lasting relationships with clients, investors and business partners.',
                'Promote awareness of investment and share-market opportunities alongside the importance of market risks.',
                'Support a disciplined, research-aware and transparent approach to business activities.',
                'Create a collaborative workplace that values learning, accountability and continuous improvement.',
                'Explore responsible opportunities for business development and wider professional networks.',
            ]),
            'core_values' => implode("\n", [
                'INTEGRITY | Acting honestly and responsibly.',
                'TRANSPARENCY | Communicating clearly and accurately.',
                'PROFESSIONALISM | Maintaining high standards of conduct.',
                'LEARNING | Building knowledge and capability.',
                'RISK AWARENESS | Recognizing that markets involve risk.',
                'RELATIONSHIPS | Valuing trust and long-term collaboration.',
            ]),
            'commitment' => 'Growup Investment Trading Pvt. Ltd. is committed to fostering a professional business environment, communicating responsibly and treating clients and business partners with respect. The company recognizes that investment and trading activities involve risk and that decisions should be made only after considering individual objectives, financial circumstances and risk tolerance.',
            'disclaimer' => 'This company profile is for general corporate and informational purposes only. It is not an offer, solicitation, investment recommendation, research report or assurance of returns. Investments and trading involve market risk, including possible loss of capital. Any financial products, investment services, advisory activities or trading services should be offered only where the company and relevant personnel hold all registrations, licenses and authorizations required by applicable law. No guaranteed or fixed returns are implied.',
            'enquiries_text' => "Growup Investment Trading Pvt. Ltd.\nFor corporate enquiries, partnership discussions and business information, please contact the company through its official channels.",
        ];
    }
};
