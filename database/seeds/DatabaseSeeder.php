<?php

use Illuminate\Database\Seeder;
use App\Product;
use App\Discount;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Product::truncate();
        Discount::truncate();

        $names = ['Google Home', 'MacBook Pro', 'Alexa', 'Raspberry Pi'];
        for ($i = 0; $i < 4; $i++) {
            factory(Product::class)->create(['name' => $names[$i]]);
        }
        $this->call(DiscountSeeder::class);
    }
}
