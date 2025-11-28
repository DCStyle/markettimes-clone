<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Information
            ['key' => 'site_name', 'value' => 'Market Times', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site_tagline', 'value' => 'Cập nhật tin tức kinh tế mỗi ngày', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site_description', 'value' => 'Trang tin tức kinh tế hàng đầu Việt Nam, cung cấp tin tức và phân tích về thị trường, tài chính, doanh nghiệp và đầu tư.', 'type' => 'string', 'group' => 'general'],
            ['key' => 'contact_email', 'value' => 'contact@markettimes.vn', 'type' => 'string', 'group' => 'general'],
            ['key' => 'contact_phone', 'value' => '+84 123 456 789', 'type' => 'string', 'group' => 'general'],
            ['key' => 'contact_address', 'value' => 'Tầng 10, Tòa nhà ABC, Quận 1, TP. Hồ Chí Minh', 'type' => 'string', 'group' => 'general'],

            // SEO & Meta
            ['key' => 'meta_title', 'value' => 'Market Times - Tin tức kinh tế & tài chính', 'type' => 'string', 'group' => 'seo'],
            ['key' => 'meta_description', 'value' => 'Đọc tin tức kinh tế mới nhất, phân tích thị trường, chứng khoán, bất động sản và các xu hướng tài chính tại Market Times.', 'type' => 'string', 'group' => 'seo'],
            ['key' => 'meta_keywords', 'value' => json_encode(['tin tức kinh tế', 'thị trường tài chính', 'chứng khoán', 'bất động sản', 'doanh nghiệp', 'đầu tư']), 'type' => 'json', 'group' => 'seo'],
            ['key' => 'meta_robots', 'value' => 'index, follow', 'type' => 'string', 'group' => 'seo'],

            // Social Media
            ['key' => 'facebook_url', 'value' => 'https://facebook.com/markettimes', 'type' => 'string', 'group' => 'social'],
            ['key' => 'twitter_url', 'value' => 'https://twitter.com/markettimes', 'type' => 'string', 'group' => 'social'],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com/markettimes', 'type' => 'string', 'group' => 'social'],
            ['key' => 'youtube_url', 'value' => '', 'type' => 'string', 'group' => 'social'],
            ['key' => 'linkedin_url', 'value' => '', 'type' => 'string', 'group' => 'social'],
            ['key' => 'og_title', 'value' => 'Market Times - Tin tức kinh tế hàng đầu', 'type' => 'string', 'group' => 'social'],
            ['key' => 'og_description', 'value' => 'Cập nhật tin tức kinh tế, tài chính, chứng khoán và phân tích thị trường mỗi ngày.', 'type' => 'string', 'group' => 'social'],

            // Branding (empty initially, will be uploaded via UI)
            ['key' => 'site_logo', 'value' => '', 'type' => 'image', 'group' => 'branding'],
            ['key' => 'site_favicon', 'value' => '', 'type' => 'image', 'group' => 'branding'],
            ['key' => 'og_image', 'value' => '', 'type' => 'image', 'group' => 'branding'],

            // Analytics
            ['key' => 'google_analytics_id', 'value' => '', 'type' => 'string', 'group' => 'analytics'],
            ['key' => 'google_tag_manager_id', 'value' => '', 'type' => 'string', 'group' => 'analytics'],
            ['key' => 'facebook_pixel_id', 'value' => '', 'type' => 'string', 'group' => 'analytics'],
            ['key' => 'custom_head_scripts', 'value' => '', 'type' => 'string', 'group' => 'analytics'],
            ['key' => 'custom_body_scripts', 'value' => '', 'type' => 'string', 'group' => 'analytics'],

            // Footer Settings
            ['key' => 'footer_magazine_title', 'value' => 'Tạp chí điện tử Nhịp sống thị trường', 'type' => 'string', 'group' => 'footer'],
            ['key' => 'footer_organization', 'value' => 'Cơ quan của Hội Thẩm định giá Việt Nam', 'type' => 'string', 'group' => 'footer'],
            ['key' => 'footer_editors', 'value' => json_encode([
                ['role' => 'Tổng Biên tập', 'name' => 'Nguyễn Văn A'],
                ['role' => 'Phó Tổng Biên tập', 'name' => 'Trần Thị B'],
                ['role' => 'Phó Tổng Biên tập', 'name' => 'Lê Văn C'],
            ]), 'type' => 'json', 'group' => 'footer'],
            ['key' => 'footer_offices', 'value' => json_encode([
                ['name' => 'Văn phòng Hà Nội', 'address' => 'Số 8, Phạm Hùng, Mỹ Đình, Nam Từ Liêm, Hà Nội'],
                ['name' => 'Văn phòng TP.HCM', 'address' => 'Số 123, Nguyễn Huệ, Quận 1, TP.HCM'],
            ]), 'type' => 'json', 'group' => 'footer'],
            ['key' => 'footer_phone', 'value' => '(024) 1234 5678', 'type' => 'string', 'group' => 'footer'],
            ['key' => 'footer_email', 'value' => 'info@markettimes.vn', 'type' => 'string', 'group' => 'footer'],
            ['key' => 'footer_license_number', 'value' => '535/GP-BTTTT', 'type' => 'string', 'group' => 'footer'],
            ['key' => 'footer_license_date', 'value' => '21/08/2021', 'type' => 'string', 'group' => 'footer'],
            ['key' => 'footer_license_issuer', 'value' => 'Bộ Thông tin và Truyền thông', 'type' => 'string', 'group' => 'footer'],
            ['key' => 'footer_copyright_text', 'value' => 'Toàn bộ bản quyền thuộc Nhịp sống thị trường', 'type' => 'string', 'group' => 'footer'],
            ['key' => 'footer_powered_by', 'value' => 'POWERED BY ONECMS', 'type' => 'string', 'group' => 'footer'],
            ['key' => 'footer_facebook_url', 'value' => '', 'type' => 'string', 'group' => 'footer'],
            ['key' => 'footer_twitter_url', 'value' => '', 'type' => 'string', 'group' => 'footer'],
            ['key' => 'footer_youtube_url', 'value' => '', 'type' => 'string', 'group' => 'footer'],
            ['key' => 'footer_instagram_url', 'value' => '', 'type' => 'string', 'group' => 'footer'],
            ['key' => 'footer_linkedin_url', 'value' => '', 'type' => 'string', 'group' => 'footer'],
            ['key' => 'footer_tiktok_url', 'value' => '', 'type' => 'string', 'group' => 'footer'],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Settings seeded successfully!');
    }
}
