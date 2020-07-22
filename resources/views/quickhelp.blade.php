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
<script type="text/javascript">
    ShopifyApp.ready(function (e) {
        ShopifyApp.Bar.initialize({
            title:'Quick order Help',
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
                        label: 'Bulk Order Help',
                        href: '{{ url('help') }}?shop=<?php echo $shop; ?>',
                        loading: false
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
?>

<?php
if (Session::has('shop')) {
    $app_page = "https://" . session('shop') . "/pages/one-page-quick-order";
    $pages = "https://" . session('shop') . "/admin/pages";
    $apps = "https://" . session('shop') . "/admin/apps";
} else if(isset($_REQUEST['shop'])){
    $app_page = "https://" . $shop . "/pages/one-page-quick-order";
    $pages = "https://" . $shop . "/admin/pages";
    $apps = "https://" . $shop . "/admin/apps";
} else {
    $app_page = "#";
    $pages = "#";
    $apps = "#";
}
?>

<div class="container formcolor formcolor_help" >
    <div class=" row">
        <div class="help_page">
            <div class="col-md-12 col-sm-12 col-xs-12 need_help">
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
                <h2 class="dd-help">What is purpose of One Page Quick Order?</h2> <!-- What does One Page Quick Order do?  -->
                <ul>
                    <li>One Page Quick Order Application will add the quick order page to your store from where your customers can select any/all products along with different variants and multiple quantities to add them to the cart.</li>
                </ul>    
                <h2 class="dd-help">How One Page Quick Order will be displayed in my store?</h2>                
                <ul>
                    <li>One page Quick Order seperate page will get created automatically which you can see at <a href ="<?php echo $app_page;?>" target ="_blank"><?php echo $app_page;?></a>.</li>
                    <?php /*<li>To view the page, visit the <a href ="<?php echo $pages;?>" target ="_blank">Pages section</a> in your Shopify Admin.</li> */?>                   
                </ul>
                <h2 class="dd-help">How to Configure?</h2> 
                <ul>
                    <li>To make general settings configuration, please use the button <b>General Settings Demo</b> which will guide and provide information for purpose of each section and what kind of data should be inserted.</li>
                    <li>One Page Quick Order seperate page will be available at <a href ="<?php echo $app_page;?>" target ="_blank"><?php echo $app_page;?></a>.</li>
                    <li>This link can be shared with your customers or else it can also be used by linking to your store's navigation menu. <a class="info_css" href="{{ asset('image/navigation.png') }}" target="_blank">See Example</a>
                    </li>
                    <li>For more reference on how to create menu item in your navigation check the <b>Shopify Help center</b> tutorial <a href="https://help.shopify.com/en/manual/sell-online/online-store/menus-and-links/editing-menus#add-a-menu-item" target="_blank">Here</a>.</li>
                    <?php /*<li>To edit or delete the page, visit the <a href ="<?php echo $pages;?>" target ="_blank">Pages section</a> in your Shopify Admin.</li> */?>                   
                    {{-- <li> If you want to display the app in any page other then above created page, 
                    then copy the shortcode below and paste it in appropriate file.</li>  --}}
                </ul>
                <?php /*<h2 class="dd-help">How to use app? (User guide)</h2>
                <ul>
                    <li>To read or download the user guide of how you can use the app click <a href="{{ url('/download/user_guide.pdf') }}" target="_blank"> here</a>.</li>
                </ul>
                */ ?>
                <?php /*<ul class="shortcode-note">
                    <li>
                        <div class="copystyle_wrapper">  
                            <textarea id="script_code" rows="1" class="form-control short-code"  readonly=""><?php echo "<div class='zestard_bulk_order_list_view' store_encrypt= " . $encrypted_store_id . "></div>"; ?>
                            </textarea>
                            <btn id="copy_script" name="copy_script" value="Copy Shortcode" class="btn btn-info copycss_button" data-clipboard-target=".script_code" style="display: block;" onclick="copy_shortcode()"><i class="fa fa-check"></i> Copy</btn>
                        </div>
                    </li>

                </ul> */ ?>
                         
            </div>
            
             
        </div>
           <div class="col-md-12 col-sm-12 col-xs-12">
       <h2 class="dd-help">Uninstall Instruction</h2>
        <ul>
            <li>To uninstall the app, just delete the app from your <a href ="{{ $apps}}" target="_blank">App Section</a>.</li>
            <li>To delete One Page Quick Order page app just go to your <a href ="{{ $pages }}" target="_blank">Page Section</a> and delete the One Page Quick Order.</li>
            <li>To reinstall the app, go to Shopify App Store and find the One Page Quick Order app.</li>
            <li>Then install the app and it will automatically create the One Page Quick Order page.</li>
        </ul>
           </div>
        <br/>
        <br/>
        <br/>
        <!--Version updates

        <div class="version_update_section">

            <div class="col-md-6" style="padding-right: 0;">

                <div class="feature_box">

                    <h3 class="dd-help">Version Updates <span class="verison_no">2.0</span></h3>

                    <div class="version_block">

                        <div class="col-md-12">

                            <div class="col-md-3 version_date">

                                <p><i class="glyphicon glyphicon-heart"></i></p>

                                <strong>22 Jan, 2018</strong>

                                <a href="#"><b>Update</b></a>

                            </div>

                            <div class="col-md-8 version_details">

                                <strong>Version 2.0</strong>

                                <ul>

                                    <li>Add Delivery Date & Time information under customer order Email</li>

                                    <li>Auto Select for Next Available Delivery Date</li>

                                    <li>Auto Tag Delivery Details to all the Orders</li>

                                    <li>Manage Cut Off Time for Each Individual Weekday</li>

                                    <li>Limit Number of Order Delivery during the given time slot for any day </li>

                                </ul>

                            </div>

                        </div>

                        <div class="col-md-12">

                            <div class="col-md-3 version_date">

                                <p><i class="glyphicon glyphicon-globe"></i></p>

                                <strong>20 Dec, 2017</strong>

                                <a href="#"><b>Release</b></a>

                            </div>

                            <div class="col-md-8 version_details version_details_2">

                                <strong>Version 1.0</strong>

                                <ul>

                                    <li>Delivery Date & Time Selection</li>

                                    <li>Same Day & Next Day Delivery</li>

                                    <li>Blocking Specific Days and Dates</li>

                                    <li>Admin Order Manage & Export Based on Delivery Date</li>

                                    <li>Option for Cut Off Time & Delivery Time</li>

                                </ul>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-6">

                <div class="feature_box">

                    <h3 class="dd-help">Upcoming Features</h3>

                    <div class="feature_block">

                        <div>   

                            <span class="checkboxFive">

                                <input type="checkbox" checked disabled value="1" id="checkbox0" name="exclude_block_date_status">

                                <label for="checkbox0"></label>

                            </span> 

                            <strong>Multiple Cutoff Time Option</strong>  

                        </div>

                        <div>   

                            <span class="checkboxFive">

                                <input type="checkbox" checked disabled value="1" id="checkbox4" name="exclude_block_date_status">

                                <label for="checkbox4"></label>

                            </span> 

                            <strong>Multiple Delivery Time Option</strong>  

                        </div>

                        <div>

                            <span class="checkboxFive">

                                <input type="checkbox" checked disabled value="1" id="checkbox5" name="exclude_block_date_status">

                                <label for="checkbox5"></label>

                            </span> 

                            <strong>Auto Tag Delivery Details to all the Orders Within Interval of 1 hour from Order Placed Time</strong>  

                        </div>

                        <div>   

                            <span class="checkboxFive">

                                <input type="checkbox" checked disabled value="1" id="checkbox1" name="exclude_block_date_status">

                                <label for="checkbox1"></label>

                            </span> 

                            <strong>Auto Select for Next Available Delivery Date</strong>  

                        </div>

                        <div>   

                            <span class="checkboxFive">

                                <input type="checkbox" checked disabled value="1" id="checkbox2" name="exclude_block_date_status">

                                <label for="checkbox2"></label>

                            </span> 

                            <strong>Order Export in Excel</strong>  

                        </div>

                        <div>   

                            <span class="checkboxFive">

                                <input type="checkbox" checked disabled value="1" id="checkbox3" name="exclude_block_date_status">

                                <label for="checkbox3"></label>

                            </span> 

                            <strong>Filtering Orders by Delivery Date</strong>  

                        </div>                                        

                    </div>

                    <div>

                        <p class="feature_text">

                            New features are always welcome send us on : <a href="mailto:support@zestard.com"><b>support@zestard.com</b></a> 

                        </p>

                    </div>

                </div>

            </div>

        </div>
-->
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
@endsection