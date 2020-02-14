<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppConfig extends Model
{
    protected $table = 'appconfig';
    
    protected $primaryKey = 'id';
    
    protected $fillable = ['product_name_label','product_image_label','product_price_label','product_quantity_label','product_sku_label','header_background_color','show_options_background_color','add_to_cart_background_color','show_options_text_color','add_to_cart_text_color','show_options_text',
        'sold_out_text','add_to_cart_text','store_id','app_status','sold_out_background_color' ,'sold_out_text_color'];
    
    public $timestamps = false; 
}
