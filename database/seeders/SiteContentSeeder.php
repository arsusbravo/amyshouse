<?php

namespace Database\Seeders;

use App\Models\SiteContent;
use Illuminate\Database\Seeder;

class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        $contents = [
            // Homepage
            [
                'key' => 'home.greeting',
                'label' => 'Homepage Greeting',
                'type' => 'textarea',
                'sort_order' => 0,
                'zh-TW' => '手作溫暖，穿在心上。每一件作品都是用心製作的獨特手作品。',
                'en' => 'Handmade warmth, worn with heart. Every piece is a unique creation crafted with love.',
            ],

            // About page
            [
                'key' => 'about.intro',
                'label' => 'About - Introduction',
                'type' => 'textarea',
                'sort_order' => 1,
                'zh-TW' => '歡迎來到艾米小屋！我是Amy，一個熱愛手作的人。',
                'en' => "Welcome to Amy's House! I'm Amy, a passionate crafter based in the Netherlands.",
            ],
            [
                'key' => 'about.story',
                'label' => 'About - Story',
                'type' => 'textarea',
                'sort_order' => 2,
                'zh-TW' => "每一件作品都是我用心製作的。從選線、配色到編織，每個步驟都傾注了溫度與愛。\n無論是為你的Labubu穿上暖暖的毛衣，還是為你自己編織一件獨特的手作品，\n我都希望這份溫暖能傳遞到你手中。",
                'en' => "Every piece is made with heart and hands. From choosing the yarn, matching colors,\nto the final stitch — each step is filled with warmth and care. Whether it's a cozy\nsweater for your Labubu or a unique handmade accessory for yourself, I hope this\nwarmth reaches you.",
            ],
            [
                'key' => 'about.philosophy',
                'label' => 'About - Philosophy',
                'type' => 'textarea',
                'sort_order' => 3,
                'zh-TW' => '手作的美好在於——世界上沒有完全相同的兩件作品。每一針每一線都是獨一無二的，就像每個收到作品的你。',
                'en' => 'The beauty of handmade lies in its uniqueness — no two pieces are exactly the same. Every stitch is one of a kind, just like you.',
            ],
            [
                'key' => 'about.closing',
                'label' => 'About - Closing',
                'type' => 'text',
                'sort_order' => 4,
                'zh-TW' => '手作溫暖，穿在心上 ❤',
                'en' => 'Handmade warmth, worn with heart ❤',
            ],

            // Footer
            [
                'key' => 'footer.tagline',
                'label' => 'Footer Tagline',
                'type' => 'text',
                'sort_order' => 5,
                'zh-TW' => '手作溫暖，穿在心上',
                'en' => 'Handmade warmth, worn with heart',
            ],
        ];

        foreach ($contents as $item) {
            $zhContent = $item['zh-TW'];
            $enContent = $item['en'];

            unset($item['zh-TW'], $item['en']);

            SiteContent::updateOrCreate(
                ['key' => $item['key'], 'locale' => 'zh-TW'],
                [...$item, 'locale' => 'zh-TW', 'content' => $zhContent],
            );

            SiteContent::updateOrCreate(
                ['key' => $item['key'], 'locale' => 'en'],
                [...$item, 'locale' => 'en', 'content' => $enContent],
            );
        }
    }
}