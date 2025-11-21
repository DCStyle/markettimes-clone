<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'VN-Index',
            'VIC',
            'VJC',
            'VPB',
            'ACB',
            'Chứng khoán',
            'Bất động sản',
            'Ngân hàng',
            'Công nghệ',
            'Tài chính',
            'Doanh nghiệp',
            'Thẩm định giá',
            'Đầu tư',
            'Thị trường',
            'Kinh tế',
            'Xuất khẩu',
            'Nhập khẩu',
            'FDI',
            'GDP',
            'Lạm phát',
            'Lãi suất',
            'Thuế',
            'Startup',
            'Fintech',
            'E-commerce',
        ];

        foreach ($tags as $tagName) {
            Tag::create([
                'name' => $tagName,
                'slug' => Str::slug($tagName),
            ]);
        }
    }
}
