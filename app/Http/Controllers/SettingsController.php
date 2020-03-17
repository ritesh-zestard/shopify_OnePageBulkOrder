<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RocketCode\Shopify\API;
use App;
use DB;
use App\AppConfig;
use App\ShopModel;

class SettingsController extends Controller {

    public function save() {
        if(session('shop')){
            $shop = session('shop');
        } else {
            $shop = $_REQUEST['shop'];
        }       
        
        $shopid = DB::table('usersettings')->where('store_name', $shop)->value('id');        
        $appconfig = new AppConfig;
        $app_setting = AppConfig::where('store_id', $shopid)->first();
        // if($_SERVER["REMOTE_ADDR"]=='103.254.244.134' || $_SERVER["REMOTE_ADDR"]=='122.170.162.150'){
        //     echo 'dd '.$shop;exit;
        // }
        $select_store = ShopModel::where('store_name', $shop)->first();
        //dd();
        $encrypted_store_id = $select_store->store_encrypt;
        $intro_status = $select_store->intro_status;
        $sort_order = DB::table('field_sorting_details')->where('shop_id', $shopid)->first();        
        
        if(count($app_setting) > 0){
            return view('quick_order_dashboard', compact('app_setting', 'encrypted_store_id', 'sort_order', 'intro_status', 'shop'));
        }else{
            return view('activate_quick_order');
        }
        
    }

    public function store(Request $request) {
        //$shop = $request->session()->get('shop');
        if(session('shop')){
            $shop = session('shop');
        } else {
            $shop = $_REQUEST['shop'];
        } 

        $shopid = DB::table('usersettings')->where('store_name', $shop)->value('id');

        $appconfig = new AppConfig;
        $app_setting = AppConfig::where('store_id', $shopid)->first();

        if (count($app_setting) > 0) {
            if ($request['app_status']) {
                $app_setting->app_status = $request['app_status'];
            } else {
                $app_setting->app_status = 0;
            }

            
            $app_setting->store_id = $shopid;
            $app_setting->product_name_label = $request['product_name_label'];
            $app_setting->product_image_label = $request['product_image_label'];
            $app_setting->product_price_label = $request['product_price_label'];
            $app_setting->product_quantity_label = $request['product_quantity_label'];
            $app_setting->display_sku = isset($request->display_sku) ? $request['display_sku'] : '';
            $app_setting->product_sku_label = $request['product_sku_label'];
            $app_setting->header_background_color = $request['header_background_color'];
            $app_setting->show_options_background_color = $request['show_options_background_color'];
            $app_setting->add_to_cart_background_color = $request['add_to_cart_background_color'];

            $app_setting->show_options_text_color = $request['show_options_text_color'];
            $app_setting->add_to_cart_text_color = $request['add_to_cart_text_color'];
            $app_setting->show_options_text = $request['show_options_text'];

            $app_setting->sold_out_text = $request['sold_out_text'];
            $app_setting->add_to_cart_text = $request['add_to_cart_text'];

            $app_setting->sold_out_background_color = $request['sold_out_background_color'];
            $app_setting->sold_out_text_color = $request['sold_out_text_color'];

            $app_setting->save();

            $notification = array(
                'message' => 'Updated Successfully.',
                'alert-type' => 'success'
            );

            return redirect()->route('quick_order_dashboard_save', ['shop' => $shop])->with('notification', $notification);
        } else {

            if ($request['app_status']) {
                $appconfig->app_status = $request['app_status'];
            } else {
                $appconfig->app_status = 0;
            }
            $appconfig->store_id = $shopid;
            $appconfig->product_name_label = $request['product_name_label'];
            $appconfig->product_image_label = $request['product_image_label'];
            $appconfig->product_price_label = $request['product_price_label'];
            $appconfig->product_quantity_label = $request['product_quantity_label'];
            $appconfig->product_sku_label = $request['product_sku_label'];
            $app_setting->display_sku = isset($request->display_sku) ? $request['display_sku'] : '';
            $appconfig->header_background_color = $request['header_background_color'];
            $appconfig->show_options_background_color = $request['show_options_background_color'];
            $appconfig->add_to_cart_background_color = $request['add_to_cart_background_color'];

            $appconfig->show_options_text_color = $request['show_options_text_color'];
            $appconfig->add_to_cart_text_color = $request['add_to_cart_text_color'];
            $appconfig->show_options_text = $request['show_options_text'];

            $appconfig->sold_out_text = $request['sold_out_text'];
            $appconfig->add_to_cart_text = $request['add_to_cart_text'];

            $appconfig->sold_out_background_color = $request['sold_out_background_color'];
            $appconfig->sold_out_text_color = $request['sold_out_text_color'];

            $appconfig->save();

            $notification = array(
                'message' => 'Updated Successfully.',
                'alert-type' => 'success'
            );

            return redirect()->route('quick_order_dashboard_save',['shop' => $shop])->with('notification', $notification);
        }
    }

    public function help() {
        $shop = session('shop');
        $select_store = ShopModel::where('store_name', $shop)->first();
        $encrypted_store_id = $select_store->store_encrypt;
        return view('quickhelp', compact('encrypted_store_id'));
    }

    public function update_sort_order(Request $request) {
        $shop = session('shop');
        $shopid = DB::table('usersettings')->where('store_name', $shop)->value('id');
        $new_order = json_encode($request['new_order']);
        $update_sort_order = DB::table('field_sorting_details')->where('shop_id', $shopid)->update(['sort_order' => $new_order]);
    }

    public function download( $filename = '' )
    {
        // Check if file exists in app/storage/file folder
        $file_path = storage_path() . "/app/downloads/" . $filename;
        $headers = array(
            'Content-Type: application/pdf',
            'Content-Disposition: attachment; filename='.$filename,
        );
        if ( file_exists( $file_path ) ) {
            // Send Download
            return \Response::download( $file_path, $filename, $headers );
        } else {
            // Error
            exit( 'Requested file does not exist on our server!' );
        }
    }

    public function update_demo_status(Request $request) {
        $shop = session('shop');
        $select_store = ShopModel::where('store_name', $shop)->first();
        $update_status = DB::table('usersettings')->where('store_name', $shop)->update(['intro_status' => 1]);
        return back();
    }

    public function activate_quick_order(Request $request)
    {
        $shop = session('shop');
        $app_settings = DB::table('appsettings')->where('id', 1)->first();
        $select_store = DB::table('usersettings')->where('store_name', $shop)->first();
        $sh = App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $select_store->access_token]);
        //dd($select_store->store_encrypt);
        $finaly_encrypt = $select_store->store_encrypt;
        //for set Default color and settings
        AppConfig::create([
            'store_id' => $select_store->id,
            'product_name_label' => 'Product Name',
            'product_image_label' => 'Product Image',
            'product_price_label' => 'Product Price',
            'product_quantity_label' => 'Product Quantity',
            'product_sku_label' => 'Product SKU',
            'header_background_color' => '#1e88e5',
            'show_options_background_color' => '#34bc99',
            'add_to_cart_background_color' => '#253952',
            'show_options_text_color' => '#FFFFFF',
            'add_to_cart_text_color' => '#FFFFFF',
            'sold_out_background_color' => '#ff7043',
            'sold_out_text_color' => '#FFFFFF',
            'show_options_text' => 'Show Options',
            'sold_out_text' => 'Sold Out',
            'add_to_cart_text' => 'Add to Cart',
            'app_status' => '1'
        ]);

        //Default settings for sorting order
        $sort_order =array("Product Image","Product Name","Product Price","Product Quantity","Product SKU");
        $sort_order = json_encode($sort_order);
        $check_sort_order = DB::table('field_sorting_details')->where('shop_id', $select_store->id)->first();
        if(empty($check_sort_order)){
            DB::table('field_sorting_details')->insert(['sort_order' => $sort_order,'shop_id' => $select_store->id]);
        }
        

        $page = $sh->call([
            'URL' => '/admin/pages.json',
            'METHOD' => 'GET',
        ]);

        foreach ($page->pages as $singlePage) {
            if ($singlePage->title == 'One Page Quick Order') {
                $oldpageId = $singlePage->id;
                $pageDelete = $sh->call([
                    'URL' => '/admin/pages/' . $oldpageId . '.json',
                    'METHOD' => 'DELETE',
                ]);
            }
        } 

        $data = array('page' => array('title' => 'One Page Quick Order', 'body_html' => '<div class="zestard_bulk_order_list_view" store_encrypt="'. $finaly_encrypt .'" style="width: 100%; text-align: center;"><img style="margin: 0 auto;" width="130" height="130" src="https://shopifydev.anujdalal.com/bulkorder/public/image/loader_new.svg" /></div><script src="https://shopifydev.anujdalal.com/bulkorder/public/js/one_page_quick_order.js"></script>'));
        $url_create_page = $sh->call(['URL' => '/admin/pages.json', 'METHOD' => 'POST', 'DATA' => $data ]);
        //$script_quick_order = $sh->call(['URL' => '/admin/script_tags.json', 'METHOD' => 'POST', 'DATA' => ['script_tag' => ['event' => 'onload', 'src' => 'https://shopifydev.anujdalal.com/bulk_quick_order_dev/public/js/one_page_quick_order.js', 'display_scope' => 'online_store']]]);

        return redirect()->route('quick_order_dashboard_save');
    }

}
