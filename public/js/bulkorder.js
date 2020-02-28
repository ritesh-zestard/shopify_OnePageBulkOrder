var base_path_bulkorder = "https://zestardshop.com/shopifyapp/bulkorder/public/";
var result, $zestard_bulk_order = "";
if (typeof jQuery == 'undefined' || Shopify.shop == "little-mango-deodorants.myshopify.com") {
    //alert('jQuery is Undefined');	
    //If Undefined or Not available, Then Load	
    (function() {
        var jscript = document.createElement("script");
        var bulkorder_jscript = document.createElement("script");
        //jscript.src = base_path_bulkorder + "js/3.2.1.jquery.min.js";
        //jscript.src = "https://code.jquery.com/jquery-1.10.2.js";
        jscript.src = "https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js";
        jscript.type = 'text/javascript';
        jscript.async = false;

        document.head.append(jscript);

        jscript.onload = function() {
            //Assigning Jquery Object to Zestard_jq
            $zestard_bulk_order = window.jQuery;
            //alert('jQuery Loaded');			
            //Send Email that jQuery is Still not Working										
            if (typeof jQuery.ui == 'undefined') {
                //If Undefined or Not available, Then Load				
                var script = document.createElement('script');
                script.type = "text/javascript";
                script.src = base_path_bulkorder + 'js/jquery-ui.js';
                document.head.append(script);

                script.onload = function() {
                    bulkorder_jscript.src = base_path_bulkorder + "js/loadingoverlay.min.js";
                    bulkorder_jscript.type = 'text/javascript';
                    bulkorder_jscript.async = false;
                    //bulkorder_jscript.onload = function() {   								
                    bulkorder();
                    //};										                                				
                }
            }
        };
    })();
} else {
    $zestard_bulk_order = window.jQuery;
    var bulkorder_jscript = document.createElement("script");
    if (typeof jQuery.ui == 'undefined') {
        //If Undefined or Not available, Then Load				
        var script = document.createElement('script');
        script.type = "text/javascript";
        script.src = base_path_bulkorder + 'js/jquery-ui.js';
        document.head.append(script);
        script.onload = function() {
            // Check If jQuery UI Object is Undefined					
            {
                $zestard_bulk_order = window.jQuery;
                //bulkorder_jscript.src = "https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.0.2/dist/loadingoverlay.min.js";
                bulkorder_jscript.src = base_path_bulkorder + "js/loadingoverlay_progress.min.js";
                bulkorder_jscript.type = 'text/javascript';
                bulkorder_jscript.async = false;
                //bulkorder_jscript.onload = function(){ 								
                bulkorder();
                //};	                        
            }
        }
    } else {
        //console.log('here');
        /* bulkorder_jscript.src = base_path_bulkorder + "js/loadingoverlay.min.js";
        bulkorder_jscript.type = 'text/javascript';							
        bulkorder_jscript.async = false;
        bulkorder_jscript.onload = function() { 
        	console.log('loaded'); */
        $zestard_bulk_order = window.jQuery;
        bulkorder();
        //};	                                
    }
}

function bulkorder() {
    var shop = Shopify.shop;
    //console.log(shop);
    var zestard_bulk_order_id = $zestard_bulk_order('.bulkorder').attr('id');
    if (shop == "mailersdirect.myshopify.com") {
        zestard_bulk_order_id = $zestard_bulk_order('.ztpl-bulkorder').attr('id');
        //zestard_bulk_order_id = document.getElementsByClassName('ztpl-bulkorder')[1].id;
    }
    //var zestard_bulk_order_id = document.getElementsByClassName('bulkorder')[0].id;        
    if (zestard_bulk_order_id) {
        $zestard_bulk_order.ajax({
            type: "POST",
            url: base_path_bulkorder + "search",
            crossDomain: true,
            async: false,
            data: { 'id': zestard_bulk_order_id },
            success: function(data) {
                result = data;
            }
        });
        $zestard_bulk_order(".bulkorder").html(result);
        if (shop == "mailersdirect.myshopify.com") {
            $zestard_bulk_order(".ztpl-bulkorder").html(result);
            //document.getElementsByClassName('bulkorder').html(result);
        }

    }
}