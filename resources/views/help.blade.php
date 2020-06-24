@extends('header')
@section('content')
<?php
        if (!session('shop')) {
            $shop = session('shop');
        } else if(isset($_REQUEST['shop'])) {
            $shop = $_REQUEST['shop'];
        }else{
            $shop = "";
        }        
        ?> 
<script>
 ShopifyApp.ready(function() {
	ShopifyApp.Bar.initialize({
	icon: "{{ asset('image/bulk-order-icon3.jpg') }}",
	title: 'Bulk Order Help',
	buttons: {
        secondary: [
            {
                label: 'Bulk Order Settings',
                href: '{{ url('dashboard') }}?shop=<?php echo $shop; ?>',
                loading: false
            },
            {
                label: 'Quick Order Settings',
                href: '{{ url('quick_order_dashboard_save') }}?shop=<?php echo $shop; ?>',
                loading: false
            },
            {
                label: 'Quick order Help',
                href: '{{ url('quick_help') }}?shop=<?php echo $shop; ?>',
                loading: true
            },
            {
                label: 'Dashboard',
                href: '{{ url('new_dashboard') }}',
                loading: true
            }
        ]
    }
	});
});
</script>
<?php 
    $store_name = session('shop'); 
    $pageUrl = 'javscript:void(0);';
    $appUrl = 'javscript:void(0);';
    if(Session::has('shop')){
	$pageUrl = "https://".session('shop')."/admin/pages";
	$appUrl = "https://".session('shop')."/admin/apps";
    }
?>
<link rel="stylesheet" href="{{ asset('css/design_style.css') }}" />
<div class="container formcolor formcolor_help" >
    <div class=" row">
        <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">              
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">×</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <img src="" class="imagepreview" style="width: 100%;">
                    </div>
                </div>
            </div>
        </div>
        <div class="help_page">            
            <div class="col-md-12 col-sm-12 col-xs-12 need_help">
                <div class="success-copied"></div>
                <h2 class="dd-help">Need Help?</h2>
                <p class="col-md-12"><b>To customize any thing within the app or for other work just contact us on below details</b></p>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <ul class="dd-help-ul">
                        <li><span>Developer: </span><a target="_blank" href="https://www.zestard.com">Zestard Technologies Pvt Ltd</a></li>
                        <li><span>Email: </span><a href="mailto:support@zestard.com" target="_top">support@zestard.com</a></li>
                        <li><span>Website: </span><a target="_blank" href="https://www.zestard.com">https://www.zestard.com</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-md-12 col-sm-12 col-xs-12">
                <h2 class="dd-help">Configuration Instruction</h2> 
                <div class="col-md-12 col-sm-12 col-xs-12 help_accordians">
                    <div class="panel-group" id="accordion">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <p data-toggle="collapse" data-parent="#accordion" href="#collapse1"> 
                                        <strong><span class="">Can we disable the product images in front-end at time of search?</span>
                                            <span class="fa fa-chevron-down pull-right"></span></strong>  
                                    </p>
                                </h4>
                            </div>
                            <div id="collapse1" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <p>Yes, you can do it by selecting "Off" option in <b>Show Product Image.</b></p>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <p data-toggle="collapse" data-parent="#accordion" href="#collapse2"> 
                                        <strong>
                                            <span class="">How to configure / show out of stock products in cart page?</span>
                                            <span class="fa fa-chevron-down pull-right"></span>
                                        </strong>  
                                    </p>
                                </h4>
                            </div>
                            <div id="collapse2" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <ul class="ul-help">
                                        <li>First check "Allow out of stock products to order1" option in Bulk Order Settings.</li>
                                        <li>Then search for the particular product in <b>Products</b> and open the page.</li>
                                        <li>Edit the varitants of the product.</li>
                                        <li>Here their is a Inventory Section, If in "Inventory policy" selected option is <b>Don't track Inventory</b> you don't have to do any thing, but if the selected option is <b>Shopify tracks this product's inventory</b> then you have to check the below checkbox which says "Allow customers to purchase this product when it's out of stock".</li>
                                    </ul>       
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <p data-toggle="collapse" data-parent="#accordion" href="#collapse3"> 
                                        <strong><span class="">When we can show available quantity?</span>
                                            <span class="fa fa-chevron-down pull-right"></span></strong>  
                                    </p>
                                </h4>
                            </div>
                            <div id="collapse3" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <ul class="ul-help">
                                        <p>If "Allow out of stock products to order" is uncheck then only you can check the "show avaliable quantity". It will show the avaliable quantity of the particular product after selecting the product.</p>
                                    </ul>                       
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <p data-toggle="collapse" data-parent="#accordion" href="#collapse4"> 
                                        <strong><span class="">How to change language or design in frontend?</span>
                                            <span class="fa fa-chevron-down pull-right"></span></strong>  
                                    </p>
                                </h4>
                            </div>
                            <div id="collapse4" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <ul class="ul-help">
                                        <li>In Language Settings you can place any text in any language in front of particular fields.</li>
                                        <li>For Desgining you can add additional CSS from backend.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>                        
                    </div>
                </div>
            </div>
            <!--Uninstall Process Div start-->
            <div class="col-md-12 col-sm-12 col-xs-12">
                <h2 class="dd-help">Uninstall Instruction</h2> 
                <div class="col-md-12 col-sm-12 col-xs-12 help_accordians">
                    <div class="panel-group" id="accordion">                        
                        <ul class="ul-help">
                            <li>To uninstall the app, just delete the app from your <a href="<?php echo $appUrl ?>" target="__blank">App Section.</a></li>
                            <li>To delete <b>One Page Bulk Order page</b> app just go to your <a href="<?php echo $pageUrl ?>" target="__blank">Page Section</a> and delete the <b>Bulk Order page.</b></li>
                            <li>Once the above two steps are completed remove the app from your store.</li>
                            <li>To reinstall the app, go to Shopify App Store and find the <a href="https://apps.shopify.com/bulk-order" target="_blank">One Page Bulk Order</a> app.</li>
                            <li>Then install the app and it will automatically create the Bulk Order page.</li>
                        </ul>                        
                    </div>
                </div>
            </div>
            <!--Uninstall Process Div end-->
        </div>   
    </div>
</div>
<script>
    $(document).ready(function () {
        // Add minus icon for collapse element which is open by default
        $(".collapse.in").each(function () {
            $(this).siblings(".panel-heading").find(".fa").addClass("fa-chevron-up").removeClass("fa-chevron-down");
        });
        // Toggle plus minus icon on show hide of collapse element
        $(".collapse").on('show.bs.collapse', function () {
            $(this).parent().find("span.fa").removeClass("fa-chevron-down").addClass("fa-chevron-up");
        }).on('hide.bs.collapse', function () {
            $(this).parent().find("span.fa").removeClass("fa-chevron-up").addClass("fa-chevron-down");
        });
    });
</script>
<script>
    var el1 = document.getElementById('copyproductBtn');
    var el2 = document.getElementById('copyblogBtn');
    var el3 = document.getElementById('copyrandomproductBtn');
    if (el1) {
        el1.addEventListener("click", function () {
            copyToClipboard(document.getElementById("product-shortcode"));
        });
    }
    if (el2) {
        el2.addEventListener("click", function () {
            copyToClipboard(document.getElementById("blog-shortcode"));
        });
    }
    if (el3) {
        el3.addEventListener("click", function () {
            copyToClipboard(document.getElementById("randomproduct-shortcode"));
        });
    }
    function copyToClipboard(elem) {
        var targetId = "_hiddenCopyText_";
        var isInput = elem.tagName === "INPUT" || elem.tagName === "TEXTAREA";
        var origSelectionStart, origSelectionEnd;
        if (isInput) {
            target = elem;
            origSelectionStart = elem.selectionStart;
            origSelectionEnd = elem.selectionEnd;
        } else {
            target = document.getElementById(targetId);
            if (!target) {
                var target = document.createElement("textarea");
                target.style.position = "absolute";
                target.style.left = "-9999px";
                target.style.top = "0";
                target.id = targetId;
                document.body.appendChild(target);
            }
            target.textContent = elem.textContent;
        }
        var currentFocus = document.activeElement;
        target.focus();
        target.setSelectionRange(0, target.value.length);
        var succeed;
        try {
            succeed = document.execCommand("copy");
        } catch (e) {
            succeed = false;
        }
        if (currentFocus && typeof currentFocus.focus === "function") {
            currentFocus.focus();
        }
        if (isInput) {
            elem.setSelectionRange(origSelectionStart, origSelectionEnd);
        } else {
            target.textContent = "";
        }
        return succeed;
    }
</script>
@endsection
