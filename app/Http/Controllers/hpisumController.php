<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use App\ShopModel;
use App\block_config;
use App\Symbol;
use App\ShopCurrency;

class hpisumController extends Controller
{
    public function index()
    {
        $sh = App::make('ShopifyAPI');
        $app_settings = DB::table('appsettings')->where('id', 1)->first();
        $shop = session('shop');
		if(empty($shop))
		{
			$shop = $_GET['shop'];
			//$shop = $request->input('shop');
			session(['shop' => $shop]);			
        }
        $shop_model = new ShopModel;
        $shop_find = ShopModel::where('store_name', $shop)->first();
        $sh = App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $shop_find->access_token]);
        $shop_info = $sh->call(['URL' => '/admin/shop.json', 'METHOD' => 'GET']);		
		$shop_currency = $shop_info->shop->currency;
        $product_count = $sh->call(['URL' => '/admin/products/count.json', 'METHOD' => 'GET']);
        $count = $product_count->count;
        $products = $sh->call(['URL' => '/admin/products.json?limit=15', 'METHOD' => 'GET']);
        $ids_array = array();
        $product_ids = DB::table('product_data')->where('shop', $shop)->first();
		if(count($product_ids) > 0){
            $json_data = $product_ids->selected_product;
			$ids_array = json_decode($json_data);
		}
		//dd($ids_array);	
        return view('product_list',['products' => $products->products, 'currency' => $shop_currency, 'selected_products' => $ids_array]);
        
    }

    public function get_all_product(Request $request)
	{
        
		$limit = $request['length'];
        $draw = $request['draw'];
        $start = $request['start'];
        $current_page = ceil($start / $limit) + 1;
        $search = $request['search']['value'];
		$app_settings = DB::table('appsettings')->where('id', 1)->first();
		$shop = session('shop');		
		if(empty($shop))
        {
            $shop = $_GET['shop'];
		}		
		$select_store = DB::table('usersettings')->where('store_name', $shop)->get();
        $ids_array = array();
        
		$product_ids = DB::table('product_data')->where('shop', $shop)->first();
		if(count($product_ids) > 0){
            $json_data = $product_ids->selected_product;
			$ids_array = json_decode($json_data);
		}
				
		$sh = App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' =>$app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $select_store[0]->access_token]);
		$shop_info = $sh->call(['URL' => '/admin/shop.json', 'METHOD' => 'GET']);		
		$shop_currency = $shop_info->shop->currency;
		$get_products = array();
		$count = $sh->call(['URL' => '/admin/products/count.json', 'METHOD' => 'GET']);
		if($search){
			$pages = ceil($count->count / 250);
                $limit = 250;
                for ($i = 0; $i < $pages; $i++) {
                    $current_page = $i + 1;
					$products = $sh->call(['URL' => '/admin/products.json?title='.$search.'&limit=' . $limit . '&page=' . $current_page, 'METHOD' => 'GET']);
					foreach ($products->products as $row) {
						if (stristr($row->title, $search)) {
							if(count($ids_array) > 0){
								if(in_array($row->id, $ids_array)){
									$select_option = '<input type="checkbox" name="product_select" class="product_select" value="'.$row->id.'" checked>';
								}else{
									$select_option = '<input type="checkbox" name="product_select" class="product_select" value="'.$row->id.'">';
								}
							}
							else{
								$select_option = '<input type="checkbox" name="product_select" class="product_select" value="'.$row->id.'">';
							}

							//for image
							if(count($row->images) != 0){
								$image = '<img src="'.$row->images[0]->src.'" class="product_image" width="50px" height="50px">';
							}
							else{
								$image = '<img src="'.url('/images/no-image-available.png').'" class="product_image" width="50px" height="50px">';
								
							}
							$price = $shop_currency.$row->variants['0']->price;
							$new_row = array($select_option,$image,$row->title,$price);
							$get_products[] = $new_row;
						}
					}
				}

		}
		else{
			$pages = ceil($count->count / 10);
			$url = 'https://' . $shop . '/admin/products.json?limit='.$limit.'&page='.$current_page;                       
			$products = $sh->call(['URL' => $url, 'METHOD' => 'GET'], FALSE);
			
			foreach ($products->products as $row) {

				if(count($ids_array) > 0){
					if(in_array($row->id, $ids_array)){
						$select_option = '<input type="checkbox" name="product_select[]" id="'.$row->id.'" class="product_select" value="'.$row->id.'" onChange="product_select_model($(this).val())" checked >';
					}else{
						$select_option = '<input type="checkbox" name="product_select[]" id="'.$row->id.'" class="product_select" value="'.$row->id.'" onChange="product_select_model($(this).val())">';
					}
				}
				else{
					$select_option = '<input type="checkbox" name="product_select[]" id="'.$row->id.'" class="product_select" value="'.$row->id.'" onChange="product_select_model($(this).val())">';
				}
				


				//for image
				if(count($row->images) != 0){
					$image = '<img src="'.$row->images[0]->src.'" class="product_image" width="50px" height="50px">';
				}
				else{
					$image = '<img src="'.url('/images/no-image-available.png').'" class="product_image" width="50px" height="50px">';
					
				}
				$price = $shop_currency.$row->variants['0']->price;
				$new_row = array($select_option,$image,$row->title,$price);
				$get_products[] = $new_row;
			}
		}
		
		//dd($get_products);
		$all_products = [
            "draw" => $draw,
            "recordsTotal" => $count->count,
            "recordsFiltered" => $count->count,
            'data' => $get_products
        ];
		return $all_products;
		//return view('quickbuy_products_new',['products' => $products->products, 'currency' => $shop_currency]);
    }
    public function update_products(Request $request)
	{
        dd($request['selected_product']);

    }
}
