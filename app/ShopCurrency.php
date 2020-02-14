<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopCurrency extends Model
{
    protected $table = 'shop_currency';
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable =[
      'currency_code',
      'shop_id'
    ];
}
