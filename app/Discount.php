<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    const FIXED_PRODUCT_DISCOUNT    = 1;
    const PERCENT_PRODUCT_DISCOUNT  = 2;
    const PRODUCT_FOR_FREE          = 3;
    const QUANTITY_PRODUCT_DISCOUNT = 4;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id', 'type', 'value', 'present_id', 'start_at', 'end_at',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'start_at', 'end_at'
    ];

    /**
     * Get the product that the discount is applied to.
     */
    public function product()
    {
        return $this->belongsTo('App\Product');
    }
}
