<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const COD_PAYMENT = 10;
    const CC_PAYMENT  = 11;

    const COURIER_DELIVERY = 20;
    const NP_DELIVERY      = 21;
    const DHL_DELIVERY     = 22;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'total', 'credit_card', 'payment', 'delivery', 'order'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'order' => 'array'
    ];

    /**
     * Get the user that made the order.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
