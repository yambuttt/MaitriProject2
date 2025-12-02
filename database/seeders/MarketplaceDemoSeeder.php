<?php

namespace Database\Seeders;

use App\Models\MarketplaceCategory;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MarketplaceDemoSeeder extends Seeder
{
    public function run(): void
    {
        $category = MarketplaceCategory::firstOrCreate(
            ['slug' => 'akun-premium'],
            [
                'name'        => 'Akun Premium',
                'description' => 'Kumpulan akun digital premium (Canva, Netflix, dsb).',
                'is_active'   => true,
            ]
        );

        $product = MarketplaceProduct::firstOrCreate(
            ['slug' => 'akun-canva'],
            [
                'marketplace_category_id' => $category->id,
                'name'        => 'Akun Canva Pro',
                'description' => "Penjualan akun Canva Pro sharing.\nAkun dikirim manual oleh admin setelah pembayaran.",
                'thumbnail'   => null,
                'is_active'   => true,
            ]
        );

        $variants = [
            ['name' => 'Canva Pro 30 Hari', 'duration_days' => 30,  'price' => 50000],
            ['name' => 'Canva Pro 60 Hari', 'duration_days' => 60,  'price' => 90000],
            ['name' => 'Canva Pro 90 Hari', 'duration_days' => 90,  'price' => 130000],
        ];

        foreach ($variants as $v) {
            MarketplaceVariant::firstOrCreate(
                [
                    'marketplace_product_id' => $product->id,
                    'name'                   => $v['name'],
                ],
                [
                    'duration_days' => $v['duration_days'],
                    'price'         => $v['price'],
                    'is_active'     => true,
                ]
            );
        }
    }
}
