<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // === Labubu Clothes (type 1) ===
            [
                'slug' => 'labubu-winter-sweater',
                'product_type_id' => 1,
                'price' => 1500,
                'stock' => 5,
                'size_info' => json_encode(['fits' => 'Labubu 17cm']),
                'production_days' => 3,
                'sort_order' => 0,
                'translations' => [
                    'zh-TW' => ['name' => 'Labubu 冬季毛衣', 'description' => '手工編織的迷你毛衣，適合17cm Labubu。使用柔軟羊毛線，保暖又可愛。每一件都是獨一無二的手作品。'],
                    'en' => ['name' => 'Labubu Winter Sweater', 'description' => 'Hand-knitted mini sweater for 17cm Labubu. Made with soft wool yarn, warm and adorable. Each piece is one-of-a-kind.'],
                ],
                'materials' => [1], // wool
                'colors' => [2, 3], // red, white
            ],
            [
                'slug' => 'labubu-satin-dress',
                'product_type_id' => 1,
                'price' => 1800,
                'stock' => 3,
                'size_info' => json_encode(['fits' => 'Labubu 17cm']),
                'production_days' => 5,
                'sort_order' => 1,
                'translations' => [
                    'zh-TW' => ['name' => 'Labubu 緞面小洋裝', 'description' => '精緻緞面材質，小巧可愛的洋裝造型。裙擺有蕾絲點綴，非常適合拍照。'],
                    'en' => ['name' => 'Labubu Satin Mini Dress', 'description' => 'Delicate satin material with a cute dress design. Lace-trimmed hem, perfect for photos.'],
                ],
                'materials' => [2], // satin
                'colors' => [1, 12], // pink, lavender
            ],
            [
                'slug' => 'labubu-christmas-outfit',
                'product_type_id' => 1,
                'price' => 2200,
                'stock' => 0, // sold out
                'size_info' => json_encode(['fits' => 'Labubu 17cm']),
                'production_days' => 5,
                'sort_order' => 2,
                'translations' => [
                    'zh-TW' => ['name' => 'Labubu 聖誕套裝', 'description' => '聖誕限定！包含紅色毛衣和聖誕帽。讓你的Labubu一起過聖誕節。'],
                    'en' => ['name' => 'Labubu Christmas Outfit', 'description' => 'Christmas limited edition! Includes red sweater and Santa hat. Let your Labubu celebrate Christmas.'],
                ],
                'materials' => [1, 7], // wool, fleece
                'colors' => [2, 3], // red, white
            ],
            [
                'slug' => 'labubu-hoodie',
                'product_type_id' => 1,
                'price' => 1600,
                'stock' => 8,
                'size_info' => json_encode(['fits' => 'Labubu 17cm']),
                'production_days' => 4,
                'sort_order' => 3,
                'translations' => [
                    'zh-TW' => ['name' => 'Labubu 連帽小衛衣', 'description' => '超萌連帽設計！棉質面料柔軟舒適，帽子上有可愛的小耳朵造型。'],
                    'en' => ['name' => 'Labubu Mini Hoodie', 'description' => 'Adorable hoodie design! Soft cotton fabric with cute little ears on the hood.'],
                ],
                'materials' => [3], // cotton
                'colors' => [16, 1], // grey, pink
            ],
            [
                'slug' => 'labubu-sailor-uniform',
                'product_type_id' => 1,
                'price' => 2000,
                'stock' => 4,
                'size_info' => json_encode(['fits' => 'Labubu 17cm']),
                'production_days' => 6,
                'sort_order' => 4,
                'translations' => [
                    'zh-TW' => ['name' => 'Labubu 水手服', 'description' => '經典日系水手服設計，附領結。深藍配白色條紋，復古又可愛。'],
                    'en' => ['name' => 'Labubu Sailor Uniform', 'description' => 'Classic Japanese sailor uniform design with bow tie. Navy and white stripes, retro and cute.'],
                ],
                'materials' => [3], // cotton
                'colors' => [10, 3], // navy, white
            ],
            [
                'slug' => 'labubu-overall-set',
                'product_type_id' => 1,
                'price' => 1900,
                'stock' => 6,
                'size_info' => json_encode(['fits' => 'Labubu 17cm']),
                'production_days' => 5,
                'sort_order' => 5,
                'translations' => [
                    'zh-TW' => ['name' => 'Labubu 吊帶褲套裝', 'description' => '可愛的牛仔風吊帶褲搭配條紋上衣，休閒又時尚。吊帶可調節。'],
                    'en' => ['name' => 'Labubu Overall Set', 'description' => 'Cute denim-style overalls with a striped top. Casual and stylish. Adjustable straps.'],
                ],
                'materials' => [3], // cotton
                'colors' => [9, 4], // blue, cream
            ],
            [
                'slug' => 'labubu-knit-cape',
                'product_type_id' => 1,
                'price' => 1400,
                'stock' => 10,
                'size_info' => json_encode(['fits' => 'Labubu 17cm']),
                'production_days' => 2,
                'sort_order' => 6,
                'translations' => [
                    'zh-TW' => ['name' => 'Labubu 針織小斗篷', 'description' => '簡約針織斗篷，一秒變身小紅帽！輕鬆穿脫，適合各種場景搭配。'],
                    'en' => ['name' => 'Labubu Knit Cape', 'description' => 'Simple knit cape — instant Little Red Riding Hood look! Easy to put on, goes with everything.'],
                ],
                'materials' => [5], // acrylic yarn
                'colors' => [2, 5], // red, beige
            ],
            [
                'slug' => 'labubu-flower-dress',
                'product_type_id' => 1,
                'price' => 1700,
                'stock' => 2,
                'size_info' => json_encode(['fits' => 'Labubu 17cm']),
                'production_days' => 5,
                'sort_order' => 7,
                'translations' => [
                    'zh-TW' => ['name' => 'Labubu 碎花洋裝', 'description' => '春日碎花圖案，浪漫田園風格。裙擺有層次感，非常適合春天拍照。'],
                    'en' => ['name' => 'Labubu Floral Dress', 'description' => 'Spring floral pattern, romantic countryside style. Layered hem, perfect for spring photos.'],
                ],
                'materials' => [3, 2], // cotton, satin
                'colors' => [1, 7, 13], // pink, green, yellow
            ],
            [
                'slug' => 'labubu-raincoat',
                'product_type_id' => 1,
                'price' => 1300,
                'stock' => 7,
                'size_info' => json_encode(['fits' => 'Labubu 17cm']),
                'production_days' => 3,
                'sort_order' => 8,
                'translations' => [
                    'zh-TW' => ['name' => 'Labubu 小雨衣', 'description' => '半透明小雨衣，附迷你帽子。荷蘭天氣必備！讓你的Labubu也能防雨。'],
                    'en' => ['name' => 'Labubu Mini Raincoat', 'description' => 'Translucent mini raincoat with tiny hood. A must for Dutch weather! Keep your Labubu dry.'],
                ],
                'materials' => [3], // cotton (lining)
                'colors' => [13, 3], // yellow, white
            ],
            [
                'slug' => 'labubu-pajama-set',
                'product_type_id' => 1,
                'price' => 1500,
                'stock' => 5,
                'size_info' => json_encode(['fits' => 'Labubu 17cm']),
                'production_days' => 4,
                'sort_order' => 9,
                'translations' => [
                    'zh-TW' => ['name' => 'Labubu 睡衣套裝', 'description' => '柔軟法蘭絨睡衣，附迷你睡帽。星星圖案，讓你的Labubu有個好夢。'],
                    'en' => ['name' => 'Labubu Pajama Set', 'description' => 'Soft flannel pajamas with mini sleep cap. Star pattern for sweet dreams.'],
                ],
                'materials' => [7], // fleece
                'colors' => [9, 13], // blue, yellow
            ],

            // === Real Clothes (type 2) ===
            [
                'slug' => 'handknit-cardigan-cream',
                'product_type_id' => 2,
                'price' => 8500,
                'stock' => 2,
                'size_info' => json_encode(['sizes' => ['S', 'M', 'L']]),
                'production_days' => 14,
                'sort_order' => 10,
                'translations' => [
                    'zh-TW' => ['name' => '手編羊毛開襟外套', 'description' => '100%手工編織羊毛外套，經典款式。寬鬆版型，適合秋冬穿搭。每件需要兩週製作時間。'],
                    'en' => ['name' => 'Handknit Wool Cardigan', 'description' => '100% hand-knitted wool cardigan, classic design. Relaxed fit, perfect for autumn/winter. Each piece requires two weeks to make.'],
                ],
                'materials' => [1], // wool
                'colors' => [4], // cream
            ],
            [
                'slug' => 'crochet-summer-top',
                'product_type_id' => 2,
                'price' => 5500,
                'stock' => 3,
                'size_info' => json_encode(['sizes' => ['S', 'M']]),
                'production_days' => 10,
                'sort_order' => 11,
                'translations' => [
                    'zh-TW' => ['name' => '鉤針夏日上衣', 'description' => '透氣棉線鉤針上衣，鏤空花紋設計。夏天穿搭的好選擇，可搭配高腰褲或長裙。'],
                    'en' => ['name' => 'Crochet Summer Top', 'description' => 'Breathable cotton yarn crochet top with openwork pattern. Great summer piece, pairs well with high-waist pants or long skirts.'],
                ],
                'materials' => [4], // cotton yarn
                'colors' => [3, 8], // white, mint
            ],
            [
                'slug' => 'wool-beanie-hat',
                'product_type_id' => 2,
                'price' => 2800,
                'stock' => 12,
                'size_info' => json_encode(['sizes' => ['One Size']]),
                'production_days' => 2,
                'sort_order' => 12,
                'translations' => [
                    'zh-TW' => ['name' => '手工羊毛毛帽', 'description' => '柔軟溫暖的手編毛帽，多色可選。荷蘭冬天的必備品！'],
                    'en' => ['name' => 'Handknit Wool Beanie', 'description' => 'Soft and warm hand-knitted beanie, available in multiple colors. Essential for Dutch winters!'],
                ],
                'materials' => [1], // wool
                'colors' => [16, 5, 6], // grey, beige, brown
            ],

            // === Bags (type 3) ===
            [
                'slug' => 'mini-tote-bag',
                'product_type_id' => 3,
                'price' => 3500,
                'stock' => 4,
                'size_info' => json_encode(['dimensions' => '25x20x8 cm']),
                'production_days' => 7,
                'sort_order' => 13,
                'translations' => [
                    'zh-TW' => ['name' => '手工編織小提包', 'description' => '純手工編織的小型提包，日常使用超方便。棉線材質，可機洗。內有小口袋。'],
                    'en' => ['name' => 'Handknit Mini Tote Bag', 'description' => 'Fully hand-knitted mini tote bag, perfect for daily use. Cotton yarn, machine washable. Inner pocket included.'],
                ],
                'materials' => [4], // cotton yarn
                'colors' => [5, 6], // beige, brown
            ],
            [
                'slug' => 'crochet-crossbody-bag',
                'product_type_id' => 3,
                'price' => 4200,
                'stock' => 2,
                'size_info' => json_encode(['dimensions' => '20x15x5 cm']),
                'production_days' => 8,
                'sort_order' => 14,
                'translations' => [
                    'zh-TW' => ['name' => '鉤針斜背小包', 'description' => '精緻鉤針斜背包，附可調節背帶。小巧精緻，出門帶手機和錢包剛剛好。'],
                    'en' => ['name' => 'Crochet Crossbody Bag', 'description' => 'Delicate crochet crossbody with adjustable strap. Compact and cute — fits your phone and wallet perfectly.'],
                ],
                'materials' => [4], // cotton yarn
                'colors' => [8, 12], // mint, lavender
            ],
            [
                'slug' => 'labubu-carrier-bag',
                'product_type_id' => 3,
                'price' => 2800,
                'stock' => 6,
                'size_info' => json_encode(['fits' => 'Labubu 17cm', 'dimensions' => '22x18 cm']),
                'production_days' => 5,
                'sort_order' => 15,
                'translations' => [
                    'zh-TW' => ['name' => 'Labubu 專用小提袋', 'description' => '帶你的Labubu出門的專用提袋！透明窗口設計，讓你的Labubu看得到外面。附棉質內襯。'],
                    'en' => ['name' => 'Labubu Carrier Bag', 'description' => 'A special carrier bag for your Labubu! Clear window design so your Labubu can see outside. Cotton lining included.'],
                ],
                'materials' => [3, 4], // cotton, cotton yarn
                'colors' => [1, 4], // pink, cream
            ],

            // === Accessories (type 4) ===
            [
                'slug' => 'labubu-scarf',
                'product_type_id' => 4,
                'price' => 800,
                'stock' => 15,
                'size_info' => json_encode(['fits' => 'Labubu 17cm']),
                'production_days' => 1,
                'sort_order' => 16,
                'translations' => [
                    'zh-TW' => ['name' => 'Labubu 迷你圍巾', 'description' => '迷你針織圍巾，可以繞Labubu脖子兩圈。超可愛的小配件！'],
                    'en' => ['name' => 'Labubu Mini Scarf', 'description' => 'Mini knitted scarf, wraps around Labubu\'s neck twice. Super cute accessory!'],
                ],
                'materials' => [5], // acrylic yarn
                'colors' => [2, 7, 14, 11], // red, green, orange, purple
            ],
            [
                'slug' => 'labubu-bow-tie-set',
                'product_type_id' => 4,
                'price' => 600,
                'stock' => 20,
                'size_info' => json_encode(['fits' => 'Labubu 17cm', 'set' => '3 pieces']),
                'production_days' => 1,
                'sort_order' => 17,
                'translations' => [
                    'zh-TW' => ['name' => 'Labubu 蝴蝶結三入組', 'description' => '三色蝴蝶結套組，可夾在頭上或衣服上。輕鬆改變Labubu的風格！'],
                    'en' => ['name' => 'Labubu Bow Tie Set (3 pcs)', 'description' => 'Three-color bow tie set, clips onto head or clothes. Easily change your Labubu\'s style!'],
                ],
                'materials' => [2], // satin
                'colors' => [1, 12, 13], // pink, lavender, yellow
            ],
            [
                'slug' => 'handknit-coaster-set',
                'product_type_id' => 4,
                'price' => 1200,
                'stock' => 8,
                'size_info' => json_encode(['set' => '4 pieces', 'diameter' => '10 cm']),
                'production_days' => 3,
                'sort_order' => 18,
                'translations' => [
                    'zh-TW' => ['name' => '手編杯墊四入組', 'description' => '手工鉤針杯墊，花朵造型。棉線材質可水洗，實用又美觀的居家小物。'],
                    'en' => ['name' => 'Crochet Coaster Set (4 pcs)', 'description' => 'Hand-crocheted flower-shaped coasters. Washable cotton yarn, practical and pretty home accessory.'],
                ],
                'materials' => [4], // cotton yarn
                'colors' => [1, 13, 8, 12], // pink, yellow, mint, lavender
            ],
            [
                'slug' => 'wool-keychain-charm',
                'product_type_id' => 4,
                'price' => 500,
                'stock' => 25,
                'size_info' => null,
                'production_days' => 1,
                'sort_order' => 19,
                'translations' => [
                    'zh-TW' => ['name' => '毛線鑰匙圈吊飾', 'description' => '可愛的毛線球鑰匙圈，附小鈴鐺。可掛在包包或鑰匙上，多色可選。'],
                    'en' => ['name' => 'Yarn Keychain Charm', 'description' => 'Cute yarn pom-pom keychain with tiny bell. Hang on your bag or keys, multiple colors available.'],
                ],
                'materials' => [5], // acrylic yarn
                'colors' => [17], // multicolor
            ],
        ];

        foreach ($products as $data) {
            $translations = $data['translations'];
            $materialIds = $data['materials'];
            $colorIds = $data['colors'];
            unset($data['translations'], $data['materials'], $data['colors']);

            $productId = DB::table('products')->insertGetId([
                ...$data,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Translations
            foreach ($translations as $locale => $trans) {
                DB::table('product_translations')->insert([
                    'product_id' => $productId,
                    'locale' => $locale,
                    'name' => $trans['name'],
                    'description' => $trans['description'],
                ]);
            }

            // Materials pivot
            foreach ($materialIds as $materialId) {
                DB::table('product_material')->insert([
                    'product_id' => $productId,
                    'material_id' => $materialId,
                ]);
            }

            // Colors pivot
            foreach ($colorIds as $colorId) {
                DB::table('product_color')->insert([
                    'product_id' => $productId,
                    'color_id' => $colorId,
                ]);
            }
        }

        $this->command->info('Seeded ' . count($products) . ' products with translations, materials, and colors.');
    }
}
