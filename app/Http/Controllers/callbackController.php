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
use Mail;
use App\AppConfig;

class callbackController extends Controller {

    public function index(Request $request) {
        $sh = App::make('ShopifyAPI');
        $app_settings = DB::table('appsettings')->where('id', 1)->first();
        if (!empty($_GET['shop'])) {
            $shop = $_GET['shop'];
            $select_store = DB::table('usersettings')->where('store_name', $shop)->get();

            if (count($select_store) > 0) {
                session(['shop' => $shop]);
                //return redirect()->route('dashboard');
                //Remove coment for the Payment method
                $sh = App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $select_store[0]->access_token]);
                $id = $select_store[0]->charge_id;
                // if ($_SERVER['REMOTE_ADDR'] = '103.254.244.134') {
                //     print_r($id);exit;
                // }
                $url = 'admin/recurring_application_charges/' . $id . '.json';
                $charge = $sh->call(['URL' => $url, 'METHOD' => 'GET']);
                $charge_id = $select_store[0]->charge_id;
                $charge_status = $select_store[0]->status;
                if (!empty($charge_id) && $charge_id > 0 && $charge_status == "active") {
                    session(['shop' => $shop]);
                    return redirect()->route('dashboard', ['shop' => $shop]);
                } else {
                    return redirect()->route('payment_process', ['shop' => $shop]);
                }
            } else {
                $sh = App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop]);

                if ($shop == env('SHOPIFY_STORE_NAME')) {
                    $permission_url = $sh->installURL(['permissions' => array('read_script_tags', 'write_script_tags', 'read_themes', 'read_products', 'write_products', 'write_content', 'write_themes'), 'redirect' => $app_settings->redirect_url]);
                } else {
                    $permission_url = $sh->installURL(['permissions' => array('read_script_tags', 'write_script_tags', 'read_themes', 'read_products', 'write_products', 'write_content', 'write_themes'), 'redirect' => $app_settings->redirect_url]);
                }
                return redirect($permission_url);
            }
        }
    }

    public function redirect(Request $request) {
        $app_settings = DB::table('appsettings')->where('id', 1)->first();
        if (!empty($request->input('shop')) && !empty($request->input('code'))) {
            $shop = $request->input('shop'); //shop name


            $select_store = DB::table('usersettings')->where('store_name', $shop)->get();
            if (count($select_store) > 0) {
                /* session(['shop' => $shop]);
                  return redirect()->route('dashboard'); */
                //Remove coment for the Payment method
                $sh = App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $select_store[0]->access_token]);
                $id = $select_store[0]->charge_id;
                $url = 'admin/recurring_application_charges/' . $id . '.json';
                $charge = $sh->call(['URL' => $url, 'METHOD' => 'GET']);
                $charge_id = $select_store[0]->charge_id;
                $charge_status = $select_store[0]->status;
                if (!empty($charge_id) && $charge_id > 0 && $charge_status == "active") {
                    session(['shop' => $shop]);
                    return redirect()->route('dashboard', ['shop' => $shop]);
                } else {
                    return redirect()->route('payment_process');
                }
            }
            $sh = App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop]);
            try {
                $verify = $sh->verifyRequest($request->all());
                if ($verify) {
                    $code = $request->input('code');
                    $accessToken = $sh->getAccessToken($code);
                    //$rand=rand(5,15);
                    //$shop_encrypt=crypt($rand,"shop");
                    DB::table('usersettings')->insert(['access_token' => $accessToken, 'store_name' => $shop, 'store_encrypt' => ""]);
                    $shop_find = ShopModel::where('store_name', $shop)->first();
                    $shop_id = $shop_find->id;

                    $encrypt = crypt($shop_id, "ze");
                    $finaly_encrypt = str_replace(['/', '.'], "Z", $encrypt);

                    DB::table('usersettings')->where('id', $shop_id)->update(['store_encrypt' => $finaly_encrypt]);
                    $shop_find = ShopModel::where('store_name', $shop)->first();

                    $sh = App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $shop_find->access_token]);

                    //for creating the uninstall webhook
                    $url = 'https://' . $_GET['shop'] . '/admin/webhooks.json';
                    $webhookData = [
                        'webhook' => [
                            'topic' => 'app/uninstalled',
                            'address' => env('APP_URL') . 'uninstall.php',
                            'format' => 'json'
                        ]
                    ];
                    $uninstall = $sh->appUninstallHook($accessToken, $url, $webhookData);

                    //api call for get store info
                    $store_details = $sh->call(['URL' => '/admin/shop.json', 'METHOD' => 'GET']);
                    $shop_currency = new ShopCurrency;
                    $shop_currency->currency_code = $store_details->shop->currency;
                    $shop_currency->shop_id = $shop_id;
                    $shop_currency->save();
                    $currency_format = Symbol::where('currency_code', $store_details->shop->currency)->first();

                    //api call for Create Bulkorder pages

                    $page = $sh->call([
                        'URL' => '/admin/pages.json',
                        'METHOD' => 'GET',
                    ]);
                    foreach ($page->pages as $singlePage) {
                        if ($singlePage->title == 'Bulkorder') {
                            $oldBulkOrderId = $singlePage->id;
                            //echo "<pre/>"; print_r($singlePage);

                            $pageDelete = $sh->call([
                                'URL' => '/admin/pages/' . $oldBulkOrderId . '.json',
                                'METHOD' => 'DELETE',
                            ]);
                        }
                    }

                    $pages = $sh->call([
                        'URL' => '/admin/pages.json',
                        'METHOD' => 'POST',
                        'DATA' => [
                            'page' => [
                                'title' => 'Bulkorder',
                                'body_html' => '<div class="bulkorder" id="' . $shop_find->store_encrypt . '" mony_format="' . $currency_format->symbol_html . '"></div>',
                                'published' => true
                            ]
                        ]
                    ]);

                    //for set Default color and settings
                    AppConfig::create([
                        'store_id' => $shop_id,
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

                    $data = array('page' => array('title' => 'One Page Quick Order', 'body_html' => '<div class="zestard_bulk_order_list_view" store_encrypt="' . $finaly_encrypt . '" style="width: 100%; text-align: center;"><img style="margin: 0 auto;" width="130" height="130" src="https://zestardshop.com/shopifyapp/bulkorder/public/image/loader_new.svg" /></div><script src="https://zestardshop.com/shopifyapp/bulkorder/public/js/one_page_quick_order.js"></script>'));
                    $url_create_page = $sh->call(['URL' => '/admin/pages.json', 'METHOD' => 'POST', 'DATA' => $data]);

                    //api call for get theme info
                    $theme = $sh->call(['URL' => '/admin/themes/', 'METHOD' => 'GET']);
                    foreach ($theme->themes as $themeData) {
                        if ($themeData->role == 'main') {

                            $theme_id = $themeData->id;
                            $view = (string) View('snippets');

                            //api call for creating snippets
                            $call = $sh->call(['URL' => '/admin/themes/' . $theme_id . '/assets.json', 'METHOD' => 'PUT', 'DATA' => ['asset' => ['key' => 'templates/search.zestardbulkorder-json.liquid', 'value' => $view]]]);
                        }
                    }

                    //Default settings for sorting order
                    $sort_order = array("Product Image", "Product Name", "Product Price", "Product Quantity", "Product SKU");
                    $sort_order = json_encode($sort_order);
                    DB::table('field_sorting_details')->insert(['sort_order' => $sort_order, 'shop_id' => $shop_id]);

                    //api call for creating the app script tag 
                    $script = $sh->call(['URL' => '/admin/script_tags.json', 'METHOD' => 'POST', 'DATA' => ['script_tag' => ['event' => 'onload', 'src' => 'https://zestardshop.com/shopifyapp/bulkorder/public/js/bulkorder.js', 'display_scope' => 'online_store']]]);
                    //$script_quick_order = $sh->call(['URL' => '/admin/script_tags.json', 'METHOD' => 'POST', 'DATA' => ['script_tag' => ['event' => 'onload', 'src' => 'https://zestardshop.com/shopifyapp/bulk_quick_order_dev/public/js/one_page_quick_order.js', 'display_scope' => 'online_store']]]);

                    session(['shop' => $shop]);

                    //Check if trial is still running
                    $check_trial = DB::table('trial_info')->where('store_name', $shop)->first();
                    if (count($check_trial) > 0) {
                        $total_trial_days = $check_trial->trial_days;
                        $trial_activated_date = $check_trial->activated_on;
                        $trial_over_date = $check_trial->trial_ends_on;
                        $current_date = date("Y-m-d");
                        //$current_date = "2019-2-09";

                        if (strtotime($current_date) < strtotime($trial_over_date)) {
                            $date1 = date_create($trial_over_date);
                            $date2 = date_create($current_date);
                            $trial_remain = date_diff($date2, $date1);
                            $new_trial_days = $trial_remain->format("%a");
                        } else {
                            $new_trial_days = 0;
                        }

                        if ($shop == env('SHOPIFY_STORE_NAME')) {
                            $url = 'https://' . $shop . '/admin/recurring_application_charges.json';
                            $charge = $sh->call([
                                'URL' => $url,
                                'METHOD' => 'POST',
                                'DATA' => array(
                                    'recurring_application_charge' => array(
                                        'name' => 'Bulkorder',
                                        'price' => 0.01,
                                        'return_url' => url('payment_success'),
                                        'trial_days' => $new_trial_days,
                                        'test' => true
                                    )
                                )
                                    ], false);
                        } else {
                            //return redirect('dashboard');
                            //creating the Recuring charge for app
                            $url = 'https://' . $shop . '/admin/recurring_application_charges.json';
                            $charge = $sh->call([
                                'URL' => $url,
                                'METHOD' => 'POST',
                                'DATA' => array(
                                    'recurring_application_charge' => array(
                                        'name' => 'Bulkorder',
                                        'price' => 3.99,
                                        'return_url' => url('payment_success'),
                                        'trial_days' => $new_trial_days,
                                    //'test' => true
                                    )
                                )
                                    ], false);
                        }
                    } else {//for the first time create trial
                        if ($shop == env('SHOPIFY_STORE_NAME')) {
                            $url = 'https://' . $shop . '/admin/recurring_application_charges.json';
                            $charge = $sh->call([
                                'URL' => $url,
                                'METHOD' => 'POST',
                                'DATA' => array(
                                    'recurring_application_charge' => array(
                                        'name' => 'Bulkorder',
                                        'price' => 0.01,
                                        'return_url' => url('payment_success'),
                                        'trial_days' => 7,
                                        'test' => true
                                    )
                                )
                                    ], false);
                        } else {

                            //creating the Recuring charge for app
                            $url = 'https://' . $shop . '/admin/recurring_application_charges.json';
                            $charge = $sh->call([
                                'URL' => $url,
                                'METHOD' => 'POST',
                                'DATA' => array(
                                    'recurring_application_charge' => array(
                                        'name' => 'Bulkorder',
                                        'price' => 3.99,
                                        'return_url' => url('payment_success'),
                                        'trial_days' => 7,
                                    //'test' => true
                                    )
                                )
                                    ], false);
                        }
                    }

                    //dd($charge);
                    $create_charge = DB::table('usersettings')->where('store_name', $shop)->update(['charge_id' => (string) $charge->recurring_application_charge->id, 'api_client_id' => $charge->recurring_application_charge->api_client_id, 'price' => $charge->recurring_application_charge->price, 'status' => $charge->recurring_application_charge->status, 'billing_on' => $charge->recurring_application_charge->billing_on, 'payment_created_at' => $charge->recurring_application_charge->created_at, 'activated_on' => $charge->recurring_application_charge->activated_on, 'trial_ends_on' => $charge->recurring_application_charge->trial_ends_on, 'cancelled_on' => $charge->recurring_application_charge->cancelled_on, 'trial_days' => $charge->recurring_application_charge->trial_days, 'decorated_return_url' => $charge->recurring_application_charge->decorated_return_url, 'confirmation_url' => $charge->recurring_application_charge->confirmation_url, 'domain' => $shop]);

                    $shopi_info = $sh->call(['URL' => '/admin/shop.json', 'METHOD' => 'GET']);


                    //for the installation follow up mail for cliant
                    $subject = "Zestard Installation Greetings :: One Page Bulk Order";
                    $sender = "support@zestard.com";
                    $sender_name = "Zestard Technologies";
                    $app_name = "One Page Bulk Order";
                    $logo = 'https://zestardshop.com/shopifyapp/bulkorder/public/image/zestard-logo.png';
                    $installation_follow_up_msg = '<html>

                        <head>
                            <meta name="viewport" content="width=device-width, initial-scale=1">
                            <style>
                                @import url("https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i");
                                @media only screen and (max-width:599px) {
                                    table {
                                        width: 100% !important;
                                    }
                                }
                                
                                @media only screen and (max-width:412px) {
                                    h2 {
                                        font-size: 20px;
                                    }
                                    p {
                                        font-size: 13px;
                                    }
                                    .easy-donation-icon img {
                                        width: 120px;
                                    }
                                }
                            </style>
                        
                        </head>
                        
                        <body style="background: #f4f4f4; padding-top: 57px; padding-bottom: 57px;">
                            <table class="main" border="0" cellspacing="0" cellpadding="0" width="600px" align="center" style="border: 1px solid #e6e6e6; background:#fff; ">
                                <tbody>
                                    <tr>
                                        <td style="padding: 30px 30px 10px 30px;" class="review-content">
                                            <p class="text-align:left;"><img src="' . $logo . '" alt=""></p>
                                            <p style="font-family: \'Open Sans\', sans-serif;font-size: 15px;color: dimgrey;margin-bottom: 13px; line-height: 25px; margin-top: 0px;"><b>Hi ' . $shopi_info->shop->shop_owner . '</b>,</p>
                                            <p style="font-family: \'Open Sans\', sans-serif;font-size: 15px;color: dimgrey;margin-bottom: 13px;line-height: 25px;margin-top: 0px;">Thanks for Installing Zestard Application ' . $app_name . '</p>
                                            <p style="font-family: \'Helvetica\', sans-serif;font-size: 15px;color: dimgrey;margin-bottom: 13px;line-height: 25px;margin-top: 0px;">We appreciate your kin interest for choosing our application and hope that you have a wonderful experience.</p>
                                            <p style="font-family: \'Open Sans\', sans-serif;font-size: 15px;color: dimgrey;margin-bottom: 13px;line-height: 25px;margin-top: 0px;">Please don\'t feel hesitate to reach us in case of any queries or questions at <a href="mailto:support@zestard.com" style="text-decoration: none;color: #1f98ea;font-weight: 600;">support@zestard.com</a>.</p>
                                            <p style="font-family: \'Open Sans\', sans-serif;font-size: 15px;color: dimgrey;margin-bottom: 13px;line-height: 25px;margin-top: 0px;">We also do have live chat support services for quick response and resolution of queries.</p>
                                            <p style="font-family: \'Open Sans\', sans-serif;font-size: 15px;color: dimgrey;margin-bottom: 13px;line-height: 25px;margin-top: 0px;">(Please Note: Support services are available according to the IST Time Zone(i.e GMT 5:30+) as we reside in India. Timings are from 10:00am to 7:00pm)</p>
                        
                                        </td>
                                    </tr>
                        
                                    <tr>
                                        <td style="padding: 20px 30px 30px 30px;">
                        
                                            <br>
                                            <p style="font-family: \'Open Sans\', sans-serif;font-size: 15px;color: dimgrey;margin-bottom: 13px;line-height: 26px; margin-bottom:0px;">Thanks,<br>Zestard Support</p>
                                        </td>
                                    </tr>
                        
                                </tbody>
                            </table>
                        </body>';

                    $receiver = $shopi_info->shop->email;

                    try {
                        // Mail::raw([], function ($message) use($sender,$sender_name,$receiver,$subject,$installation_follow_up_msg) {
                        //     $message->from($sender,$sender_name);
                        //     $message->to($receiver)->subject($subject);
                        //     $message->setBody($installation_follow_up_msg, 'text/html');
                        // });
                    } catch (\Exception $ex) {
                        echo "error";
                    }




                    $headers = 'MIME-Version: 1.0' . "\r\n";
                    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

                    $msg = '<table>
                                <tr>
                                    <th>Shop Name</th>
                                    <td>' . $shopi_info->shop->name . '</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>' . $shopi_info->shop->email . '</td>
                                </tr>
                                <tr>
                                    <th>Domain</th>
                                    <td>' . $shopi_info->shop->domain . '</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>' . $shopi_info->shop->phone . '</td>
                                </tr>
                                <tr>
                                    <th>Shop Owner</th>
                                    <td>' . $shopi_info->shop->shop_owner . '</td>
                                </tr>
                                <tr>
                                    <th>Country</th>
                                    <td>' . $shopi_info->shop->country_name . '</td>
                                </tr>
                                <tr>
                                    <th>Plan</th>
                                    <td>' . $shopi_info->shop->plan_name . '</td>
                                </tr>
                            </table>';

                    $store_details = DB::table('development_stores')->get();
                    $development_store = [];
                    foreach ($store_details as $develop_stores) {
                        $development_store[] = $develop_stores->dev_store_name;
                    }
                    if (!in_array($shop, $development_store)) {
                        mail("support@zestard.com", "One Page Bulk Order App Installed", $msg, $headers);
                    }
                    //redirecting to the Shopify payment page
                    echo '<script>window.top.location.href="' . $charge->recurring_application_charge->confirmation_url . '"</script>';
                } else {
                    // Issue with data
                }
            } catch (Exception $e) {
                echo '<pre>Error: ' . $e->getMessage() . '</pre>';
            }
        }
    }

    public function dashboard(Request $request) {
        if (session('shop')) {
            $shop = session('shop');
        } else {
            $shop = $_REQUEST['shop'];
        }
        $app_settings = DB::table('appsettings')->where('id', 1)->first();
        $shop_model = new ShopModel;
        $shop_find = ShopModel::where('store_name', $shop)->first();
        $sh = App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $shop_find->access_token]);
        $store_details = $sh->call(['URL' => '/admin/shop.json', 'METHOD' => 'GET']);
        $currency_format = Symbol::where('currency_code', $store_details->shop->currency)->first();
        $app_currencyformat = ShopCurrency::where('shop_id', $shop_find->id)->first();
        $new_install = $shop_find->new_install;
        $currencyformat_app = Symbol::where('currency_code', $app_currencyformat->currency_code)->first();
        $all_currency = Symbol::all();
        $shop_domain = $store_details->shop->domain;

        // Update App
        //  if ($shop == "smilotricsdemo.myshopify.com") {
        $url = 'https://' . $shop . '/admin/oauth/access_scopes.json';
        $variant_data = $sh->call([
            'URL' => $url,
            'METHOD' => 'GET'
                ], false);
        $scope_array = array();
        foreach ($variant_data->access_scopes as $scope_data) {
            $scope_array[] = $scope_data->handle;
        }
        if (!in_array('read_products', $scope_array) && !in_array('write_products', $scope_array)) {
            return view('update_app');
        }
        //  }

        /*if ($shop == env('SHOPIFY_STORE_NAME')) {
            return view('hpisum_dashboard', ['store_detail' => $currency_format, 'app_currency' => $currencyformat_app, 'currency' => $all_currency, 'shop_details' => $shop_find, 'shop_domain' => $shop_domain, 'new_install' => $new_install]);
        } else { */
            return view('dashboard', ['store_detail' => $currency_format, 'app_currency' => $currencyformat_app, 'currency' => $all_currency, 'shop_details' => $shop_find, 'shop_domain' => $shop_domain, 'new_install' => $new_install, 'shop' => $shop]);
        //}
        //return view('dashboard', ['store_detail' => $currency_format, 'app_currency' => $currencyformat_app, 'currency' => $all_currency, 'shop_details' => $shop_find, 'shop_domain' => $shop_domain, 'new_install' => $new_install ]);
    }

    public function payment_method(Request $request) {
        $shop = session('shop');
        $app_settings = DB::table('appsettings')->where('id', 1)->first();
        $select_store = DB::table('usersettings')->where('store_name', $shop)->get();

        if (count($select_store) > 0) {
            $sh = App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $select_store[0]->access_token]);

            $charge_id = $select_store[0]->charge_id;
            $url = 'admin/recurring_application_charges/' . $charge_id . '.json';
            $charge = $sh->call(['URL' => $url, 'METHOD' => 'GET']);
            if (count($charge) > 0) {
                if ($charge->recurring_application_charge->status == "pending") {
                    echo '<script>window.top.location.href="' . $charge->recurring_application_charge->confirmation_url . '"</script>';
                } elseif ($charge->recurring_application_charge->status == "declined" || $charge->recurring_application_charge->status == "expired") {
                    //creating the new Recuring charge after declined app
                    $url = 'https://' . $shop . '/admin/recurring_application_charges.json';
                    $charge = $sh->call([
                        'URL' => $url,
                        'METHOD' => 'POST',
                        'DATA' => array(
                            'recurring_application_charge' => array(
                                'name' => 'Bulkorder',
                                'price' => 3.99,
                                'return_url' => url('payment_success'),
                            //'test' => true
                            )
                        )
                            ], false);

                    $create_charge = DB::table('usersettings')->where('store_name', $shop)->update(['charge_id' => (string) $charge->recurring_application_charge->id, 'api_client_id' => $charge->recurring_application_charge->api_client_id, 'price' => $charge->recurring_application_charge->price, 'status' => $charge->recurring_application_charge->status, 'billing_on' => $charge->recurring_application_charge->billing_on, 'payment_created_at' => $charge->recurring_application_charge->created_at, 'activated_on' => $charge->recurring_application_charge->activated_on, 'trial_ends_on' => $charge->recurring_application_charge->trial_ends_on, 'cancelled_on' => $charge->recurring_application_charge->cancelled_on, 'trial_days' => $charge->recurring_application_charge->trial_days, 'decorated_return_url' => $charge->recurring_application_charge->decorated_return_url, 'confirmation_url' => $charge->recurring_application_charge->confirmation_url, 'domain' => $shop]);

                    //redirecting to the Shopify payment page
                    echo '<script>window.top.location.href="' . $charge->recurring_application_charge->confirmation_url . '"</script>';
                } elseif ($charge->recurring_application_charge->status == "accepted") {

                    $active_url = '/admin/recurring_application_charges/' . $charge_id . '/activate.json';
                    $Activate_charge = $sh->call(['URL' => $active_url, 'METHOD' => 'POST', 'HEADERS' => array('Content-Length: 0')]);
                    $Activatecharge_array = get_object_vars($Activate_charge);
                    $active_status = $Activatecharge_array['recurring_application_charge']->status;
                    $update_charge_status = DB::table('usersettings')->where('store_name', $shop)->where('charge_id', $charge_id)->update(['status' => $active_status]);
                    return redirect()->route('dashboard', ['shop' => $shop]);
                }
            }
        }
    }

    public function payment_compelete(Request $request) {
        $app_settings = DB::table('appsettings')->where('id', 1)->first();
        $shop = session('shop');
        $select_store = DB::table('usersettings')->where('store_name', $shop)->get();

        $sh = App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $select_store[0]->access_token]);
        $charge_id = $_GET['charge_id'];
        $url = 'admin/recurring_application_charges/#{' . $charge_id . '}.json';
        $charge = $sh->call(['URL' => $url, 'METHOD' => 'GET',]);
        $status = $charge->recurring_application_charges[0]->status;

        $update_charge_status = DB::table('usersettings')->where('store_name', $shop)->where('charge_id', $charge_id)->update(['status' => $status]);

        if ($status == "accepted") {
            $active_url = '/admin/recurring_application_charges/' . $charge_id . '/activate.json';
            $Activate_charge = $sh->call(['URL' => $active_url, 'METHOD' => 'POST', 'HEADERS' => array('Content-Length: 0')]);
            $Activatecharge_array = get_object_vars($Activate_charge);
            $active_status = $Activatecharge_array['recurring_application_charge']->status;
            $trial_start = $Activatecharge_array['recurring_application_charge']->activated_on;
            $trial_end = $Activatecharge_array['recurring_application_charge']->trial_ends_on;
            $trial_days = $Activatecharge_array['recurring_application_charge']->trial_days;
            $update_charge_status = DB::table('usersettings')->where('store_name', $shop)->where('charge_id', $charge_id)->update(['status' => $active_status, 'activated_on' => $trial_start, 'trial_ends_on' => $trial_end]);

            //check if any trial info is exists or not
            if ($trial_days > 0) {
                $check_trial = DB::table('trial_info')->where('store_name', $shop)->first();
                if (count($check_trial) > 0) {
                    DB::table('trial_info')->where('store_name', $shop)->update(['trial_days' => $trial_days, 'activated_on' => $trial_start, 'trial_ends_on' => $trial_end]);
                } else {
                    DB::table('trial_info')->insert([
                        'store_name' => $shop,
                        'trial_days' => $trial_days,
                        'activated_on' => $trial_start,
                        'trial_ends_on' => $trial_end
                    ]);
                }
            }


            return redirect()->route('dashboard', ['shop' => $shop]);
        } elseif ($status == "declined") {
            echo '<script>window.top.location.href="https://' . $shop . '/admin/apps"</script>';
        }
        //return redirect()->route('dashboard');
    }

    public function Currency(Request $request) {
        $shop = session('shop');
        //dd($request->currency);
        $app_settings = DB::table('appsettings')->where('id', 1)->first();
        $shop_model = new ShopModel;
        $shop_find = ShopModel::where('store_name', $shop)->first();
        $currency_details = Symbol::where('id', $request->currency)->first();
        $currency_update = ShopCurrency::where('shop_id', $shop_find->id)->first();
        //dd($currency_update);
        $currency_update->currency_code = $currency_details->currency_code;
        $currency_update->save();
        $currency_updated = ShopCurrency::where('shop_id', $shop_find->id)->first();

        $sh = App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $shop_find->access_token]);
        $store_details = $sh->call(['URL' => '/admin/shop.json', 'METHOD' => 'GET']);
        $currency_format = Symbol::where('currency_code', $store_details->shop->currency)->first();
        $app_currencyformat = ShopCurrency::where('shop_id', $shop_find->id)->first();
        $currencyformat_app = Symbol::where('currency_code', $app_currencyformat->currency_code)->first();
        $all_currency = Symbol::all();
        $new_format = Symbol::where('currency_code', $currency_updated->currency_code)->first();
        return view('dashboard', ['store_detail' => $currency_format, 'app_currency' => $currencyformat_app, 'currency' => $all_currency, 'currency_updated' => $new_format, 'shop_details' => $shop_find]);
    }

    public function validatefrontend(Request $request) {
        $id = $request->id;
        //dd($id);

        $shop_find = ShopModel::where('store_encrypt', $id)->first();

        if ($shop_find->status == "active") {
            if ($shop_find->store_name == "segurico.myshopify.com") {
                //return view('search', ['shop_find' => $shop_find]);
                return view('seguri_search', ['shop_find' => $shop_find]);
            } elseif ($shop_find->store_name == "additive-manufacturing-consumables.myshopify.com") {
                return view('additive_manufacturing_search', ['shop_find' => $shop_find]);
            } else {
                return view('search', ['shop_find' => $shop_find]);
            }
        } else {
            return "Page Not Found";
        }
    }

    public function get_user_settings(Request $request) {
        $shop = $request['shop'];
        $settings = ShopModel::select('display_product_image_status', 'show_available_quantity', 'additional_css', 'show_out_of_stock_products')->where('store_name', $shop)->first();
        return $settings;
    }

    public function save_settings(Request $request) {

        if (session('shop')) {
            $shop = session('shop');
        } else {
            $shop = $_REQUEST['shop'];
        }
        DB::table('usersettings')->where('store_name', $shop)->update(
                ['quantity_status' => $request['quantity_status'],
                    'display_product_image_status' => $request['display_product_image_status'],
                    'additional_css' => $request['additional_css'],
                    'page_title_label' => $request['page_title_label'],
                    'product_name_label' => $request['product_name_label'],
                    'quantity_label' => $request['quantity_label'],
                    'cost_label' => $request['cost_label'],
                    'total_label' => $request['total_label'],
                    'allow_out_of_stock_products_to_order' => isset($request['allow_out_of_stock_products_to_order']) ? '1' : '0',
                    'show_out_of_stock_products' => isset($request['show_out_of_stock_products']) ? '1' : '0',
                    'show_available_quantity' => isset($request['show_available_quantity']) ? '1' : '0',
                    'available_quantity_label' => $request['available_quantity_label']]);

        $shop_find = ShopModel::where('store_name', $shop)->first();
        $shop_find->new_install = 'N';
        $shop_find->save();

        $notification = array(
            'message' => 'Settings Saved Successfully.',
            'alert-type' => 'success'
        );
        return redirect()->route('dashboard', ['shop' => $shop])->with('notification', $notification);
    }

    public function get_variant_quantity_by_id(Request $request) {
        $variantId = $_POST['variantId'];
        $sh = App::make('ShopifyAPI');
        $app_settings = DB::table('appsettings')->where('id', 1)->first();

        //$shop = session('shop');
        $shop = $request->input('shop_name');
        $select_store = DB::table('usersettings')->where('store_name', $shop)->get();

        $sh = App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $select_store[0]->access_token]);

        $url = 'https://' . $shop . '/admin/variants/' . $variantId . '.json';

        $variant_data = $sh->call([
            'URL' => $url,
            'METHOD' => 'GET'
                ], false);

        echo $variant_data->variant->inventory_quantity;
    }

    public function update_modal_status(Request $request) {
        $shop = $request->input('shop_name');
        $shop_find = ShopModel::where('store_name', $shop)->first();
        $shop_find->new_install = 'N';
        $shop_find->save();
    }

}
