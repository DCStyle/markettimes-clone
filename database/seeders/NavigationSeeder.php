<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NavigationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create "TRANG CHỦ" (Home) link as first item
        \App\Models\NavigationItem::create([
            'label' => 'TRANG CHỦ',
            'type' => 'custom',
            'custom_url' => '/',
            'parent_id' => null,
            'order' => 0,
            'is_active' => true,
            'open_in_new_tab' => false,
        ]);

        // Import active top-level categories as navigation items
        $categories = \App\Models\Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        foreach ($categories as $index => $category) {
            \App\Models\NavigationItem::create([
                'label' => $category->name,
                'type' => 'category',
                'category_id' => $category->id,
                'parent_id' => null,
                'order' => $index + 1, // Start from 1 (Home is 0)
                'is_active' => true,
                'open_in_new_tab' => false,
            ]);
        }

        $this->command->info('Navigation items seeded successfully!');
        $this->command->info('Created 1 home link + ' . $categories->count() . ' category links');
    }
}
