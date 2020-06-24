var base_path_bolv = "https://shopifydev.anujdalal.com/bulkorder-demo-new/";
//var $ztpl_one_page_quick_order = jQuery.noConflict();

//var ztpl_one_page_quick_order = jQuery;
var $ztpl_one_page_quick_order = "";


// loadCSS = function(href) {
//     //alert(href);
//     var link = document.createElement('link');
//     link.type = "text/css";
//     link.href = href;
//     document.head.append(link);


// };

loadJS = function(src) {
    //alert(src);
    var script = document.createElement('script');
    script.type = "text/javascript";
    script.src = src;
    //document.head.append(script);
    document.body.append(script);

};

//loadCSS(base_path_bolv + "public/js/jquery.fancybox.css");

// loadCSS("https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css");

// loadCSS("https://cdn.datatables.net/responsive/2.2.1/css/responsive.dataTables.min.css");

//loadCSS("https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css");

function load_additional_js() {

    loadJS(base_path_bolv + "public/js/jquery.fancybox.pack.js");

    loadJS(base_path_bolv + "public/js/flyto.js");

    loadJS(base_path_bolv + "public/js/jquery.dataTables.min.js");

    //    loadJS(base_path_bolv + "public/js/dataTables.responsive.min.js");

    //loadJS("https://code.jquery.com/ui/1.12.1/jquery-ui.js");
}

if (typeof jQuery == 'undefined') {

    //If Undefined or Not available, Then Load	
    (function() {
        //alert('test');
        var jscript = document.createElement("script");
        jscript.src = "https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js";
        jscript.type = 'text/javascript';
        jscript.async = false;
        document.head.append(jscript);
        jscript.onload = function() {
            if (typeof jQuery.ui == 'undefined') {
                //alert('test');
                //If Undefined or Not available, Then Load				
                var script = document.createElement('script');
                script.type = "text/javascript";
                script.src = base_path_bolv + 'public/js/jquery-ui.js';
                document.head.append(script);
                //alert(script);
                script.onload = function() {
                    //alert('re');
                    //Assigning Jquery Object to Zestard_jq
                    $ztpl_one_page_quick_order = window.jQuery;
                    load_additional_js();
                    bulk_order_list_view();
                    //};										                                				
                }
            } else {
                //Assigning Jquery Object to Zestard_jq
                $ztpl_one_page_quick_order = window.jQuery;
                load_additional_js();
                bulk_order_list_view();
            }
        };
    })();
} else {

    if (typeof jQuery.ui == 'undefined') {
        //If Undefined or Not available, Then Load				
        var script = document.createElement('script');
        script.type = "text/javascript";
        script.src = base_path_bolv + 'public/js/jquery-ui.js';
        document.head.append(script);
        script.onload = function() {
            // Check If jQuery UI Object is Undefined					
            {
                $ztpl_one_page_quick_order = window.jQuery;
                load_additional_js();
                bulk_order_list_view();
            }
        }
    } else {
        $ztpl_one_page_quick_order = window.jQuery;
        load_additional_js();
        bulk_order_list_view();

    }
}
// function bulk_order_list_view(){}
function bulk_order_list_view() {
    var ProductlistDiv = $ztpl_one_page_quick_order('.zestard_bulk_order_list_view').length;

    if (ProductlistDiv > 0) {

        var store_encrypt = $ztpl_one_page_quick_order('.zestard_bulk_order_list_view').attr("store_encrypt");

        //var blog_id = $ztpl_one_page_quick_order('.zestard_bulk_order_list_view').attr("blog_id");

        if (store_encrypt != '') {

            $ztpl_one_page_quick_order.ajax({

                type: "POST",

                url: base_path_bolv + "public/frontend_quick_order",

                crossDomain: true,

                data: { store_encrypt: store_encrypt },

                success: function(data) {

                    $ztpl_one_page_quick_order('.zestard_bulk_order_list_view').html(data);

                }

            });
        }
    }
}