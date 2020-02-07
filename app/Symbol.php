<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Symbol extends Model
{
    protected $table = 'currency';
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable =[
      'currency_code',
      'symbol_html'
    ];
}
