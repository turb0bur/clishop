<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'sku'      => $faker->bothify('##?#??'),
//        'name'     => $faker->randomElement(['Google Home', 'MacBook Pro', 'Alexa', 'Raspberry Pi']),
        'price'    => $faker->randomFloat(2, 30, 3000),
        'quantity' => $faker->numberBetween(10, 20),
        'booked'   => 0
    ];
});
