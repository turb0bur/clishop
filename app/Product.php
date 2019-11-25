<?php

namespace App;

use Carbon\Carbon;
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
     * Get the discount for the product.
     */
    public function discount()
    {
        return $this->hasOne('App\Discount')
            ->whereDate('start_at', '<', Carbon::today())
            ->whereDate('end_at', '>', Carbon::today());
    }
}
