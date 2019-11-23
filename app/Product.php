<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sku', 'name', 'price', 'quantity', 'booked', 'description'
    ];

    /**
     * Get the discounts for the product.
     */
    public function discounts()
    {
        return $this->hasMany('App\Discount');
    }
}
