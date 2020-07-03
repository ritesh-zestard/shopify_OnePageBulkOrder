<link rel="stylesheet" href="https://zestardshop.com/shopifyapp/bulkorder/public/js/jquery.fancybox.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.dataTables.min.css">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style> 
    .all_btn {
        width: 120px;
    }
    /*    .zestard_bulk_order_list_view {
            margin: 0px 12%;
        }*/
    .collapse.in {
        display: contents;
    }
    .collapse {
        display: none;
    }

    #show_options, .add_to_cart_btn, .sold_out {
        border: none;
        background-color: #403b37;
        color: #ffffff;
        box-shadow: none;
        color: #fff;
        display: inline-block;
        font-size: 13px;
        font-weight: 400;
        height: 36px;
        line-height: 36px;
        margin: 0;
        padding: 0 10px !important;
        text-transform: none;
        /*        width: 85%;
        border-radius: 2px;*/
        cursor: pointer;
        -webkit-appearance: button;
        
    }
    #product_list #show_options, .add_to_cart_btn, .sold_out{
        white-space: normal;
        height: 50px;
        line-height: 13px;
    }
    .shopping-cart-spof {
        position: fixed;
        background: rgba(0,0,0,.72);
        height: 50px;
        width: auto;
        top: 225px;
        right: 5px;
        background-image: url(https://singlepageorderform.com/app/images/cart1.png);
        background-size: 25px;
        background-repeat: no-repeat;
        background-position: 10px center;
        border-radius: 3px;
        padding: 5px 10px 0 45px;
        color: #fff;
        font-size: 15px;
        line-height: 35px;
        z-index: 1000;
    }
    .overlay{
        position: fixed; 
        display: none; 
        width: 100%; 
        height: 100%; 
        top: 0; 
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0,0,0,0.5);
        z-index: 2;
    }
    .ui-dialog 
    {
        position: fixed;
        top:50px;
        z-index: 9999;   
    }
    .quntity-input
    {
        width: 50%;
    }
    .table_product tr td
    {
        text-align: center;
    }
    .table_product tr th
    {
        text-align: center;
    }

    table.table_product
    {
        width: auto;
    }
    table.table_product tr td .quntity-input
    {
        width: 58%;
        margin: auto;
        text-align: center;
    }
    table.table_product tr td
    {
        padding: 10px 10px;
    }
    .product_image
    {
        height: 75px;width:75px;
    }
    .dataTables_wrapper .dataTables_processing {
        height: 70px !important;
    }
    div#product_list_paginate,
.dataTables_paginate.paging_simple_numbers {
    margin: 10px 0px;
}
    @media screen and (max-width: 640px) {
        div#product_list_wrapper .row .col-sm-12 {
            overflow-y: scroll;
        }
        #product_list{
            width: auto;
        }
    }
    @media screen and (max-width: 575px) {
        .ui-widget.ui-widget-content {
            width: 100% !important;
        }
    }
    
</style>

<div class="container"> 
    <div class="overlay"></div>
    <!--    <div id="crtDiv" class="shopping-cart-spof">20 items</div>-->
    <section class="items">  

        <div id="dialog" title="" class="items">
        </div>
        <div class="testb">
        <table id="product_list" class="table_product display responsive"> 
            <thead> 
                <tr style="color:white;background-color: <?php echo $app_config->header_background_color; ?>"> 
                    <?php 
                        $sort_order_array = json_decode($sort_order);
                        //print_r();
                    ?>
                    @foreach ($sort_order_array as $key => $value)
                        @if($value == "Product Image")
                            <th ><?php echo ($app_config->product_image_label == '') ? 'Product Image' : $app_config->product_image_label; ?></th>
                        @elseif($value == "Product Name")
                            <th><?php echo ($app_config->product_name_label == '') ? 'Product Name' : $app_config->product_name_label; ?></th> 
                        @elseif($value == "Product Price")
                            <th><?php echo ($app_config->product_price_label == '') ? 'Product Price' : $app_config->product_price_label; ?></th> 
                        @elseif($value == "Product Quantity")
                            <th><?php echo ($app_config->product_quantity_label == '') ? 'Product Quantity' : $app_config->product_quantity_label; ?></th>
                        @elseif($value == "Product SKU")
                            <?php if ($app_config->display_sku == 1) { ?>
                                <th><?php echo ($app_config->product_sku_label == '') ? 'Product SKU' : $app_config->product_sku_label; ?></th>
                            <?php } ?>
                        @else
                        @endif
                    @endforeach
                    <th style="width: 40%">Action</th> 
                </tr> 
            </thead>
            <tbody>
                <?php foreach ($products as $key => $row) { ?>
                        @foreach ($sort_order_array as $key2 => $value)
                            @if($value == "Product Image")
                                <td><img src="<?php echo (count($row->images) != 0) ? $row->images[0]->src : url('/images/no-image-available.png'); ?>" class="product_image"></td>
                            @elseif($value == "Product Name")
                                <td><?php echo $row->title; ?></td>
                            @elseif($value == "Product Price")
                                <td><?php echo $row->variants[0]->price; ?></td>
                            @elseif($value == "Product Quantity")
                                <td>
                                    <?php if (count($row->variants) == 1 && $row->variants[0]->inventory_quantity != 0) {
                                        ?>
                                        <input type="text" class="quntity-input" id="qty_outer_<?php echo $row->variants[0]->id; ?>" value="1" min="1" />
                                    <?php } ?>
                                </td>
                            @elseif($value == "Product SKU")
                                <?php if ($app_config->display_sku == 1) { ?>
                                    <td><?php echo ($row->variants[0]->sku != '') ? $row->variants[0]->sku : ''; ?></td>
                                <?php } ?>
                            @else
                            @endif
                        @endforeach
                        <?php if ($row->variants[0]->inventory_quantity == 0) { ?>
                            <td><button type="button" class="sold_out all_btn" disabled="disabled" style="color:<?php echo ($app_config->sold_out_text_color == '' ? '#ffffff' : $app_config->sold_out_text_color); ?> ;background-color:<?php echo ($app_config->sold_out_background_color == '' ? '#403b37' : $app_config->sold_out_background_color); ?>"><?php echo ($app_config->sold_out_text == '' ? 'Sold Out' : $app_config->sold_out_text); ?></button></td>
                        <?php } else { ?>
                            <td style="width: 26%" class="<?php echo $row->title; ?>"><?php echo count($row->variants) == 1 ? '<button type="button" class="button text-uc my-btn add_to_cart_btn all_btn" onclick="return addToCart(' . $row->variants[0]->id . ')" style="color:' . ($app_config->add_to_cart_text_color == '' ? '#ffffff' : $app_config->add_to_cart_text_color) . ';background-color:' . ($app_config->add_to_cart_background_color == '' ? '#403b37' : $app_config->add_to_cart_background_color) . '">' . ($app_config->add_to_cart_text == '' ? 'Add To Cart' : $app_config->add_to_cart_text) . '</button>' : '<input type="button" value="' . ($app_config->show_options_text == '' ? 'Show Options' : $app_config->show_options_text) . '" p_id=' . $row->id . ' class="all_btn fancybox show_option_' . $key . '" id="show_options" data-toggle="collapse" data-target="#demo_' . $key . '" style="color:' . ($app_config->show_options_text_color == '' ? '#ffffff' : $app_config->show_options_text_color) . ';background-color:' . ($app_config->show_options_background_color == '' ? '#403b37' : $app_config->show_options_background_color) . '">'; ?>                
                            </td>
                        <?php } ?>
                        
                    </tr>

                <?php } ?>
            </tbody> 
        </table>
        </div>
    </section>
</div>

<script src="https://code.jquery.com/jquery.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script type="text/javascript" src="{{ asset('js/flyto.js') }}"></script>

<script type="text/javascript">
//var ztpl_one_page_quick_order = jQuery;
$ztpl_one_page_quick_order(function () {
    $ztpl_one_page_quick_order(".fancybox").on("click", function () {
        startloader(1);
        var product_id = $ztpl_one_page_quick_order(this).attr('p_id');
        var product_name = $ztpl_one_page_quick_order(this).parent().attr('class');
        jQuery.ajax({
            url: "{{url('show_variants')}}",
            method: "POST",
            data: {product_id: product_id},
            success: function (data)
            {
                $ztpl_one_page_quick_order("#dialog").html(data);
                $ztpl_one_page_quick_order('.ui-dialog-title').html(product_name);
                //alert("test");
                $ztpl_one_page_quick_order("#dialog").dialog(
                {
                    height: 700,
                    width: 760
                });
                startloader(0);
            }
        });

    });
});
function addToCart(id)
{
    var qt = $ztpl_one_page_quick_order("#qty_outer_" + id).val();
    $ztpl_one_page_quick_order.ajax({
        type: 'POST',
        url: '/cart/add.js',
        data: 'quantity=' + qt + '&id=' + id,
        dataType: 'json',
        success: function (response) {
            $ztpl_one_page_quick_order.ajax({
                type: 'GET',
                url: '/cart/update.js',
                dataType: 'json',
                success: function (cart) {
                    alert('There are now ' + cart.item_count + ' items in the cart.');
                }
            });
        }
    });
}

$ztpl_one_page_quick_order(document).ready(function () {

    //$ztpl_one_page_quick_order("#product_list").dataTable();
    $ztpl_one_page_quick_order("#product_list").dataTable({
        "processing": true,
        "serverSide": true,
        "ordering": false,
        "responsive": true,
		"info":false,
        "dom": "<'row'<'col-sm-3'l><'col-sm-3'f><'col-sm-6'p>>" +
"<'row'<'col-sm-12'tr>>" +
"<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "ajax": "{{url('get_all_product')}}",
        "language": {
        "processing": "Loading. Please wait..."
        },
        "fnDrawCallback": function( oSettings ) {
              bind_ajax_table();
        }
    });
    
    // $ztpl_one_page_quick_order("#product_list").dataTable({
    //     "lengthChange": false,
    //     "bInfo" : false,
    //     "ajax": "{{url('get_all_product')}}"
    // });
    function bind_ajax_table(){
        $ztpl_one_page_quick_order('.items').flyto({
            item: 'tr',
            target: '.icon-cart',
            button: '.my-btn',
            shake: true
        });
        
        $ztpl_one_page_quick_order(".fancybox").on("click", function () {
            startloader(1);
            var product_id = $ztpl_one_page_quick_order(this).attr('p_id');
            var product_name = $ztpl_one_page_quick_order(this).parent().attr('class');
            jQuery.ajax({
                url: "{{url('show_variants')}}",
                method: "POST",
                data: {product_id: product_id},
                success: function (data)
                {
                    $ztpl_one_page_quick_order("#dialog").html(data);
                    $ztpl_one_page_quick_order('.ui-dialog-title').html(product_name);
                    jQuery("#dialog").dialog(
                    {
                        height: 700,
                        width: 760
                    });
                    startloader(0);
                }
            });

        });
    }
    $ztpl_one_page_quick_order('.items').flyto({
        item: 'tr',
        target: '.icon-cart',
        button: '.my-btn',
        shake: true
    });
});


function startloader(process) {
    if (process == 1) {
        $ztpl_one_page_quick_order(".overlay").css({
            'display': 'block',
            'background-image': 'url({{ asset("images/ajax-loader.gif") }})',
            'background-repeat': 'no-repeat',
            'background-attachment': 'fixed',
            'background-position': 'center'
        });
    } else {
        $ztpl_one_page_quick_order(".overlay").css({
            'display': 'none',
            'background-image': 'none',
        });
    }
}


</script>
