<!-- Bootstrap core CSS -->

<link rel="stylesheet" href="https://zestardshop.com/shopifyapp/bulkorder/public/css/bootstrap.css" >
<script src="https://zestardshop.com/shopifyapp/bulkorder/public/js/money.js"></script>

<script>
//  jQuery('.page-header').html('<h2>sdsd</h2>');
//  if(typeof jQuery == 'undefined') {
//        var script = document.createElement('script');
//        script.type = "text/javascript";
//        script.src = "https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js";
//        document.getElementsByTagName('head')[0].appendChild(script);
//  }
//
//    $ = jQuery;

	var shop_name = Shopify.shop;
	var out_of_stock;
    function startloader(process) {
        if (process == 1) {
            $(".overlay").css({
                'display': 'block',
                'background-image': 'url({{ asset('image / loader1.gif') }})',
                'background-repeat': 'no-repeat',
                'background-attachment': 'fixed',
                'background-position': 'center'
            });
        } else {
            $(".overlay").css({
                'display': 'none',
                'background-image': 'none',
            });
        }
    }
	var show_out_of_stock_products;
    var page_title = "<?php echo ($shop_find['page_title_label']); ?>";
    if (page_title != '')
    {
        $('.page-header h2').html(page_title);
    }
    $(function () {
        if ((typeof Shopify) === 'undefined') {
            Shopify = {};
        }
        if (!Shopify.formatMoney) {
            var jq = document.createElement("script");
            jq.type = "text/javascript";
            jq.src = "https://zestardshop.com/shopifyapp/bulkorder/public/js/money.js";
            document.getElementsByTagName("head")[0].appendChild(jq);
        }

        var currency_symbol = $(".bulkorder").attr('mony_format');
        if(shop_name == "mailersdirect.myshopify.com"){
            currency_symbol = "$"
        }
        var product_image_status = 1;
        var available_quantity_status = 1;
        var additional_css = "";
        //for reset all data
        $.ajax({
            url: "https://zestardshop.com/shopifyapp/bulkorder/public/get-user-settings",
            data: {shop: Shopify.shop},
            asunc: false,
            success: function (result)
            {
                product_image_status = parseInt(result.display_product_image_status);
                available_quantity_status = result.show_available_quantity;
                show_out_of_stock_products = result.show_out_of_stock_products;
                additional_css = result.additional_css;
                $('.additional-css').html("<style>" + additional_css + "</style>");
            }
        });
        //for reset all data

        $("#ztpl-reset_all").on('click', function () {

            $(".ztpl-mainrow input").val('');

            $(".ztpl-total").removeAttr('original_cost');

            $(".ztpl-qtywarning").remove();

            $(".ztpl-product_quantity").attr('disabled', 'disabled');

            $(".ztpl-varient").removeAttr('value');

            $(".ztpl-varient").removeAttr('line_quantity');

            $(".ztpl-varient").removeAttr('stock');

            $(".ztpl-calculation_box").css("display", "none");

            $(".ztpl-bo-description-section").css("display", "none");

            $(".ztpl-discription-title").css("display", "none");

            run_auto_complete();
        });

        // Add to cart button code 
        $("#ztpl-addtocart").on('click', function () {
            var msg_text = "Updating cart";
            var customElement = $("<div>", {
                id: "countdown",
                css: {

                    "font-size": "20px"
                },
                text: msg_text
            });
            $.ajaxSetup({
                async: false
            });

            out_of_stock = '<?php echo $shop_find['allow_out_of_stock_products_to_order'] ?>';

            $(".ztpl-varient").each(function () {
                if ($(this).attr("stock") != 0 || parseInt(out_of_stock) == 1) {
                    if (this.value != '' && $(this).attr("line_quantity") > 0) {
                        var qt = $(this).attr("line_quantity");
                        var variant_id = this.value;
                        $.ajax({
                            "url"    :"/cart/add.js",
                            "data"   :{id:variant_id, quantity:qt},
                            "async"  :false,
                            "success":function(){
                            }
                        });
                        // setTimeout(function () {
                        // $.post('/cart/add.js', 'quantity=' + qt + '&id=' + variant_id);
                        // }, 300);
                    }
                }
            });
            startloader(1);
            //setTimeout(function () {
                document.location.href = '/cart';
            //}, 1000);
        });

        $("#ztpl-empty_cart").on('click', function (e) {
            e.preventDefault();
            startloader(1);
            $.ajax({
                type: "POST",
                url: '/cart/clear.js',
                data: '',
                dataType: 'json',
                success: function () {
                    startloader(0);
                    location.reload();
                },
                error: function (XMLHttpRequest, textStatus) {}
            });
        });

        $(".ztpl-addrow").on('click', function () {
            var product_name = $('.ztpl-search_text').attr("data-product-name");
            product_name = (product_name == '')?'Search Product(by name or SKU)':product_name;
            var new_add = '<div class="ztpl-bo-line-item  ">' +
                    '<div class="ztpl-row-wrapper ztpl-d-flex">' +
                    '<input name="varient" type="hidden" class="ztpl-varient" />' +
                    '<div class="ztpl-cus-width ztpl-bo-variant-input-section ztpl-col-sm-4 ztpl-col-lg-4 ztpl-col-md-4 ztpl-col-xs-12 ztpl-ui-widget">' +
                    '<input name="search_text" type="text" placeholder="'+product_name+'" maxlength="524288" class="ztpl-bo-variant-input ztpl-search_text" />' +
                    '</div>' +
                    '<div class="ztpl-cus-width ztpl-bo-description-section ztpl-col-md-4"><input type="text" class="ztpl-description" disabled="disabled"/></div>'+
                    '<div class="ztpl-cus-width ztpl-col-lg-2 ztpl-col-md-2 ztpl-col-sm-2 ztpl-col-xs-4 ztpl-qt_box">' + '<input type="text" class="ztpl-product_quantity" disabled="disabled" placeholder="Quantity"/>' +
                    '</div>' +
                    '<div class="ztpl-cus-width ztpl-col-lg-3 ztpl-col-md-3 ztpl-col-sm-3 ztpl-col-xs-4 ztpl-cost_box">' + '<input type="text" class="ztpl-product_cost" disabled="disabled" placeholder="Cost" />' +
                    '</div>' +
                    '<div class="ztpl-cus-width ztpl-col-lg-3 ztpl-col-md-3 ztpl-col-sm-3 ztpl-col-xs-4 ztpl-total_box">' + '<input type="text" class="ztpl-total" disabled="disabled" placeholder="Total" />' +
                    '</div>' +
                    '<a class="ztpl-bo-col-1 ztpl-removerow ztpl-bo-remove-section"><span aria-hidden="true">&times;</span></a>' +
                    '</div>' +
                    '</div>';

            $('.ztpl-mainrow:nth-child(2)').append(new_add);
            run_auto_complete();

        });

        //search main
        run_auto_complete();

        function run_auto_complete()
        {
            $(".ztpl-search_text").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        dataType: "json",
                        url: "/search?type=product&view=zestardbulkorder-json&q=" + request.term,
                        async: false,
                        success: function (data)
						{
                            response(
								$.map(data.results, function (results){
									//console.log(results.sku);
									//if(shop_name == "innova-europe-dealer-store.myshopify.com")
									var cost_price;
									if(show_out_of_stock_products == 0)
									{
										//$.map(data.results, function (results){
										var variant = results.product.variants[0];
										cost_price = results.price;
										
										if(variant.available == true)
										{
											/* console.log(results.product.variants[0]);
											console.log(results.product); */
											if (results.variant_title != 'Default')
											{
												var show_product = results.product_title + ' ' + results.variant_title;
											}
											else
											{
												var show_product = results.product_title;
											}
											return {
												value: results.sku,
												cost: results.price,
												label: show_product,
												variantid: results.variant_id,
												stock: results.product.variants[0]['inventory_quantity'],
												inventorystatus: results.product.variants[0]['inventory_management'],
												productimage: results.thumbnail_url,
                                                productsku:results.sku
											};
										}
										//})
									}
									else
									{
										//console.log('else');
										//$.map(data.results, function (results){
										if (results.variant_title != 'Default')
										{
											var show_product = results.product_title + ' ' + results.variant_title;
										}
										else
										{
											var show_product = results.product_title;
										}
										return {
											value: results.sku,
											cost: results.price,
											label: show_product,
											variantid: results.variant_id,
											stock: results.product.variants[0]['inventory_quantity'],
											inventorystatus: results.product.variants[0]['inventory_management'],
											productimage: results.thumbnail_url,
                                            productsku:results.sku
										};
										//})
									}
								})
							);
                        }
                    });
                },
                open: function () {
                    $('.ui-autocomplete').css('width', $(this).outerWidth() + 'px');
					if(shop_name == "soc-nation.myshopify.com" || shop_name == "wireless-xplosion-ltd.myshopify.com")
					{
						$('.ui-autocomplete').css('z-index', '999999');
						$('.ui-autocomplete').css('display', 'block !important');
					}
					if(shop_name == "segurico.myshopify.com")
					{
						$('.ui-autocomplete').css('height', '500px');
					}
					//if(shop_name == "ottoandspike.myshopify.com")
					{
						$('.ui-autocomplete').css('width', '100% !important');
					}
                },
                messages: {
                    noResults: '',
                    results: function () {}
                },
                minLength: 2,

                select: function (event, ui) {
                    var variantId = ui.item.variantid;
                    var available_stock_qty = 0;
                    var description_box = '<input type="text" class="ztpl-description" disabled="disabled" value="'+ ui.item.label +'" />';
                    //debugger;
                    //console.log($(this).parent().parent().parent().parent());
                    //$(this).parent().next().css("display","block");
                    //$('.ztpl-discription-title').css("display","block");
                    //$(this).parent().parent().parent().parent().find('.ztpl-bo-description-section').css("display","block");
                    $(this).parent().next().empty();
                    $(this).parent().next().append(description_box);
                    $.ajax({
                        url: '{{url("get_variant_quantity_by_id")}}',
                        type: "POST",
                        async: false, // Please keep it false
                        data: {variantId: variantId, shop_name:shop_name},
                        success: function (d)
                        {
                            available_stock_qty = d;
                        }
                    });
                    //alert(available_stock_qty);
                    var shopify_inventory = ui.item.inventorystatus;
                    if (shopify_inventory == "shopify")
                    {
                        //alert('test1');
                        //console.log($(this).parent().parent().find('.ztpl-varient'));
                        $(this).parent().parent().find('.ztpl-varient').attr("inventory_status", shopify_inventory);
                    } else {
                        //alert('test2');
                        $(this).parent().parent().find('.ztpl-varient').attr("inventory_status", "NA");
                    }
                    var unformated_cost = Shopify.formatMoney(ui.item.cost);
                    //console.log(unformated_cost);
                    //console.log($(this).parent().next().next().next().next().find('.ztpl-total'));
                    $(this).parent().next().next().next().next().find('.ztpl-total').attr("original_cost", ui.item.cost);

                    //if product is out of stock set cost as a 0 - zero
                    out_of_stock = '<?php echo $shop_find['allow_out_of_stock_products_to_order'] ?>';

                    if (available_stock_qty == 0 && out_of_stock == 0)
                    {
                        //alert('test');
                        unformated_cost = Shopify.formatMoney(0);
                        $(this).parent().next().next().next().find('.ztpl-total').attr("original_cost", 0);
                    }
                    //alert('test');
                    var formated_cost = unformated_cost.replace("$", currency_symbol);
                    //console.log($(this).parent().parent().find('.ztpl-varient'));
                    $(this).parent().parent().find('.ztpl-varient').val(ui.item.variantid);
                    $(this).parent().parent().find('.ztpl-varient').attr("line_quantity", "1");
                    $(this).parent().parent().find('.ztpl-varient').attr("stock", available_stock_qty);
                    $(this).parent().parent().find('.ztpl-varient').attr("cost", ui.item.cost);
                    //debugger;
                    if(shop_name !== "watchbatteries.myshopify.com")
					{
                        //alert(formated_cost);
                        //console.log($(this).parent().next().next().next().find('.ztpl-product_cost'));
						$(this).parent().next().next().next().find('.ztpl-product_cost').val(formated_cost);
					}
					else
					{
                        //console.log($(this).parent().next().next());    
						$(this).parent().next().next().find('.ztpl-product_cost').val(formated_cost.replace("Rs.","").replace("$",""));
					}
                    
                    $(this).parent().next().next().find('.ztpl-product_quantity').removeAttr('disabled');
                    $(this).parent().next().next().find('.ztpl-product_quantity').attr("max", available_stock_qty);

                    //console.log($(this).parent().next().next().find('.ztpl-product_cost'));
                    //console.log(formated_cost);
                    //for check the inventory option					
                    if (shopify_inventory == "shopify")
                    {
                        if (available_stock_qty == 0)
                        {
                            //console.log($(this).parent().next());
                            var qtypath = $(this).parent().next().find('.ztpl-product_quantity');
                            qtypath.val(0);
                            qtypath.attr('disabled', 'disabled');
                            var quantity_display = $(this).parent().next().find('.ztpl-qtywarning');
                            if ($(quantity_display).length <= 0)
                            {
                                $("<strong class='ztpl-qtywarning' style='color:red;display:none';>OUT OF STOCK:" + available_stock_qty + "</strong>").insertAfter(qtypath);
                            } else {
                                $(quantity_display).text('OUT OF STOCK');
                                $(quantity_display).css('color', 'red');
                            }
                            if (parseInt(out_of_stock) == 1)
                            {
                                $(this).parent().next().find('.ztpl-product_quantity').removeAttr('disabled');
                                $(this).parent().next().find('.ztpl-product_quantity').val('1');
                                $("#ztpl-addtocart").prop("disabled", false);
                            }

                        }
						else
						{
                            
                            var quantityypath = $(this).parent().next().next().find('.ztpl-product_quantity');
                            quantityypath.val(1);
                            //console.log($(this).parent().next());
                            var quantity_display = $(this).parent().next().next().find('.ztpl-qtywarning');
                            if(available_quantity_status == 1)
                            {
                                if ($(quantity_display).length <= 0)
                                {
                                    $("<strong class='ztpl-qtywarning' style='color:green;display:<?php echo ($shop_find['show_available_quantity'] == 1) ? 'block' : 'none'; ?>'><?php echo ($shop_find['available_quantity_label'] != '') ? $shop_find['available_quantity_label'] : 'Available Quantity'; ?>:" + available_stock_qty + "</strong>").insertAfter(quantityypath);
                                }
								else
								{
                                    $(quantity_display).text('<?php echo ($shop_find['available_quantity_label'] != '') ? $shop_find['available_quantity_label'] : 'Available Quantity'; ?>:' + available_stock_qty);
                                    $(quantity_display).css('color', 'green');
                                }
                            }
                        }
						if(shop_name != "watchbatteries.myshopify.com")
						{
                            //alert('2');
                            //console.log($(this).parent().next().next().next().next().find('.ztpl-total'));
							$(this).parent().next().next().next().next().find('.ztpl-total').val(formated_cost);
						}
						else
						{
                            //console.log($(this).parent().next().next().next());
							$(this).parent().next().next().next().next().find('.ztpl-total').val(formated_cost.replace("$",""));
						}
                    }
					else
					{
                        //console.log($(this).parent().next());
                        var quantityypath = $(this).parent().next().next().find('.ztpl-product_quantity');

                        quantityypath.val(1);

                        var quantity_display = $(this).parent().next().next().find('.ztpl-qtywarning');

						if(shop_name != "watchbatteries.myshopify.com")
						{
							$(this).parent().next().next().next().next().find('.ztpl-total').val(formated_cost);
						}
						else
						{
							$(this).parent().next().next().next().next().find('.ztpl-total').val(formated_cost.replace("$",""));
						}
                        $(this).parent().next().next().next().next().find('.ztpl-total').attr("original_cost", ui.item.cost);

                    }

					//for the kayup of the quantity

                    $('.ztpl-product_quantity').on('keyup', function () {
                        //console.log('test');
                        out_of_stock = '<?php echo $shop_find['allow_out_of_stock_products_to_order'] ?>';
                        var qty = $(this).val();
                        
                        var stock = $(this).parent().parent().find('.ztpl-varient').attr('stock');
                        
                        var cost = $(this).parent().parent().find('.ztpl-varient').attr('cost');

                        var inventory = $(this).parent().parent().find('.ztpl-varient').attr('inventory_status');

                        //alert(inventory);

                        //for check the inventory option

                        if (inventory == "shopify")

                        {
                            console.log('test 1');
                            if (qty > parseInt(stock) && (parseInt(out_of_stock) == 0)) {
                                //console.log('test 2');
                                if (qty >= 0 && parseInt(stock) != 0) {
                                    //console.log('test 3');
                                    alert('<?php echo ($shop_find['available_quantity_label'] != '') ? $shop_find['available_quantity_label'] : 'Available Quantity'; ?> :' + stock);
                                    qty = stock;

                                    var total = (stock * cost);

                                    var unformated_total = Shopify.formatMoney(total);

                                    var formated_total = unformated_total.replace("$", currency_symbol);

                                    if(shop_name !== "watchbatteries.myshopify.com")
									{
                                        console.log('test 4');
										$(this).parent().next().next().find('.ztpl-total').val(formated_total);
									}
									else
									{
                                        //console.log('test 5');
                                        //console.log($(this).parent().next().next().find('.ztpl-total'));
										$(this).parent().next().next().find('.ztpl-total').val(formated_total.replace("Rs.","").replace("$",""));
									}
                                    console.log('test 6');
                                    //console.log($(this).parent().next().next().find('.ztpl-total'));
                                    $(this).parent().next().next().find('.ztpl-total').attr("original_cost", total);
                                    //console.log($(this).parent().parent().find('.ztpl-varient').attr("line_quantity"));
                                    $(this).parent().parent().find('.ztpl-varient').attr("line_quantity", qty);
                                    //console.log($(this).parent());
                                    $(this).parent().find('.ztpl-product_quantity').attr("max", stock);
                                    
                                    $(this).val(stock);

                                } else {
                                    //console.log('test 7');
                                    //alert("You can't put less than 0 qty");
                                    //console.log($(this).parent().next().next());
                                    $(this).parent().next().next().next().find('.ztpl-total').val(0);

                                }

                            } else {//console.log('test 8');
                                if (qty > 0) {
                                    //console.log('test 9');
                                    var total = (qty * cost);

                                    //console.log(cost);

                                    var unformated_total = Shopify.formatMoney(total);

                                    var formated_total = unformated_total.replace("$", currency_symbol);

                                    if(shop_name !== "watchbatteries.myshopify.com")
									{
                                        //console.log('test 10');
										$(this).parent().next().next().find('.ztpl-total').val(formated_total);
									}
									else
									{
                                        //console.log('test 11');
                                        //console.log($(this).parent().next().next());
										$(this).parent().next().next().next().find('.ztpl-total').val(formated_total.replace("Rs.","").replace("$",""));
									}
                                    //console.log($(this).parent().next().next());
                                    $(this).parent().next().next().next().find('.ztpl-total').attr("original_cost", total);

                                    $(this).parent().parent().find('.ztpl-varient').attr("line_quantity", qty);

                                    $(this).parent().next().next().find('.ztpl-product_quantity').attr("max", stock);

                                    $(this).val(qty);

                                } else {

                                    //alert("You can't put less than 0 qty");
                                    //console.log($(this).parent().next().next().next());
                                    $(this).parent().next().next().next().find('.ztpl-total').val(0);

                                }

                            }

                        } else {

                            if (qty > 0) {

                                var total = (qty * cost);

                                var unformated_total = Shopify.formatMoney(total);

                                var formated_total = unformated_total.replace("$", currency_symbol);

                                if(shop_name !== "watchbatteries.myshopify.com")
								{
									$(this).parent().next().next().find('.ztpl-total').val(formated_total);
								}
								else
								{
                                    //console.log($(this).parent().next().next());
									$(this).parent().next().next().next().find('.ztpl-total').val(formated_total.replace("Rs.","").replace("$",""));
								}
                               // debugger;
                                //console.log($(this).parent().next().next().next().next());
                                $(this).parents('.ztpl-row-wrapper').find('.ztpl-total').attr("original_cost", total);

                                $(this).parent().parent().find('.ztpl-varient').attr("line_quantity", qty);

                                $(this).parent().next().next().find('.ztpl-product_quantity').attr("max", stock);

                                $(this).val(qty);

                            } else {

                                //alert("You can't put less than 0 qty");

                                $(this).parent().next().next().next().find('.ztpl-total').val(0);

                            }

                        }
                        calculate_total();
                    });
                    calculate_total();
                }
            });




            /////

            $(".ztpl-removerow").on('click', function () {
                //console.log('test');
                $(this).parent('div').parent('div').remove();
                calculate_total();

            });

            $('.ztpl-search_text').each(function () {
                $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
                    //for check the inventory option			
                    if (item.inventorystatus == "shopify")
                    {
                        if(item.stock <= 0)
						{
							out_of_stock = <?php echo $shop_find['allow_out_of_stock_products_to_order'] ?>;
							//console.log('if');
                            if (product_image_status == 1)
                            {
								if (out_of_stock == 0)
								{
									img = "<a href='javascript:void(0)' class='btn-disabled' disabled='disabled' style='pointer-events: none; opacity: .4;cursor: default !important;'><img src='" + item.productimage + "' height='32' width='32'/><span class='pn_number'>PN:"+ item.productsku +"</span><br><span>" + item.label + "</span>" + item.label + "&nbsp;&nbsp;&nbsp;&nbsp;(OUT OF STOCK)</a>";
								}
								else
								{
									img = "<a href='javascript:void(0)' class='' style=''><img src='" + item.productimage + "' height='32' width='32'/><span class='pn_number'>PN:"+ item.productsku +"</span><br><span>" + item.label + "</span>" + item.label + "&nbsp;&nbsp;&nbsp;&nbsp;(OUT OF STOCK)</a>";
								}
                            }
							else
                            {
								if (out_of_stock == 0)
								{
									img = "<a href='javascript:void(0)' class='btn-disabled' disabled='disabled' style='pointer-events: none; opacity: .4;cursor: default !important;'><span class='pn_number'>PN:"+ item.productsku +"</span><br>" + item.label + "&nbsp;&nbsp;&nbsp;&nbsp;(OUT OF STOCK)</a>";
								}
								else
								{
									img = "<a href='javascript:void(0)' class='' style=''><span class='pn_number'>PN:"+ item.productsku +"</span><br>" + item.label + "&nbsp;&nbsp;&nbsp;&nbsp;(OUT OF STOCK)</a>";
								}
                            }
							out_of_stock = '<?php echo $shop_find['allow_out_of_stock_products_to_order'] ?>';
							if(parseInt(out_of_stock) == 1)
							{
								return $("<li class=''>").append(img).appendTo(ul);
							}
							else
							{
								return $("<li class='ztpl-ui-state-disabled ui-state-disabled' disabled>").append(img).appendTo(ul);
							}
                            //return $('<li class="ztpl-ui-state-disabled ui-state-disabled">' + item.label + '&nbsp;&nbsp;&nbsp;&nbsp;(OUT OF STOCK) </li>').appendTo(ul);
                        }
						/* Below else if condition is included by Girish as item.stock can be undefined */
						/* else if(typeof item.stock == "undefined")
                        {												
							out_of_stock = <?php echo $shop_find['allow_out_of_stock_products_to_order'] ?>;
                            if (product_image_status == 1)
                            {								
								if (out_of_stock == 0)
								{
									img = "<a href='javascript:void(0)' class='btn-disabled' disabled='disabled' style='pointer-events: none; opacity: .4;cursor: default !important;'><img src='" + item.productimage + "' height='32' width='32'/><span>" + item.label + "</span>" + item.label + "&nbsp;&nbsp;&nbsp;&nbsp;(OUT OF STOCK)</a>";
								}
								else
								{									
									img = "<a href='javascript:void(0)' class='' disabled='disabled' style='pointer-events: none; opacity: .4;cursor: default !important;'><img src='" + item.productimage + "' height='32' width='32'/><span>" + item.label + "</span>" + item.label + "&nbsp;&nbsp;&nbsp;&nbsp;(OUT OF STOCK)</a>";
								}
                            } 
							else
                            {
								if (out_of_stock == 0)
								{
									img = "<a href='javascript:void(0)' class='btn-disabled' disabled='disabled' style='pointer-events: none; opacity: .4;cursor: default !important;'>" + item.label + "&nbsp;&nbsp;&nbsp;&nbsp;(OUT OF STOCK)</a>";
								}
								else
								{									
									img = "<a href='javascript:void(0)' class='' disabled='disabled' style='pointer-events: none; opacity: .4;cursor: default !important;'>" + item.label + "&nbsp;&nbsp;&nbsp;&nbsp;(OUT OF STOCK)</a>";
								}
                            }
							out_of_stock = '<?php echo $shop_find['allow_out_of_stock_products_to_order'] ?>';							
							if(parseInt(out_of_stock) == 1)
							{
								return $("<li class='ztpl-ui-state-disabled ui-state-disabled' disabled>").append(img).appendTo(ul);
							}
							else
							{
								return $("<li class='ztpl-ui-state-disabled ui-state-disabled' disabled>").append(img).appendTo(ul);
							}
						} */
						else
                        {
                            if (product_image_status == 1)
                            {
                                img = "<a><img src='" + item.productimage + "' height='32' width='32'/><span class='pn_number'>PN:"+ item.productsku +"</span><br><span>" + item.label + "</span></a>";

                            } else
                            {
                                img = "<a><span class='pn_number'>PN:"+ item.productsku +"</span><br>" + item.label + "</a>";
                            }
                            return $("<li>").append(img).appendTo(ul);
                        }
                    } else {

                        if (product_image_status == 1)
                        {
                            img = "<a><img src='" + item.productimage + "' height='32' width='32'/><span class='pn_number'>PN:"+ item.productsku +"</span><br><span>" + item.label + "</span></a>";

                        } else
                        {
                            img = "<a><span class='pn_number'>PN:"+ item.productsku +"</span><br>" + item.label + "</a>";
                        }
                        return $("<li>").append(img).appendTo(ul);
                    }

                };

            });

        }

        // get grant total & quantity
        function calculate_total()
        {

            $(".ztpl-calculation_box").css("display", "grid");

            var grand_total = 0;
            var total_qty = 0;

            // Calculate each row total

            $(".ztpl-row-wrapper").each(function () {
                //debugger;
                //console.log($(this).children().find('.ztpl-total'));
                var qty = parseInt($(this).children().find('.ztpl-product_quantity').val());
                var total = parseInt($(this).children().find('.ztpl-total').attr('original_cost'));

                //console.log(qty);
                if (qty > 0) {
                    total_qty = total_qty + qty;
                    
                    grand_total = grand_total + total;
                    
                }
            });

            $("#ztpl-total_itams").text(total_qty); // set a total quantity
            var unformated_total = Shopify.formatMoney(grand_total);
            var formated_total = unformated_total.replace("$", currency_symbol);
            //alert(formated_total);
            $("#ztpl-grand_total").text((grand_total == 0) ? 0 : formated_total); // set a grand total
            // enabled & disabled Add to cart button
            out_of_stock = '<?php echo $shop_find['allow_out_of_stock_products_to_order'] ?>';
//            if (out_of_stock == 1)
//            {
            //    (grand_total == 0) ? $("#ztpl-addtocart").prop("disabled", true) : $("#ztpl-addtocart").prop("disabled", false);
//
//            }
            if (grand_total == 0 && out_of_stock == 0)
            {
                $("#ztpl-addtocart").prop("disabled", true);
            } else
            {
                $("#ztpl-addtocart").prop("disabled", false)
            }

        }
//        out_of_stock = '<?php echo $shop_find['allow_out_of_stock_products_to_order'] ?>';
//        if(out_of_stock == 1)
//        {
//            

    });

</script>
<div class="overlay"></div>
<div class="ztpl-bulk-order-wrapper">

    <div class="ztpl-bo-app">

        <div class="ztpl-row main-label ztpl-d-flex">

            <div  class="ztpl-cus-width ztpl-col-sm-4 ztpl-col-lg-4 ztpl-col-md-4 ztpl-col-xs-12  ztpl-search-label"><?php echo ($shop_find['product_name_label'] != '') ? $shop_find['product_name_label'] : 'Search Product(by name or SKU)' ?></div>

            <div class="ztpl-cus-width ztpl-col-md-4 ztpl-child-label ztpl-discription-title">Description</div>

            <div class="ztpl-cus-width ztpl-col-lg-2 ztpl-col-md-2 ztpl-col-sm-2 ztpl-col-xs-4  ztpl-child-label"><?php echo ($shop_find['quantity_label'] != '') ? $shop_find['quantity_label'] : 'Quantity' ?></div>

            <div class="ztpl-cus-width ztpl-col-lg-3 ztpl-col-md-3 ztpl-col-sm-3 ztpl-col-xs-4 ztpl-child-label"><?php echo ($shop_find['cost_label'] != '') ? $shop_find['cost_label'] : 'Cost' ?></div>

            <div class="ztpl-cus-width ztpl-col-lg-3 ztpl-col-md-3 ztpl-col-sm-3 ztpl-col-xs-4 ztpl-child-label"><?php echo ($shop_find['total_label'] != '') ? $shop_find['total_label'] : 'Total' ?></div>

        </div>

        <div class="ztpl-row ztpl-mainrow">

            <?php
            for ($i = 0; $i < 5; $i++) {
                ?>
                <div class="ztpl-bo-line-item">

                    <div class="ztpl-row-wrapper ztpl-d-flex"><input name="varient" type="hidden" class="ztpl-varient" />

                        <div class="ztpl-cus-width ztpl-bo-variant-input-section ztpl-col-sm-4 ztpl-col-lg-4 ztpl-col-md-4 ztpl-col-xs-12 ztpl-ui-widget"><input name="search_text" type="text" data-product-name="<?php echo ($shop_find['product_name_label'] != '') ? $shop_find['product_name_label'] : 'Search Product(by name or SKU)' ?>" placeholder="<?php echo ($shop_find['product_name_label'] != '') ? $shop_find['product_name_label'] : 'Search Product(by name or SKU)' ?>
                                                                                                                                                  "maxlength="524288" class="ztpl-bo-variant-input ztpl-search_text" /></div>
                        <div class="ztpl-cus-width ztpl-bo-description-section ztpl-col-md-4"><input type="text" class="ztpl-description" disabled="disabled"/></div>

                        <div class="ztpl-cus-width ztpl-col-lg-2 ztpl-col-md-2 ztpl-col-sm-2 ztpl-col-xs-4 ztpl-qt_box"><input type="text" class="ztpl-product_quantity" min="1" disabled="disabled" placeholder="<?php echo ($shop_find['quantity_label'] != '') ? $shop_find['quantity_label'] : 'Quantity' ?>" /></div>

                        <div class="ztpl-cus-width ztpl-col-lg-3 ztpl-col-md-3 ztpl-col-sm-3 ztpl-col-xs-4 ztpl-cost_box"><input type="text" class="ztpl-product_cost ztpl-money" disabled="disabled" placeholder="<?php echo ($shop_find['cost_label'] != '') ? $shop_find['cost_label'] : 'Cost' ?>" /></div>

                        <div class="ztpl-cus-width ztpl-col-lg-3 ztpl-col-md-3 ztpl-col-sm-3 ztpl-col-xs-4 ztpl-total_box"><input type="text" class="ztpl-total money" disabled="disabled" placeholder="<?php echo ($shop_find['total_label'] != '') ? $shop_find['total_label'] : 'Total' ?>" /></div>

                </div>

            </div>
            <?
            }
            ?>


        </div>

        <!--Calculation row -->
        <div class="row">
            <div class="col-md-5">
                <a class="ztpl-addrow"><span class="ztpl-glyphicon ztpl-glyphicon-plus ztpl-plus-icon"></span><b id="add_product_label">Add Product</b></a>
            </div>
            <div class="ztpl-bo-line-item ztpl-calculation_box">
                <div class="ztpl-row-wrapper col-md-7">
                    <div class="ztpl-qt_box qtbox_media" style="float: left;margin-left: 10px"><b>Quantity:</b> <span id="ztpl-total_itams"></span></div>

                    <div class="ztpl-total_box ztpl-totalbox_media" style="float: right;margin-right: 27%"><b>Total:</b> <span id="ztpl-grand_total" class="ztpl-money"></span></div>
                </div>
            </div>
        </div>
        <!--Calculation row end-->
        <div class="ztpl-bo-add-line-item ztpl-row-wrapper">
            <div>
                <div class="ztpl-bo-cart-controls">
                    <div class="ztpl-bo-cart-link ztpl-col-sm-6 ztpl-col-xs-12">
						<button class="ztpl-btn ztpl-btn-primary" id="ztpl_go_to_cart" onclick="document.location.href = '/cart'">Go To Cart</button>
						<button class="ztpl-btn ztpl-bo-clear-cart ztpl-btn-primary" id="ztpl-empty_cart">Clear Cart</button>
					</div>
                    <div class="ztpl-col-sm-6 ztpl-col-xs-12 ztpl-bo-cart-link">
						<button class="ztpl-btn ztpl-bo-update-cart ztpl-btn-primary" id="ztpl-addtocart" disabled="disabled">Add To Cart</button>
						<button type="reset" value="Reset" class="ztpl-btn ztpl-btn-primary" id="ztpl-reset_all">Reset Form</button>
					</div>
                </div>
            </div>
        </div>
    </div>
    <div class="additional-css"></div>
	<script>
		if(shop_name == "segurico.myshopify.com")
		{
			//console.log('here');
			$("#ztpl_go_to_cart").prop('value', "Ver carrito");
			$("#ztpl-empty_cart").prop('value', "Vaciar carrito");
			$("#ztpl-addtocart").prop('value', "Vaciar formulario");
			$("#ztpl-reset_all").prop('value', "Agregar al carrito");
			$("#add_product_label").html("Agregar producto");
		}
	</script>
    <style>
		.ztpl-total
		{
			display: block !important;
		}
        .ztpl-bo-variant-input-section
		{
            display: inline-block;
        }
        .ui-widget.ui-widget-content {
            max-width: 475px;
            max-height: 300px;
            overflow: auto;
            cursor: pointer;
            padding-left: 0;
            z-index: 120;
        }
        .ui-widget.ui-widget-content li.ui-menu-item{
            display: block;
        }
        .ui-widget.ui-widget-content li.ui-menu-item a{
            padding: 10px;
            font-size: 14px;
            vertical-align: middle;
            display: block;
            border: none;
            width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .ui-widget.ui-widget-content li.ui-menu-item img{
            margin-right: 5px;
            vertical-align: middle;
        }
        .ui-widget.ui-widget-content li.ui-menu-item li:hover
        {
            background-color: #32527c;
        }
		.ztpl-bo-quantity-input-section {
            display: inline-block;
        }
        .ztpl-bo-remove-section {
            display: inline-block;
            height: 50px;
            line-height: 50px;
            text-align: center;
            width:auto;
        }
		.ztpl-bo-svg-container {
            padding: 5px 0px;
        }
		.ztpl-bo-remove-section svg {
            height: 25px;
            width:auto;
        }
		.ztpl-bo-variant-input {
            width: 100%;
        }



        .ztpl-bo-line-item {
            padding-bottom: 10px;
            /*padding-right: 15px;
            padding-left: 15px;*/

        }



        .ztpl-bo-line-item input {

            height: 50px !important;

            width: 100% !important;

            padding-top: 0px !important;

            padding-bottom: 0px !important;

            padding-left: 10px !important;
    margin-bottom: 0px;
        }



        .ztpl-bo-img {

            margin-left: 0px !important;

            max-height: 50px !important;

            width: auto !important;

        }



        .ztpl-bo-img-container {

            max-height: 50px !important;

        }



        .ztpl-bo-line-item-details {

            font-size: 1.05rem;

        }



        .ztpl-angucomplete-searching {

            padding: 0px 5px;

            font-size: 1.05rem;

        }



        .ztpl-angucomplete-holder{

            position: relative;

        }



        .ztpl-angucomplete-dropdown {

            margin-top: 0px;

            padding: 20px 10px;

            background-color: #FCFCFC;

            border: 1px solid #DDDDDD;

            max-height: 360px;

            overflow: scroll;

            position: absolute;

            z-index: 999;

            width: 100%;

        }



        .ztpl-angucomplete-row {

            min-height: 50px;

            margin-bottom: 20px;

            cursor: pointer;

        }



        .ztpl-angucomplete-image-holder {

            width: calc(16.66666667% - 20px);

            float: left;

            padding: 0px 5px;

            margin: 0px !important;

        }



        .ztpl-angucomplete-image {

            max-height: 50px !important;

            width: auto !important;

        }



        .ztpl-angucomplete-title {

            width: calc(83.33333333% - 20px);

            float: left;

            font-size: 1.05rem;

            padding: 0px 5px;

        }



        .ztpl-bo-add-line-item {

            position: relative;

            margin-top: 10px;

            padding-left: 25px;

        }



        .ztpl-bo-cart-controls {

            margin-top: 20px;

            padding-left: 15px;

            display: inline-block;

            width: 100%;

        }







        .ztpl-bo-clear-cart,

        .ztpl-bo-update-cart {

            float:right;

            text-align: right;

            margin-bottom: 10px;

            margin-right: 10px;



        }



        .ztpl-bo-update-cart-btn {

            float: right;

            max-width: 80%;

            position: relative;

            bottom: 5px;

        }



        .ztpl-bo-calculate{

            margin-left: 10px;

        }



        .ztpl-bo-line-item-already-exists-msg {

            color: red;

        }



        .ztpl-bo-row {

            padding: 0px 10px;

            min-height: 50px;

        }



        .ztpl-bo-col-1 {

            width: calc(8.33333333% - 20px);

            float: left;

        }



        .ztpl-bo-col-2 {

            width: calc(16.6666667% - 20px);

            float: left;

        }



        .ztpl-bo-col-3 {

            width: calc(25% - 20px);

            float: left;

        }



        .ztpl-bo-col-4 {

            width: calc(33.33333333% - 20px);

            float: left;

        }



        .ztpl-bo-col-5 {

            width: calc(41.66666667% - 20px);

            float: left;

        }



        .ztpl-bo-col-6 {

            width: calc(50% - 20px);

            float: left;

        }



        .ztpl-bo-col-7 {

            width: calc(58.33333333% - 20px);

            float: left;

        }



        .ztpl-bo-col-8 {

            width: calc(66.66666667% - 20px);

            float: left;

        }



        .ztpl-bo-col-9 {

            width: calc(75% - 20px);

            float: left;

        }



        .ztpl-bo-col-10 {

            width: calc(83.33333333% - 20px);

            float: left;

        }



        .ztpl-bo-col-11 {

            width: calc(91.66666667% - 20px);

            float: left;

        }



        .ztpl-bo-col-12 {

            width: calc(100% - 20px);

            float: left;

        }



        .ztpl-bo-col-1, .ztpl-bo-col-2, .ztpl-bo-col-3, .ztpl-bo-col-4, .ztpl-bo-col-5, .ztpl-bo-col-6,

        .ztpl-bo-col-7, .ztpl-bo-col-8, .ztpl-bo-col-9, .ztpl-bo-col-10, .ztpl-bo-col-11, .ztpl-bo-col-12 {

            margin: 0px 10px;

        }

        .ztpl-bo-app ztpl-img.bo-remove-section.svg {

            width: auto !important;

            height: auto;

            cursor: pointer;

        }

        .ztpl-main-label{

            font-size: medium;

            padding-bottom: 20px;

            padding-top: 20px;

            color: rebeccapurple;

            padding-right: 15px;

            padding-left: 15px;

        }

        input:focus, select:focus, textarea:focus {

            font-size: inherit;

        }

        .ztpl-qtywarning{

            position: relative;

            font-size: 10px;

        }

        .ztpl-row-wrapper{

            width: 100%;

            display: inline-block;

        }

        a.ztpl-addrow {

            text-decoration: none;

            cursor: pointer;

        }

        .ztpl-calculation_box{

            display: none;

        }



        .ztpl-search-label, .ztpl-child-label {

            color: #000;

            font-size: 20px;

        }

        @media screen and (max-width: 768px) {
            .ui-widget.ui-widget-content {
                max-width: 290px;
            }
        }
        @media screen and (max-width: 667px) {
            .ui-widget.ui-widget-content {
                max-width: 645px;
            }
        }
        @media screen and (max-width: 568px) {
            .ui-widget.ui-widget-content {
                max-width: 550px;
            }
        }
        @media screen and (max-width: 547px) {

            .ztpl-bo-add-line-item.ztpl-row-wrapper {

                width: 100%;

                text-align: left;

                padding: 0;

                margin: 0;

            }



            .ztpl-bo-cart-controls {

                margin: 0;

                padding: 10px 0;

                display: inline-block;

            }
            .ui-widget.ui-widget-content {
                max-width: 528px;
            }
        }



        @media screen and (max-width: 480px) {

            .ztpl-bo-cart-link button {

                float: left;

            }

            .ztpl-calculate.ztpl-calculate-btn {

                float: left;

                margin-left: 15px;

            }

            button#ztpl-addtocart {

                margin: 0;

            }



            .ztpl-qt_box, .ztpl-cost_box, .ztpl-total_box, .ztpl-ui-widget {

                margin: 0;

                padding: 0;

            }
            .ui-widget.ui-widget-content {
                max-width: 470px;
            }
            .ztpl-bo-line-item {
                padding-left: 10px;
                padding-right: 10px;
            }
        }


        @media screen and (max-width: 375px) {
            .ui-widget.ui-widget-content {
                max-width: 370px;
            }
        }
        @media screen and (max-width: 767px) and (min-width: 320px) {

            .ztpl-bo-variant-input-section {

                display: inline-block;

                margin-bottom: 10px;

            }



            .ztpl-row-wrapper {

                width: 100%;

                display: inline-block;

                padding-bottom: 10px;

                margin-bottom: 10px;

            }



            .ztpl-row-wrapper input[disabled] {

                background-color: rgba(210, 20, 20, 0.17);

            }



            .ztpl-row-wrapper input.ztpl-product_quantity {

                background-color: rgba(17, 236, 85, 0.16);

            }



            .ztpl-search-label {

                text-align: center;

                color: #000;

                font-size: 20px;

            }



            .ztpl-child-label {

                text-align: center;

                color: #131313;

                font-size: 17px;

                padding-top: 10px;

            }

            .ztpl-display-none {

                display: none;

            }

            .ztpl-qtbox_media{

                padding-left: 0px;

            }

            .ztpl-totalbox_media{

                padding-left: 20px;

            }

            .ztpl-ui-autocomplete {

                width: 228px;

                white-space: nowrap;

                overflow: hidden;

                text-overflow: ellipsis;

            }

        }

        @media screen and (max-width: 991px) and (min-width: 320px) {

            .ztpl-main-label {

                display:none;

            }

        }

        @media screen and (max-width: 320px){
            .ui-widget.ui-widget-content {
                max-width: 310px;
            }
        }
        a.ztpl-bo-col-1.ztpl-removerow.ztpl-bo-remove-section {

            margin: 0;

            cursor: pointer;

            font-size: 30px;

            text-decoration: none;

            color: #000;
            position: absolute;
            right: -45px;

        }

        a.ztpl-addrow b {

            font-weight: 600px;

            color: #000;

            vertical-align: middle;

        }

        .ztpl-calculate-btn{

            float: right;

            margin: 0;

            margin-right: 20px;

        }

        span.ztpl-glyphicon.ztpl-glyphicon-plus.ztpl-plus-icon {

            color: #000;

            font-size: 11px;

            margin-right: 5px;

        }

        .ztpl-bulk-order-wrapper .ztpl-container {

            padding-top: 10px;

        }



        /*for the autocomplate*/

        .ztpl-ui-autocomplete {

            position: absolute;

            top: 100%;

            left: 0;

            z-index: 1000;

            float: left;

            display: none;

            min-width: 160px;

            _width: 160px;

            padding: 4px 0;

            margin: 2px 0 0 0;

            list-style: none;

            background-color: #ffffff;

            border-color: #ccc;

            border-color: rgba(0, 0, 0, 0.2);

            border-style: solid;

            border-width: 1px;

            -webkit-border-radius: 5px;

            -moz-border-radius: 5px;

            border-radius: 5px;

            -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);

            -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);

            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);

            -webkit-background-clip: padding-box;

            -moz-background-clip: padding;

            background-clip: padding-box;

            *border-right-width: 2px;

            *border-bottom-width: 2px;

        }

        .ztpl-ui-menu-item > a.ztpl-ui-corner-all {

            display: block;

            padding: 3px 15px;

            clear: both;

            font-weight: normal;

            line-height: 18px;

            color: #555555;

            white-space: nowrap;

        }

        .ui-state-active, .ui-widget-content .ui-state-active {

            display: inline-block;

            /* width: 100%; */

            text-decoration: none;

            color: #fff;

            background: #337ab7;

            text-decoration: none;

            padding: 1px;

            white-space: nowrap;

            overflow: hidden;

            text-overflow: ellipsis;

            cursor: pointer;

        }

        .ztpl-ui-menu-item-wrapper {

            color: #000;

            padding: 5px;

            white-space: nowrap;

            overflow: hidden;

            text-overflow: ellipsis;

            cursor: pointer;

        }
        li.ztpl-ui-state-disabled {
            padding: 0 5px;
        }
        .ztpl-ui-autocomplete{
            max-height: 300px;
            overflow: auto;
        }
        .ztpl-ui-helper-hidden-accessible {
            display:none;
        }
		.ui-helper-hidden-accessible {
            display:none;
        }
        .ui-widget.ui-widget-content{
            border:1px solid #c5c5c5;
        }
        .ui-widget-content{
            border:1px solid #ddd;
            background: #fff;
            color:#333;
        }
        .ui-widget{
            font-family: Arial,Helvetica,sans-serif;
            font-size: 1em;
        }
        .ui-font{
            z-index:100;
        }

        /*extra css*/
        .ztpl-row:after, .ztpl-row:before {
            display: table;
            content: " ";
        }
        .ztpl-row:after{
            clear: both;
        }
        .ztpl-btn{
            width: auto;
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
        .ui-widget.ui-widget-content li.ui-menu-item img {
            margin-right: 15px;
            vertical-align: middle;
            display: block;
            float: left;
        }
        .ui-widget.ui-widget-content li.ui-menu-item a .pn_number {
            font-weight: bold;
            color: #000;
            font-size: 12px;
            letter-spacing: 0.5px;
        }
        /*.pn_number{
            position: absolute;
            top: 0;
            left: 50px;
        }*/
        .ztpl-row-wrapper {
            width: 100%;
            
        }
        .ztpl-d-flex{
            display: flex;
            
        }
       /* .ztpl-cus-width {
            padding: 0 5px;
        }*/
       
    </style>

