@extends('header')
@section('content')

<script type="text/javascript">
    ShopifyApp.ready(function(){
        ShopifyApp.Bar.initialize({
            buttons: {
                primary: {
                    label: 'SAVE',
                    message: 'form_submit',
                    loading: true,
                },
                secondary: [
                    {
                        label: 'HELP',
                        href: '{{ url('help') }}',
                        loading: false
                    },
                    {
                        label: 'Product List',
                        href: '{{ url('product_list') }}',
                        loading: false
                    }
                ]
            }
        });
        $(".allow_out_of_stock").on('click', function () {
            if ($('.allow_out_of_stock').prop('checked') == true)
            {
                $('.show_available_quantity').prop('checked', false);
                $('.show_available_quantity').prop('disabled', true);
            } else
            {
                $('.show_available_quantity').prop('disabled', false);
            }
        });
        if ($('.allow_out_of_stock').prop('checked') == true)
        {
            $('.show_available_quantity').prop('disabled', true);

        }
    });
    //allow_out_of_stock
</script>

<div class="container bulk-order-container">

    <div class="row">

        <div class="card">

            <div class="card-content">

                <p class="bulk-install">

                    The Bulk Order App Has Been Installed!

                </p>

                <p>

                <p>

                    The bulk order page for your store is located at

                    <b><a href="<?php
                        if (Session::has('shop')) {

                            echo "https://" . $shop_domain . "/pages/bulkorder";
                        } else {

                            echo"#";
                        }
                        ?>" target="_blank">

                            <?php
                            if (Session::has('shop')) {

                                echo "https://" . session('shop') . "/pages/bulkorder";
                            }
                            ?>    

                        </a></b>

                </p>

                <p>

                    You can share this link with your customers or link to it from your store's navigation menu <b><a class="info_css" href="{{ asset('image/add bulkorder page.png') }}">See Example</a></b>

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

                <p>

                    Your Store currency is <b>"{{ $store_detail->currency_code }}"</b> and your currency Symbol is <b>"{{ $store_detail->symbol_html }}" </b>

                </p>

                </p>

            </div>

        </div>

        <div class="card" style="display:block;">
            <div class="card-content">
                <p class="bulk-setting">
                    Bulk Order Settings
                </p>
                <form action="save" name="save settings" method="POST" data-shopify-app-submit="form_submit">
                    {{ csrf_field() }}
                    
                    <div class="test_div">
                        <div class="col-md-6">
                            <p class="bulk-setting">
                                Bulk Order Settings
                            </p>
<!--                            <div class="row">
                                <div class="col-md-7">
                                    <label for="quantity_status" class="bulk-text" >Show Available Quantity</label>
                                </div>
                                <div class="col-md-5">
                                    <select name="quantity_status" class="bulk-option">
                                        <option value="0" @if($shop_details->quantity_status == 0) selected @endif >off</option>
                                        <option value="1" @if($shop_details->quantity_status == 1) selected @endif >on</option>
                                    </select>
                                </div>
                            </div>-->
                            <div class="row">
                                <div class="col-md-7">
                                    <label for="display_product_image_status" class="bulk-text">Show Product Image</label>
                                </div>
                                <div class="col-md-5">
                                    <select name="display_product_image_status" class="bulk-option" >
                                        <option value="0" @if($shop_details->display_product_image_status == 0) selected @endif >off</option>
                                        <option value="1" @if($shop_details->display_product_image_status == 1) selected @endif >on</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-7">
                                    <label for="allow_out_of_stock_products_to_order" class="bulk-text">Allow out of stock products to order</label>
                                </div>
                                <div class="col-md-5">
                                    <input type="checkbox" name="allow_out_of_stock_products_to_order" class="allow_out_of_stock" value="1" <?php echo ($shop_details->allow_out_of_stock_products_to_order == 1) ? 'checked' : ''; ?> style="position: initial">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-7">
                                    <label for="show_available_quantity" class="bulk-text">Show Available Quantity</label>
                                </div>
                                <div class="col-md-5">
                                    <input type="checkbox" name="show_available_quantity" class="show_available_quantity" value="1" <?php echo ($shop_details->show_available_quantity == 1) ? 'checked' : ''; ?> style="position: initial">
                                </div>
                            </div>
							<div class="row">
                                <div class="col-md-7">
                                    <label for="show_available_quantity" class="bulk-text">Show Out of Stock Products</label>
                                </div>
                                <div class="col-md-5">
                                    <input type="checkbox" name="show_out_of_stock_products" class="show_out_of_stock_products" value="1" <?php echo ($shop_details->show_out_of_stock_products == 1) ? 'checked' : ''; ?> style="position: initial">									 
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="additional_css" class="bulk-text">Additional CSS</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12"> 
                                    <textarea name="additional_css" class="form-control bulk-option" rows="4" style="margin-bottom:0px;"><?php
                                        if ($shop_details->additional_css) {
                                            echo $shop_details->additional_css;
                                        }
                                        ?></textarea>
                                    <div class="note">
                                        <b>Note:</b> You can add CSS For Example: body {margin:0px;}
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <p class="bulk-setting">
                                Language Settings
                            </p>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="page_title_label" class="bulk-text">Page Title Label</label>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="page_title_label" class="form-control" placeholder="BULKORDER" value="<?php echo isset($shop_details->page_title_label) ? $shop_details->page_title_label : ''; ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="product_name_label" class="bulk-text">Product Name Label</label>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="product_name_label" class="form-control" placeholder="Search Product(by name or SKU)" value="<?php echo isset($shop_details->product_name_label) ? $shop_details->product_name_label : ''; ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="quantity_label" class="bulk-text">Quantity Name Label</label>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="quantity_label" class="form-control" placeholder="Quantity" value="<?php echo isset($shop_details->quantity_label) ? $shop_details->quantity_label : ''; ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="cost_label" class="bulk-text">Cost Name Label</label>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="cost_label" class="form-control" placeholder="Cost" value="<?php echo isset($shop_details->cost_label) ? $shop_details->cost_label : ''; ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="total_label" class="bulk-text">Total Name Label</label>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="total_label" class="form-control" placeholder="Total" value="<?php echo isset($shop_details->total_label) ? $shop_details->total_label : ''; ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="available_quantity_label" class="bulk-text">Available Quantity Label</label>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="available_quantity_label" class="form-control" placeholder="Avalilable Quantity" value="<?php echo isset($shop_details->available_quantity_label) ? $shop_details->available_quantity_label : ''; ?>">
                                </div>
                            </div>
                        </div>
                    </div>
				</form>
            </div>
        </div>
    </div>
</div>
<div class="new_install">
	<div class="modal fade" id="new_note">
		<div class="modal-dialog" style="width:80%;">          
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"><b>Note</b></h4>
				</div>
				<div class="modal-body">
					<p>Dear Customer, As this is a paid app and hundreds of customers are using it, So if you face any issue(s) on your store before uninstalling, Please contact support team (<a href="mailto:support@zestard.com">support@zestard.com</a>) or live chat at bottom right to resolve it ASAP.</p>
				</div>        
				<div class="modal-footer">			
					<div class="datepicker_validate" id="modal_div">
						<div>
							<strong>Show me this again</strong>
							<input name="modal_status" type="checkbox" checked id="dont_show_again"></input>															
						</div>      
					</div>      
				</div>      
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$("#dont_show_again").change(function(){
		var checked   = $(this).prop("checked");
		var shop_name = "{{ session('shop') }}";			
		if(!checked)
		{
			$.ajax({
				url:'update-modal-status',
				data:{shop_name:shop_name},
				async:false,					
				success:function(result)
				{
					
				}
			});				
			$('#new_note').modal('toggle');
		}
	});
	if("{{ $new_install }}")
	{		
		var new_install = "{{ $new_install }}";	
		if(new_install == "Y")
		{
			$('#new_note').modal('show');
		}
	}		
</script>
<style>
.modal-backdrop
{
	z-index:99 !important;
}
</style>
@endsection