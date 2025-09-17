<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Media;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $titles = ["second Song of Bees Comic Book", "Third Song of Bees Comic Book", "Fourth Song of Bees Comic Book"];
        foreach ($titles as $title) {
            $couponCheck = Coupon::where('title', $title)->first();
            if (!$couponCheck) {
                $categoryCheck = Category::where('title', 'Comics')->first();
                if (!$categoryCheck) {
                    $categoryCheck = Category::create([
                        "title" => "Comics",
                    ]);
                }
                $media = Media::first();
                Coupon::create([
                    'title' => $title,
                    'category_id' => $categoryCheck->id,
                    'coin' => 500,
                    'details' => 'Graphical Novel for Kids with a salt of Science 80 Colorful Pages Best Suited For 12 Do-It-Yourself Science Activities Ages 6-12',
                    'coupon_media_id' => $media ? $media->id : null,
                    'link' => "https://www.life-lab.org/lifelaboffer/",
                ]);
            }
        }
    }
}
