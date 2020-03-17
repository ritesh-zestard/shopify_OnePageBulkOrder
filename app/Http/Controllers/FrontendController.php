<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AddNewsForm;
use App;
use DB;

class FrontendController extends Controller {

    public function __construct() {
        $this->apiVersion = env('API_VERSION');
    }

    public function frontend(request $request) {
        $shop = session('shop');
        $app_settings = DB::table('appsettings')->where('id', 1)->first();
        $select_store = DB::table('usersettings')->where('store_name', $shop)->get();


        $sh = App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $select_store[0]->access_token]);

        $url = 'https://' . $shop . '/admin/api/' . $this->apiVersion . '/products.json';

        $count = $sh->callAdvanceAdvance(['URL' => '/admin/api/' . $this->apiVersion . '/products/count.json', 'METHOD' => 'GET']);

        $app_config = DB::table('appconfig')->where('store_id', $select_store[0]->id)->first();

        //$products = $sh->callAdvance(['URL' => $url, 'METHOD' => 'GET'], false);

        $get_products = array();

        if ($count->count > 0) {
            $pages = ceil($count->count / 10);

            for ($i = 0; $i < $pages; $i++) {
                $url = 'https://' . $shop . '/admin/api/' . $this->apiVersion . '/products.json?limit=10&page=' . ($i + 1);

                $products = $sh->callAdvance(['URL' => $url, 'METHOD' => 'GET'], FALSE);

                foreach ($products->products as $row) {
                    $get_products[] = $row;
                }
            }
        }
        //dd($get_products);
        // $products = $sh->callAdvance(['URL' => $url, 'METHOD' => 'GET'], FALSE);
        // $get_products = $products->products;
        //dd($get_products);
        if ($select_store[0]->status == "active" and $app_config->app_status == 1) {
            return view('front_view', ['products' => $get_products, 'app_config' => $app_config]);
        }

        //return view('front_view', ['products' => $get_products, 'app_config' => $app_config]);
    }

    public function show_variants() {
        $shop = session('shop');
        $app_settings = DB::table('appsettings')->where('id', 1)->first();
        if ($shop == 'yoganastix.com') {
            $shop = 'yoganastix.myshopify.com';
        }

        if ($shop == 'hopsandnuts.com') {
            $shop = 'hops-and-nuts-craft-beer-snacks.myshopify.com';
        }

        $select_store = DB::table('usersettings')->where('store_name', $shop)->get();
        $app_config = DB::table('appconfig')->where('store_id', $select_store[0]->id)->first();

        $sh = App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $select_store[0]->access_token]);
        $product_id = $_POST['product_id'];
        $url = 'https://' . $shop . '/admin/api/' . $this->apiVersion . '/' . $product_id . '.json';
        $row = $sh->callAdvance(['URL' => $url, 'METHOD' => 'GET'], FALSE);
        echo '<section class="items">';
        echo '<table id="sub_product" class="table_product">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Image</th>';
        foreach ($row->product->options as $option) {
            echo '<th>' . ucfirst($option->name) . '</th>';
        }
        echo '<th>Price</th>';
        echo '<th>Quantity</th>';
        echo ($app_config->display_sku == 1) ? '<th>SKU</th>' : '';
        echo '<th>Action</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';


        foreach ($row->product->variants as $row_variant) {
            ?>
            <tr>
                <td>
                    <?php
                    $flag = 0;
                    foreach ($row->product->images as $row_image) {
                        if ($row_image->id == $row_variant->image_id) {
                            $flag = 1;
                            $img_src = $row_image->src;
                        }
                    }
                    if ($flag == 1) {
                        ?>
                        <img src="<?php echo $img_src; ?>" class="product_image">
                    <?php } else {
                        ?>
                        <img src="<?php echo url('/images/no-image-available.png'); ?>" class="product_image">
                        <?php
                    }
                    ?>
                </td>
                <?php echo ($row_variant->option1 != '') ? '<td>' . $row_variant->option1 . '</td>' : ''; ?>
                <?php echo ($row_variant->option2 != '') ? '<td>' . $row_variant->option2 . '</td>' : ''; ?>
                <?php echo ($row_variant->option3 != '') ? '<td>' . $row_variant->option3 . '</td>' : ''; ?>
                <td><?php echo $row_variant->price; ?></td>
                <td>
                    <?php
                    // echo $row_variant->inventory_quantity;
                    if ($row_variant->inventory_quantity == 0) {
                        echo '';
                    } else {
                        echo '<input type="text" class="quntity-input" id="qty_' . $row_variant->id . '" value="1" min="1" />';
                    }
                    ?>                    
                </div>
            </td>
            <?php echo ($app_config->display_sku == 1) ? '<td>' . $row_variant->sku . '</td>' : ''; ?>
            <td><?php echo ($row_variant->inventory_quantity == 0) ? '<input type="button" value="Sold Out" class="all_btn add_to_cart_btn" disabled="disabled" style="color:' . ($app_config->sold_out_text_color == '' ? '#ffffff' : $app_config->sold_out_text_color) . ';background-color:' . ($app_config->sold_out_background_color == '' ? '#403b37' : $app_config->sold_out_background_color) . '">' : '<input type="button" value="' . ($app_config->add_to_cart_text == '' ? 'Add To Cart' : $app_config->add_to_cart_text) . '" class="all_btn my-btn add_to_cart_btn" onclick="return addToCart(' . $row_variant->id . ')" style="color:' . ($app_config->add_to_cart_text_color == '' ? '#ffffff' : $app_config->add_to_cart_text_color) . ';background-color:' . ($app_config->add_to_cart_background_color == '' ? '#403b37' : $app_config->add_to_cart_background_color) . '">' ?></td>
            </tr>
            <?php
        }
        echo '</tbody>';
        echo '</table>';
        echo '</section>';
        ?>
        <script>

            function addToCart(id)
            {
                //console.log($ztpl_one_page_quick_order);
                var qt = $ztpl_one_page_quick_order("#qty_" + id).val();
                $ztpl_one_page_quick_order.ajax({
                    type: 'POST',
                    url: '/cart/add.js',
                    data: 'quantity=' + qt + '&id=' + id,
                    dataType: 'json',
                    success: function (response) {
                        $ztpl_one_page_quick_order.ajax({
                            type: 'GET',
                            url: '/cart.js',
                            dataType: 'json',
                            success: function (cart) {
                                alert('There are now ' + cart.item_count + ' items in the cart.');
                            }
                        });
                    }
                });
            }
            $ztpl_one_page_quick_order('.items').flyto({
                item: 'tr',
                target: '.icon-cart',
                button: '.my-btn',
                shake: true
            });

        </script>
        <?php
    }

    public function product_displays() {
        $shop = session('shop');
        $app_settings = DB::table('appsettings')->where('id', 1)->first();
        $select_store = DB::table('usersettings')->where('store_name', $shop)->get();
        $sh = App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $select_store[0]->access_token]);
        $url = 'https://' . $shop . '/admin/api/' . $this->apiVersion . '/products.json';
        $count = $sh->callAdvance(['URL' => '/admin/api/' . $this->apiVersion . '/products/count.json', 'METHOD' => 'GET']);
        $product_count = $count->count;
        $draw = $_REQUEST['draw'];
        $search = $_REQUEST['search']['value'];
        $start = $_REQUEST['start'];
        $total_products = array('draw' => $draw, 'recordsTotal' => $product_count, 'recordsFiltered' => $product_count);
        $products = $sh->callAdvance(['URL' => $url, 'METHOD' => 'GET'], false);

        if ($search) {
            $pages = ceil($product_count / 250);
            $limit = 250;
            for ($i = 0; $i < $pages; $i++) {
                $current_page = $i + 1;
                $product_list = $sh->callAdvance(['URL' => '/admin/api/' . $this->apiVersion . '/products.json?limit=' . $limit . '&page=' . $current_page, 'METHOD' => 'GET']);
                //echo "<pre>"; print_r($product_list); die;
                foreach ($product_list->products as $product) {
                    if (stristr($product->title, $search)) {
                        $total_products['data'][] = array($product->title, $product->images[0]->src, $product->variants[0]->price, $product->variants[0]->inventory_quantity, $product->variants[0]->sku);
                    }
                }
            }
        } else {

            foreach ($products->products as $product) {
                //echo "<pre>";print_r($product);            
                $total_products['data'][] = array($product->title, $product->images[0]->src, $product->variants[0]->price, $product->variants[0]->inventory_quantity, $product->variants[0]->sku);
            }
        }

        return json_encode($total_products);
    }

    public function get_all_product(request $request) {
        //dd('test');
        $shop = session('shop');
        $limit = $request['length'];
        $draw = $request['draw'];
        $start = $request['start'];
        $length = $request['length'];
        $current_page = ceil($start / $limit) + 1;
        $search = $request['search']['value'];
        if ($shop == 'bewickedusa.com') {
            $shop = 'bewicked.myshopify.com';
        }
        $app_settings = DB::table('appsettings')->where('id', 1)->first();

        if ($shop == 'yoganastix.com') {
            $shop = 'yoganastix.myshopify.com';
        }

        if ($shop == 'hopsandnuts.com') {
            $shop = 'hops-and-nuts-craft-beer-snacks.myshopify.com';
        }
        if ($shop == 'baketreats.com') {
            $shop = 'baketreats-inc.myshopify.com';
        }
        $select_store = DB::table('usersettings')->where('store_name', $shop)->get();

        $app_config = DB::table('appconfig')->where('store_id', $select_store[0]->id)->first();
        $sort_order = DB::table('field_sorting_details')->where('shop_id', $select_store[0]->id)->first();
        //dd($sort_order->sort_order);
        $sh = App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $select_store[0]->access_token]);
        //dd($sh);
        $url = 'https://' . $shop . '/admin/api/' . $this->apiVersion . '/products.json';

        $count = $sh->callAdvance(['URL' => '/admin/api/' . $this->apiVersion . '/products/count.json', 'METHOD' => 'GET']);

        $get_products = array();
        if ($count->count > 0) {
            //for the search
            if ($search) {
                $key = 0;
                $pages = ceil($count->count / 250);
                $limit = 250;
                $i = 0;
                do {
                        if ($i == 0) {
                            $products = $sh->callAdvance(['URL' => '/admin/api/' . $this->apiVersion . '/products.json?limit=' . $limit . '&page_info=', 'METHOD' => 'GET']);
                        } else {
                            $products = $sh->callAdvance(['URL' => '/admin/api/' . $this->apiVersion . '/products.json?limit=' . $limit . '&page_info=' . $params['page_info'], 'METHOD' => 'GET']);
                        } 
                        $i++;
			

                        foreach ($products->products as $row) {
							
                            if (stristr($row->title, $search)) {
								
                                //for image
                                if (count($row->images) != 0) {
                                    $image_url = '<img src="' . $row->images[0]->src . '" class="product_image">';
                                } else {
                                    $image_url = '<img src="' . url('/images/no-image-available.png') . '" class="product_image">';
                                }

                                //for the button
                                if ($row->variants[0]->inventory_quantity == 0) {
                                    $action_field = '<button type="button" class="sold_out all_btn" disabled="disabled" style="color:' . ($app_config->sold_out_text_color == '' ? '#ffffff' : $app_config->sold_out_text_color) . ';background-color:' . ($app_config->sold_out_background_color == '' ? '#403b37' : $app_config->sold_out_background_color) . '">' . ($app_config->sold_out_text == '' ? 'Sold Out' : $app_config->sold_out_text) . '</button>';
                                } else {
                                    if (count($row->variants) == 1) {
                                        $action_field = '<button type="button" class="button text-uc my-btn add_to_cart_btn all_btn" onclick="return addToCart(' . $row->variants[0]->id . ')" style="width:auto;color:' . $app_config->add_to_cart_text_color . ';background-color:' . $app_config->add_to_cart_background_color . '">' . $app_config->add_to_cart_text . '</button>';
                                    } else {
                                        $action_field = '<input type="button" value="' . $app_config->show_options_text . '" p_id=' . $row->id . ' class="all_btn fancybox show_option_' . $key . '" id="show_options" data-toggle="collapse" data-target="#demo_' . $key . '" style="color:' . $app_config->show_options_text_color . ';background-color:' . $app_config->show_options_background_color . '">';
                                    }
                                }

                                //for quantity
                                if (count($row->variants) == 1 && $row->variants[0]->inventory_quantity != 0) {
                                    $quantity_field = '<input type="text" class="quntity-input" id="qty_outer_' . $row->variants[0]->id . '" value="1" min="1" />';
                                } else {
                                    $quantity_field = "";
                                }

                                //for sku
                                if ($app_config->display_sku == 1) {
                                    $product_sku = $row->variants[0]->sku;
                                } else {
                                    $product_sku = "";
                                }

                                $sort_order_array = json_decode($sort_order->sort_order);

                                if ($app_config->display_sku == 1) {
                                    $new_row = $this->return_sequence_with_sku($sort_order_array, $image_url, $row->title, $row->variants['0']->price, $quantity_field, $product_sku, $action_field);
                                } else {
                                    $new_row = $this->return_sequence_without_sku($sort_order_array, $image_url, $row->title, $row->variants['0']->price, $quantity_field, $action_field);
                                }
								
									
                                //dd($new_row);
                                //$new_row = array($image_url,$row->title,$row->variants['0']->price,$quantity_field,$row->variants[0]->sku,$action_field);
                                $get_products['data'][] = $new_row;
                            }
                            $key++;
                        }
                        //dd($products);
                    if (isset($product_list->headers['Link'])) {
                        $next = $product_list->headers['Link'];
                        $page_params = explode(',', $next);
                        if (isset($page_params[1])) {
                            $next_url = explode(',', $page_params[1]);
                        } else {
                            $next_url = explode(',', $page_params[0]);
                        }
                        $str = substr($next_url[0], 1, -1);
                        $url_components = parse_url(substr($str, 0, strpos($str, ">")));
                        parse_str($url_components['query'], $params);
                    }
                } while (isset($page_params) && (count($page_params) == 2 || $i == 1));
            } else {
                $i = 0;
                do {
                    $pages = ceil($count->count / 10);
                    $key = 0;
                    if ($i == 0) {
                        $url = 'https://' . $shop . '/admin/api/' . $this->apiVersion . '/products.json?limit=' . $limit . '&page_info=';
                    } else {
                        $url = 'https://' . $shop . '/admin/api/' . $this->apiVersion . '/products.json?limit=' . $limit . '&page_info=' . $params['page_info'];
                    }
                    $products = $sh->callAdvance(['URL' => $url, 'METHOD' => 'GET'], FALSE);
                    $i++;
                    foreach ($products->products as $row) {
                        //for image
                        if (count($row->images) != 0) {
                            $image_url = '<img src="' . $row->images[0]->src . '" class="product_image">';
                        } else {
                            $image_url = '<img src="' . url('/images/no-image-available.png') . '" class="product_image">';
                        }
                        //for the button
                        if ($row->variants[0]->inventory_quantity == 0) {
                            $action_field = '<button type="button" class="sold_out all_btn" disabled="disabled" style="color:' . ($app_config->sold_out_text_color == '' ? '#ffffff' : $app_config->sold_out_text_color) . ';background-color:' . ($app_config->sold_out_background_color == '' ? '#403b37' : $app_config->sold_out_background_color) . '">' . ($app_config->sold_out_text == '' ? 'Sold Out' : $app_config->sold_out_text) . '</button>';
                        } else {
                            if (count($row->variants) == 1) {
                                $action_field = '<button type="button" class="button text-uc my-btn add_to_cart_btn all_btn" onclick="return addToCart(' . $row->variants[0]->id . ')" style="color:' . $app_config->add_to_cart_text_color . ';background-color:' . $app_config->add_to_cart_background_color . '">' . $app_config->add_to_cart_text . '</button>';
                            } else {
                                $action_field = '<input type="button" value="' . $app_config->show_options_text . '" p_id=' . $row->id . ' class="all_btn fancybox show_option_' . $key . '" id="show_options" data-toggle="collapse" data-target="#demo_' . $key . '" style="color:' . $app_config->show_options_text_color . ';background-color:' . $app_config->show_options_background_color . '">';
                            }
                        }

                        //for quantity
                        if (count($row->variants) == 1 && $row->variants[0]->inventory_quantity != 0) {
                            $quantity_field = '<input type="text" class="quntity-input" id="qty_outer_' . $row->variants[0]->id . '" value="1" min="1" />';
                        } else {
                            $quantity_field = "";
                        }

                        //for sku
                        if ($app_config->display_sku == 1) {
                            $product_sku = $row->variants[0]->sku;
                        } else {
                            $product_sku = "";
                        }

                        $sort_order_array = json_decode($sort_order->sort_order);

                        if ($app_config->display_sku == 1) {
                            $new_row = $this->return_sequence_with_sku($sort_order_array, $image_url, $row->title, $row->variants['0']->price, $quantity_field, $product_sku, $action_field);
                        } else {
                            $new_row = $this->return_sequence_without_sku($sort_order_array, $image_url, $row->title, $row->variants['0']->price, $quantity_field, $action_field);
                        }
                        $get_products['data'][] = $new_row;
                        $key ++;
                    }
                    if (isset($products->headers['Link'])) {
                        $next = $products->headers['Link'];
                        $page_params = explode(',', $next);
                        if (isset($page_params[1])) {
                            $next_url = explode(',', $page_params[1]);
                        } else {
                            $next_url = explode(',', $page_params[0]);
                        }
                        $str = substr($next_url[0], 1, -1);
                        $url_components = parse_url(substr($str, 0, strpos($str, ">")));
                        parse_str($url_components['query'], $params);
                    }
                } while (isset($page_params) && (count($page_params) == 2 || $i == 1));
            }

            $return_arr = array();
            for ($i = $start; $i < $start + $length; $i++) {
                if (isset($get_products['data'][$i])) {
                    $return_arr[] = $get_products['data'][$i];
                } else {
                    break;
                }
            }
            $get_products['data'] = $return_arr;
            return json_encode($get_products);
        }
    }

    public function return_sequence_with_sku($sort_order_array, $image_url, $title, $price, $quantity, $sku, $action_field) {

        foreach ($sort_order_array as $sort_key => $field) {
            //for set the first field
            if ($sort_key == 0 && $field == "Product Image") {
                $field_1 = $image_url;
            } elseif ($sort_key == 0 && $field == "Product Name") {
                $field_1 = $title;
            } elseif ($sort_key == 0 && $field == "Product Price") {
                $field_1 = $price;
            } elseif ($sort_key == 0 && $field == "Product Quantity") {
                $field_1 = $quantity;
            } elseif ($sort_key == 0 && $field == "Product SKU") {
                $field_1 = $sku;
            } else {
                
            }

            //for set the Second field
            if ($sort_key == 1 && $field == "Product Image") {
                $field_2 = $image_url;
            } elseif ($sort_key == 1 && $field == "Product Name") {
                $field_2 = $title;
            } elseif ($sort_key == 1 && $field == "Product Price") {
                $field_2 = $price;
            } elseif ($sort_key == 1 && $field == "Product Quantity") {
                $field_2 = $quantity;
            } elseif ($sort_key == 1 && $field == "Product SKU") {
                $field_2 = $sku;
            } else {
                
            }

            //for set the Third field
            if ($sort_key == 2 && $field == "Product Image") {
                $field_3 = $image_url;
            } elseif ($sort_key == 2 && $field == "Product Name") {
                $field_3 = $title;
            } elseif ($sort_key == 2 && $field == "Product Price") {
                $field_3 = $price;
            } elseif ($sort_key == 2 && $field == "Product Quantity") {
                $field_3 = $quantity;
            } elseif ($sort_key == 2 && $field == "Product SKU") {
                $field_3 = $sku;
            } else {
                
            }

            //for set the Fourth field
            if ($sort_key == 3 && $field == "Product Image") {
                $field_4 = $image_url;
            } elseif ($sort_key == 3 && $field == "Product Name") {
                $field_4 = $title;
            } elseif ($sort_key == 3 && $field == "Product Price") {
                $field_4 = $price;
            } elseif ($sort_key == 3 && $field == "Product Quantity") {
                $field_4 = $quantity;
            } elseif ($sort_key == 3 && $field == "Product SKU") {
                $field_4 = $sku;
            } else {
                
            }

            //for set the Fifth field
            if ($sort_key == 4 && $field == "Product Image") {
                $field_5 = $image_url;
            } elseif ($sort_key == 4 && $field == "Product Name") {
                $field_5 = $title;
            } elseif ($sort_key == 4 && $field == "Product Price") {
                $field_5 = $price;
            } elseif ($sort_key == 4 && $field == "Product Quantity") {
                $field_5 = $quantity;
            } elseif ($sort_key == 4 && $field == "Product SKU") {
                $field_5 = $sku;
            } else {
                
            }
        }
        $sequence_data_array = array($field_1, $field_2, $field_3, $field_4, $field_5, $action_field);

        return $sequence_data_array;
    }

    public function return_sequence_without_sku($sort_order_array, $image_url, $title, $price, $quantity, $action_field) {
        array_splice($sort_order_array, array_search("Product SKU", $sort_order_array), 1);
        foreach ($sort_order_array as $sort_key => $field) {
            //for set the first field
            if ($sort_key == 0 && $field == "Product Image") {
                $field_1 = $image_url;
            } elseif ($sort_key == 0 && $field == "Product Name") {
                $field_1 = $title;
            } elseif ($sort_key == 0 && $field == "Product Price") {
                $field_1 = $price;
            } elseif ($sort_key == 0 && $field == "Product Quantity") {
                $field_1 = $quantity;
            } else {
                
            }

            //for set the Second field
            if ($sort_key == 1 && $field == "Product Image") {
                $field_2 = $image_url;
            } elseif ($sort_key == 1 && $field == "Product Name") {
                $field_2 = $title;
            } elseif ($sort_key == 1 && $field == "Product Price") {
                $field_2 = $price;
            } elseif ($sort_key == 1 && $field == "Product Quantity") {
                $field_2 = $quantity;
            } else {
                
            }
            //for set the Third field
            if ($sort_key == 2 && $field == "Product Image") {
                $field_3 = $image_url;
            } elseif ($sort_key == 2 && $field == "Product Name") {
                $field_3 = $title;
            } elseif ($sort_key == 2 && $field == "Product Price") {
                $field_3 = $price;
            } elseif ($sort_key == 2 && $field == "Product Quantity") {
                $field_3 = $quantity;
            } else {
                
            }

            //for set the Fourth field
            if ($sort_key == 3 && $field == "Product Image") {
                $field_4 = $image_url;
            } elseif ($sort_key == 3 && $field == "Product Name") {
                $field_4 = $title;
            } elseif ($sort_key == 3 && $field == "Product Price") {
                $field_4 = $price;
            } elseif ($sort_key == 3 && $field == "Product Quantity") {
                $field_4 = $quantity;
            } else {
                
            }
        }

        $sequence_data_array = array($field_1, $field_2, $field_3, $field_4, $action_field);

        return $sequence_data_array;
    }

    public function frontend2(request $request) {
        //dd('test');
        $shop = session('shop');
        $app_settings = DB::table('appsettings')->where('id', 1)->first();
        if ($shop == 'hopsandnuts.com') {
            $shop = 'hops-and-nuts-craft-beer-snacks.myshopify.com';
        }
        if ($shop == 'bewickedusa.com') {
            $shop = 'bewicked.myshopify.com';
        }
        if ($shop == 'yoganastix.com') {
            $shop = 'yoganastix.myshopify.com';
        }

        if ($shop == 'baketreats.com') {
            $shop = 'baketreats-inc.myshopify.com';
        }

        $select_store = DB::table('usersettings')->where('store_name', $shop)->get();
        $sh = App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $select_store[0]->access_token]);
        $url = 'https://' . $shop . '/admin/api/' . $this->apiVersion . '/products.json';
        $count = $sh->callAdvance(['URL' => '/admin/api/' . $this->apiVersion . '/products/count.json', 'METHOD' => 'GET']);

        $app_config = DB::table('appconfig')->where('store_id', $select_store[0]->id)->first();
        //$products = $sh->callAdvance(['URL' => $url, 'METHOD' => 'GET'], false);
        $sort_order = DB::table('field_sorting_details')->where('shop_id', $select_store[0]->id)->first();
        //dd($sort_order->sort_order);
        $get_products = array();
        $url = 'https://' . $shop . '/admin/api/' . $this->apiVersion . '/products.json?limit=10&&page_info=';


        $products = $sh->callAdvance(['URL' => $url, 'METHOD' => 'GET'], FALSE);
        foreach ($products->products as $row) {
            $get_products[] = $row;
        }

        if ($select_store[0]->status == "active" and $app_config->app_status == 1) {
            return view('front_view', ['products' => $get_products, 'app_config' => $app_config, 'sort_order' => $sort_order->sort_order]);
        }

        //return view('front_view', ['products' => $get_products, 'app_config' => $app_config]);
    }

}
