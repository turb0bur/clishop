<?php

use Illuminate\Database\Seeder;
use App\Product;
use App\Discount;
use Carbon\Carbon;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $discounts = [
            [
                'product_id' => Product::where('name', 'MacBook Pro')->first()->id,
                'type'       => Discount::PRODUCT_FOR_FREE,
                'value'      => 1,
                'present_id' => Product::where('name', 'Raspberry Pi')->first()->id,
                'start_at'   => Carbon::yesterday(),
                'end_at'     => Carbon::now()->addMonth(),
            ],
            [
                'product_id' => Product::where('name', 'Alexa')->first()->id,
                'type'       => Discount::PERCENT_PRODUCT_DISCOUNT,
                'value'      => 10,
                'present_id' => null,
                'start_at'   => Carbon::yesterday(),
                'end_at'     => Carbon::now()->addMonth(),
            ],
            [
                'product_id' => Product::where('name', 'Google Home')->first()->id,
                'type'       => Discount::PRODUCT_FOR_FREE,
                'value'      => 2,
                'present_id' => Product::where('name', 'Google Home')->first()->id,
                'start_at'   => Carbon::yesterday(),
                'end_at'     => Carbon::now()->addMonth(),
            ],
        ];
        foreach ($discounts as $discount) {
            Discount::create($discount);
        }
    }
}
