@extends('header')
@section('content')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://zestardshop.com/shopifyapp/one_page_quick_order/public/js/jquery-ui.js"></script>
<style>
    .copystyle_wrapper
    {
        position : relative;
        width : 60%;
    }
    btn#copy_script {
        background-color: #52c1bc;
        border: 0;
        font-size: 12px;
        border: 1px solid #52c1bc;
        right: 0px;
        top: 0px;
        padding: 14px;
    }

    .connected-sortable {
        margin: 0 auto;
        list-style: none;
        width: 90%;
    }

    li.draggable-item {
    width: inherit;
    padding: 15px 20px;
    background-color: #f5f5f5;
    -webkit-transition: transform .25s ease-in-out;
    -moz-transition: transform .25s ease-in-out;
    -o-transition: transform .25s ease-in-out;
    transition: transform .25s ease-in-out;
    
    -webkit-transition: box-shadow .25s ease-in-out;
    -moz-transition: box-shadow .25s ease-in-out;
    -o-transition: box-shadow .25s ease-in-out;
    transition: box-shadow .25s ease-in-out;
    &:hover {
        cursor: pointer;
        background-color: #eaeaea;
    }
    }
    /* styles during drag */
    li.draggable-item.ui-sortable-helper {
        background-color: #e5e5e5;
        -webkit-box-shadow: 0 0 8px rgba(53,41,41, .8);
        -moz-box-shadow: 0 0 8px rgba(53,41,41, .8);
        box-shadow: 0 0 8px rgba(53,41,41, .8);
        transform: scale(1.015);
        z-index: 100;
    }
    li.draggable-item.ui-sortable-placeholder {
        background-color: #ddd;
        -moz-box-shadow:    inset 0 0 10px #000000;
        -webkit-box-shadow: inset 0 0 10px #000000;
        box-shadow:         inset 0 0 10px #000000;
    }
</style>
<?php
if (!session('shop')) {
    $shop = session('shop');
} else if(isset($_REQUEST['shop'])) {
    $shop = $_REQUEST['shop'];
}else{
    $shop = "";
}        
?> 
<script type="text/javascript">
    ShopifyApp.ready(function (e) {
        ShopifyApp.Bar.initialize({
            buttons: {
                primary: {
                    label: 'Quick Order Settings Demo',
                    callback: function(){ introJs().start(); }
                },
                secondary: [
                    {
                        label: 'Quick order Help',
                        href: '{{ url('quick_help') }}?shop=<?php echo $shop; ?>',
                        loading: true
                    },
                    {
                        label: 'Bulk order Settings',
                        href: '{{ url('dashboard') }}?shop=<?php echo $shop; ?>',
                        loading: true
                    }
                    // {
                    //     label: 'General Settings Demo',
                    //     callback: function(){ introJs().start(); } 
                    // }
                ]
            }
        });
    });

    $(document).ready(function () {
        var myintro = introJs();
        var demo_status = '{{ $intro_status }}';
        if(demo_status == 1){
            myintro.start();
            myintro.oncomplete(function() {
                window.top.location.href="{{ url("update_demo_status") }}";
            }).onexit(function() {
                window.top.location.href="{{ url("update_demo_status") }}";
            });
        }
        $(".slide_down").click(function () {
            var display = $("#shortcode_info").css("display");
            $("#shortcode_info").slideToggle();
            if (display == "none")
            {
                $(".slide_down i").removeClass("fa fa-chevron-up");
                $(".slide_down i").addClass("fa fa-chevron-down");
            } else
            {
                $(".slide_down i").removeClass("fa fa-chevron-down");
                $(".slide_down i").addClass("fa fa-chevron-up");
            }
        });
        $(".close_box").click(function () {
            var display = $("#shortcode_info").css("display");
            $("#shortcode_info").slideToggle();
            if (display == "none")
            {
                $(".slide_down i").removeClass("fa fa-chevron-up");
                $(".slide_down i").addClass("fa fa-chevron-down");
            } else
            {
                $(".slide_down i").removeClass("fa fa-chevron-down");
                $(".slide_down i").addClass("fa fa-chevron-up");
            }
        });
    });
</script>

<style>
    /*    .sub-heading
        {
            border-bottom: 2px solid lightgray;
        }*/
    /*    .label_setting
        {
            width: 33%;
            display: inline-block;
        }*/
</style>

<?php
$store_name = session('shop');
?>

<?php
if (Session::has('shop')) {
    $app_page = "https://" . session('shop') . "/pages/one-page-quick-order";
    $pages = "https://" . session('shop') . "/admin/pages";
} else if(isset($_REQUEST['shop'])) {
    $app_page = "https://" . $shop . "/pages/one-page-quick-order";
    $pages = "https://" .$shop . "/admin/pages";
} else {
    $app_page = "#";
    $pages = "#";
}
?>

<form action="{{ url('quick_order_dashboard_store') }}?shop=<?php echo $shop; ?>" method="POST" data-shopify-app-submit="form_submit" data-toggle="validator" id="commentForm">
    {{ csrf_field() }}
    <div class="formcolor">                    
        <br>
        <h2 class="sub-heading">General Settings</h2>
        <div class="row formcolor_row">
            <div class="col-sm-4 padding_left" >
                <strong>App Active?</strong>
                <span class="onoff"><input type="checkbox" name ="app_status" value="1" id="app_active" @if(count($app_setting) > 0) @if($app_setting->app_status == 1)  {{ "checked" }} @endif @endif><label for="app_active"></label></span>
            </div>    
        </div>

        <div class="row formcolor_row">
            <div class="col-md-4 padding_left">
                <div class="label_setting">
                    <div data-step="1" data-intro="This section will allow to enter Header Label information which you would like to display in the One Page Quick Order page.">
                        <h3 class="sub-heading">Label Settings</h3>
                        <div class="form-group">
                            {{-- <label>Product Name *</label>
                            <div class="help-tip">
                                <p>Enter text to show as a <b>Product Name label</b>.</p>
                            </div> --}}
                            <input type="text" class="form-control" name = "product_name_label" @if(count($app_setting) > 0) value="{{$app_setting->product_name_label}}" @else value="" @endif required>
                            
                        </div>
                        <div class="form-group">
                            {{-- <label>Product Image *</label>
                            <div class="help-tip">
                                <p>Enter text to show as a <b>Product Image label</b>.</p>
                            </div> --}}
                            <input type="text" class="form-control" name = "product_image_label" @if(count($app_setting) > 0) value="{{$app_setting->product_image_label}}" @else value="" @endif required>
                        </div>
                        <div class="form-group">
                            {{-- <label>Product Price *</label>
                            <div class="help-tip">
                                <p>Enter text to show as a <b>Product Price label</b>.</p>
                            </div> --}}
                            <input type="text" class="form-control" name = "product_price_label" @if(count($app_setting) > 0) value="{{$app_setting->product_price_label }}" @else value="" @endif required>
                        </div>
                        <div class="form-group">
                            {{-- <label>Product Quantity *</label>
                            <div class="help-tip">
                                <p>Enter text to show as a <b>Product Quantity label</b>.</p>
                            </div> --}}
                            <input type="text" class="form-control" name = "product_quantity_label" @if(count($app_setting) > 0) value="{{$app_setting->product_quantity_label }}" @else value="" @endif required>
                        </div>
                        <div class="form-group">
                            {{-- <label>Product SKU *</label>
                            <div class="help-tip">
                                <p>Enter text to show as a <b>Product SKU label</b>.</p>
                            </div> --}}
                            <input type="text" class="form-control" name = "product_sku_label" @if(count($app_setting) > 0) value="{{$app_setting->product_sku_label }}" @else value="" @endif required>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="display_sku" value="0">
                            <label>Show SKU To Customer &nbsp;&nbsp;<input type="checkbox" style="visibility:visible;" name = "display_sku" value=1 {{ ($app_setting->display_sku == 1)?'checked':'' }}></label>
                            {{-- <div class="help-tip">
                                <p>Keep this selected if you want to display <b>SKU field</b>.</p>
                            </div> --}}
                        </div>
                    </div>
                    <div class="form-group" data-step="4" data-intro="You will be able to maintain and manage the sorting order of the fields to display in the front-end of One Page Quick Order page.  ">
                        <?php 
                          $order_id = 0;
                            $fields = json_decode($sort_order->sort_order);
                        ?>
                        <label>Select display order for all fields</label>
                        <ul class="connected-sortable droppable-area2">
                            @foreach($fields as $field)
                            <li id="{{ $field }}" class="draggable-item table_fields"><span>&#8942;&#8942;&nbsp;&nbsp;</span>{{ $field }}</li>
                            <?php $order_id ++; ?>
                            @endforeach
                        </ul>
                    </div>
                    
                    <script>
                        $( init );
                        function init() {
                        $( ".droppable-area1, .droppable-area2" ).sortable({
                            connectWith: ".connected-sortable",
                            stack: '.connected-sortable ul',
                            update: function (event, ui) {
                                //alert('update call');
                                var new_order = new Array();
                                $(".table_fields").each(function() {
                                    new_order.push($(this).attr('id'));  
                                });
                                $.ajax({
                                    type: "POST",
                                    url: '{{ url('update_order') }}',
                                    crossDomain: true,
                                    data: { "new_order": new_order },
                                    success: function(data) {
                                    }
                                });
                                
                            }
                            }).disableSelection();
                        }
                    </script>
                </div>
            </div>
            <div class="col-md-4 padding_middle">
                <div class="label_setting">
                    <div data-step="2" data-intro="Different color combination can be selected to change the look n feel of the One Page Quick Order page. Different options are given like background color, text color etc for each of buttons available in the section." data-position="right">
                    <h3 class="sub-heading">Color Settings</h3>
                    <div class="form-group">
                        <!--Header Background Color-->
                        <div>
                        <p class="colorlabel">
                            <label class="header-color" for="header_background_color">Header Background Color</label>
                            {{-- <div class="help-tip">
                                <p>Selected color will get apply for <b>Header Background</b>.</p>
                            </div> --}}
                        </p>
                        <div class="cp2 input-group colorpicker-component"> 
                            &nbsp;&nbsp;
                            <input type="text" name="header_background_color" class="header_background_color" style="display: none;" @if(count($app_setting) > 0) value="{{$app_setting->header_background_color }}" @else value="" @endif>
                        </div>
                        </div>
                        <br/>                        
                        <!--Show Options Button Background Color -->
                        <div>
                        <p class="colorlabel">
                            <label class="header-color" for="show_options_background_color">Show Options Button Background Color</label>
                            {{-- <div class="help-tip">
                                <p>Selected color will get apply for <b>Show Options Button Background</b>.</p>
                            </div> --}}
                        </p>
                        <div class="cp2 input-group colorpicker-component"> 
                            &nbsp;&nbsp;
                            <input type="text" name="show_options_background_color" class="show_options_background_color" style="display: none;" @if(count($app_setting) > 0) value="{{$app_setting->show_options_background_color }}" @else value="" @endif>
                        </div>
                        </div>
                        <br/>
                        <!-- Add to Cart Button Background Color -->
                        <div >
                        <p class="colorlabel">
                            <label class="header-color" for="add_to_cart_background_color">Add to Cart Button Background Color</label>
                            {{-- <div class="help-tip">
                                <p>Selected color will get apply for <b>Add to Cart Button Background</b>.</p>
                            </div> --}}
                        </p>
                        <div class="cp2 input-group colorpicker-component">
                            &nbsp;&nbsp;
                            <input type="text" name="add_to_cart_background_color" class="add_to_cart_background_color" style="display: none;" @if(count($app_setting) > 0) value="{{$app_setting->add_to_cart_background_color }}" @else value="" @endif>
                        </div>
                        </div>
                        <br/>                        
                        <!--Show Options Button Background Color -->
                        <div>
                        <p class="colorlabel">
                            <label class="header-color" for="show_options_text_color">Show Options Button Text Color</label>
                            {{-- <div class="help-tip">
                                <p>Selected color will get apply for <b>Show Options Button Text</b>.</p>
                            </div> --}}
                        </p>
                        <div class="cp2 input-group colorpicker-component"> 
                            &nbsp;&nbsp;
                            <input type="text" name="show_options_text_color" class="show_options_text_color" style="display: none;" @if(count($app_setting) > 0) value="{{$app_setting->show_options_text_color }}" @else value="" @endif>
                        </div>
                        </div>
                        <br/>
                        <!-- Add to Cart Button Background Color -->
                        <div>
                        <p class="colorlabel">
                            <label class="header-color" for="add_to_cart_text_color">Add to Cart Button Text Color</label>
                            {{-- <div class="help-tip">
                                <p>Selected color will get apply for <b>Add to Cart Button Text</b>.</p>
                            </div> --}}
                        </p>
                        <div class="cp2 input-group colorpicker-component">
                            &nbsp;&nbsp;
                            <input type="text" name="add_to_cart_text_color" class="add_to_cart_text_color" style="display: none;" @if(count($app_setting) > 0) value="{{$app_setting->add_to_cart_text_color }}" @else value="" @endif>
                        </div>
                        </div>
                        <br/>
                        <!-- Sold Out Button Background Color -->
                        <div >
                        <p class="colorlabel">
                            <label class="header-color" for="sold_out_background_color">Sold Out button Background Color</label>
                            {{-- <div class="help-tip">
                                <p>Selected color will get apply for <b>Sold Out button Background</b>.</p>
                            </div> --}}
                        </p>
                        <div class="cp2 input-group colorpicker-component">
                            &nbsp;&nbsp;
                            <input type="text" name="sold_out_background_color" class="sold_out_background_color" style="display: none;" @if(count($app_setting) > 0) value="{{$app_setting->sold_out_background_color }}" @else value="" @endif>
                        </div>
                        </div>
                        <br/>
                        <!--Sold Out Button Text Color -->
                        <div >
                        <p class="colorlabel">
                            <label class="header-color" for="sold_out_text_color">Sold Out button Text Color</label>
                            {{-- <div class="help-tip">
                                <p>Selected color will get apply for <b>Sold Out button Text</b>.</p>
                            </div> --}}
                        </p>
                        <div class="cp2 input-group colorpicker-component"> 
                            &nbsp;&nbsp;
                            <input type="text" name="sold_out_text_color" class="sold_out_text_color" style="display: none;" @if(count($app_setting) > 0) value="{{$app_setting->sold_out_text_color }}" @else value="" @endif>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 padding_right" data-step="3" data-intro="This section will allow to change the labels of the action buttons which will provide idea to user about their usage and action.">
                <div class="label_setting">
                    <h3 class="sub-heading">Language Settings</h3>
                    <div class="form-group">
                        <label>Show Options Button Text *</label>
                        {{-- <div class="help-tip">
                            <p>Enter text to display on <b>Show Options Button</b>.</p>
                        </div> --}}
                        <input type="text" class="form-control" name ="show_options_text" @if(count($app_setting) > 0) value="{{$app_setting->show_options_text }}" @else value="" @endif required /> 
                    </div>
                    <div class="form-group">
                        <label>Sold out Button Text *</label>
                        {{-- <div class="help-tip">
                            <p>Enter text to display on <b>Sold out Button</b>.</p>
                        </div> --}}
                        <input type="text" class="form-control" name ="sold_out_text"  @if(count($app_setting) > 0) value="{{$app_setting->sold_out_text }}" @else value="" @endif required /> 
                    </div>
                    <div class="form-group">
                        <label>Add to Cart Button Text *</label>
                        {{-- <div class="help-tip">
                            <p>Enter text to display on <b>Add to Cart Button</b>.</p>
                        </div> --}}
                        <input type="text" class="form-control" name ="add_to_cart_text" @if(count($app_setting) > 0) value="{{$app_setting->add_to_cart_text }}" @else value="" @endif required /> 
                    </div>
                </div>
                <div class="label_setting">
                    <h3 class="sub-heading">The Quick order App Has Been Installed!</h3>
                    <p>    
                        <p>    
                            The Quick order page for your store is located at below mentioned link:</br>    
                            <b><a href="<?php
                                if (Session::has('shop')) {    
                                    echo "https://" . $store_name . "/pages/one-page-quick-order";
                                } else {    
                                    echo"#";
                                }
                                ?>" target="_blank">    
                                    <?php
                                    if (Session::has('shop')) {    
                                        echo "https://" . session('shop') . "/pages/one-page-quick-order";
                                    }
                                    ?>        
                                </a></b>    
                        </p>    
                        <p>    
                            You can share this link with your customers or link to it from your store's navigation menu <b><a class="info_css" href="{{ asset('image/navigation.png') }}">See Example</a></b>    
                        </p>    
                        <p>    
                            To edit or delete the page, visit the    
                            <b><a href="<?php
                                if (Session::has('shop')) {    
                                    echo "https://" . session('shop') . "/admin/pages";
                                }
                                ?>" target="_blank">    
                                    Pages section    
                                </a></b>     
                            in your Shopify Admin.    
                        </p>        
                        </p>  
                </div>
            </div>
        </div>
        <div class="padding_left save_settings_wrapper">
            <input type="submit" name="save_settings" class="save_settings" value="Save" data-step="5" data-intro="Once all the information are entered,please use Save button to save all the details and get into effect."/>
        </div>
    </div>
    
    <?php /*<div class="col-md-12 formcolor sticky_formcolor">
        <div class="shortcode_heading col-sm-12 ">
            <h2 class="slide_down sub-heading subleft col-md-12"><i class="fa fa-chevron-up"></i> Shortcode & Where to paste Shortcode?</h2>
            <div class="col-sm-12" id="shortcode_info">    
                <img class="close_box" src="{{ asset('image/cancel.png') }}" />      
                <br/>
                <br/>
                <ul>
                    <li>The one page quick order page for your store is located at <a href ="<?php echo $app_page; ?>" target ="_blank"><?php echo $app_page; ?></a> </li>

                    <li>You can share this link with your customers or link to it from your store's navigation menu <a class="info_css" href="{{ asset('image/navigation.png') }}" target="_blank">See Example</a>
                    </li>

                    <li>To edit or delete the page, visit the <a href ="<?php echo $pages; ?>" target ="_blank">Pages section</a> in your Shopify Admin.</li>

                    <li> If you want to display the app in any page other then above created page, 
                        then copy the shortcode below and paste it in appropriate file.</li> 
                </ul>

                <ul class="shortcode-note">
                    <li>
                        <div class="copystyle_wrapper">  
                            <textarea id="script_code" rows="1" class="form-control short-code"  readonly=""><?php echo "<div class='zestard_bulk_order_list_view' store_encrypt= " . $encrypted_store_id . "></div>"; ?>
                            </textarea>
                            <btn id="copy_script" name="copy_script" value="Copy Shortcode" class="btn btn-info copycss_button" data-clipboard-target=".script_code" style="display: block;" onclick="copy_shortcode()"><i class="fa fa-check"></i> Copy</btn>
                        </div>
                    </li>

                </ul>
            </div>
        </div> 
    </div> */ ?>
</form>

<script>    
    function copy_shortcode()
    {
        var copyText = document.getElementById("script_code");
        /* Select the text field */
        copyText.select();
        /* Copy the text inside the text field */
        document.execCommand("Copy");
        toastr.success("Shortcode copied!");
    }
    
</script>
<style>
    .connected-sortable{
        margin-top:10px; 
        border: 1px solid #000000;
        padding-left: 6px;
    }
    .connected-sortable .table_fields{
        margin: 10px;
        padding: 5px 15px;
        /*border: 1px solid #000000;*/
    }
    .connected-sortable .table_fields:hover {
        cursor: pointer;
    }
    .table_fields span {
        font-size: 25px;
        width: 10px;
        /*border-right: 1px solid #000000;*/
        
    }
    .save_settings_wrapper{
        padding-bottom: 20px;
    }
    .save_settings{
        position: relative;
        display: inline-block;
        min-height: 3.6rem;
        min-width: 3.6rem;
        margin: 0;
        padding: .7rem 1.6rem;
        background: -webkit-linear-gradient(top, #6371c7, #5563c1);
        background: linear-gradient(180deg, #6371c7, #5563c1);
        border: .1rem solid #3f4eae;
        box-shadow: inset 0 1px 0 0 #6774c8, 0 1px 0 0 rgba(22, 29, 37, 0.05), 0 0 0 0 transparent;
        color: #fff;
        fill: #fff;
        border-radius: .3rem;
        line-height: normal;
        text-align: center;
        text-decoration: none;
        -webkit-transition-property: background,border,box-shadow;
        transition-property: background,border,box-shadow;
        -webkit-transition-duration: .2s;
        transition-duration: .2s;
        -webkit-transition-timing-function: cubic-bezier(0.64, 0, 0.35, 1);
        transition-timing-function: cubic-bezier(0.64, 0, 0.35, 1);
        box-sizing: border-box;
        cursor: pointer;
        white-space: nowrap;
        text-transform: none;
        font-family: -apple-system, "BlinkMacSystemFont", "San Francisco", "Roboto", "Segoe UI", "Helvetica Neue", sans-serif;
        font-weight: normal;
        vertical-align: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        -webkit-appearance: none;
        -webkit-tap-highlight-color: transparent;
    }
    .help-tip{
        position: relative;
        display: inline-block;
        /* top: 18px; 
        right: 18px; */
        left: 5px;
        text-align: center;
        background-color: black;
        border-radius: 50%;
        width: 15px;
        height: 15px;
        font-size: 8px;
        line-height: 15px;
        cursor: pointer;
    }
    
    .help-tip:before{
        content:'\003F';
        font-weight: bold;
        color:#fff;
    }
    
    .help-tip:hover p{
        display:block;
        transform-origin: 100% 0%;
    
        -webkit-animation: fadeIn 0.3s ease-in-out;
        animation: fadeIn 0.3s ease-in-out;
    
    }
    
    .help-tip p{    /* The tooltip */
        display: none;
        text-align: left;
        background-color: #1E2021;
        padding: 20px;
        width: 300px;
        position: absolute;
        border-radius: 3px;
        box-shadow: 1px 1px 1px rgba(0, 0, 0, 0.2);
        right: auto;
        color: #FFF;
        font-size: 13px;
        line-height: 1.4;
        left: -16px;
        top: 19px;
        z-index: 9999999;
    }
    
    .help-tip p:before{ /* The pointer of the tooltip */
        position: absolute;
        content: '';
        width:0;
        height: 0;
        border:6px solid transparent;
        border-bottom-color:#1E2021;
        left: 16px;
        top: -12px;
    }
    
    .help-tip p:after{ /* Prevents the tooltip from being hidden */
        width:100%;
        height:40px;
        content:'';
        position: absolute;
        top:-40px;
        left:0;
    }

    .help-tip b{
        color: white;
    }
    
    /* CSS animation */
    
    @-webkit-keyframes fadeIn {
        0% { 
            opacity:0; 
            transform: scale(0.6);
        }
    
        100% {
            opacity:100%;
            transform: scale(1);
        }
    }
    
    @keyframes fadeIn {
        0% { opacity:0; }
        100% { opacity:100%; }
    }
    @media only screen and (max-width: 1336px) {
        .help-tip p {
            width: 200px;
        }
    }
    </style>    
@endsection