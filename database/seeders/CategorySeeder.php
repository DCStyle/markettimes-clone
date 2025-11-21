<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Tiêu điểm',
                'slug' => 'tieu-diem',
                'description' => 'Các tin tức nổi bật và quan trọng nhất',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Thẩm định giá',
                'slug' => 'tham-dinh-gia',
                'description' => 'Tin tức và diễn đàn về thẩm định giá',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Tài chính',
                'slug' => 'tai-chinh',
                'description' => 'Tin tức tài chính và chứng khoán',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Kinh doanh',
                'slug' => 'kinh-doanh',
                'description' => 'Tin tức kinh doanh và doanh nghiệp',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Thế giới',
                'slug' => 'the-gioi',
                'description' => 'Tin tức kinh tế thế giới',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Công nghệ',
                'slug' => 'cong-nghe',
                'description' => 'Tin tức công nghệ và chuyển đổi số',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Bất động sản',
                'slug' => 'bat-dong-san',
                'description' => 'Tin tức thị trường bất động sản',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Ngành hàng',
                'slug' => 'nganh-hang',
                'description' => 'Tin tức các ngành hàng và sản phẩm',
                'order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
