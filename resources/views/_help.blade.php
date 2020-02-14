@extends('header')
@section('content')
<script>
 ShopifyApp.ready(function() {
	ShopifyApp.Bar.initialize({
	icon: "{{ asset('image/bulk-order-icon3.jpg') }}",
	title: 'Help',
	buttons: {}
	});
});
</script>
<?php
$pageUrl = 'javscript:void(0);';
$appUrl = 'javscript:void(0);';
if(Session::has('shop')){
	$pageUrl = "https://".session('shop')."/admin/pages";
	$appUrl = "https://".session('shop')."/admin/apps";
}
?>
<div class="container bulk-order-container">
	<div class="row">
		<div class="card">
			<div class="card-content">
				<h5>Need Help?</h5>
				<p><b>To customize anything within the app or for support inquiries contact us at:</b></p>
				<ul>
					<li>Developer: <b><a target="_blank" href="https://www.zestard.com">Zestard Technologies Pvt Ltd</a></b></li>
					<li>Email: <b><a href="mailto:support@zestard.com">support@zestard.com</a></b></li>
					<li>Website: <b><a target="_blank" href="https://www.zestard.com">https://www.zestard.com</a></b></li>
				</ul>
				<hr>
				<h5>Limitations</h5>
				<ul class="limit">
					<li>After installing the app, it automatically creates the page, in which customers are able to make the orders.</li>
					<li>That page will take your theme look and feel(stylesheet).</li>
					<li>The App supports only one currency.</li>
					<li>After installing the App, you can navigate that page on your menu.</li>
					<li>This App only works for the cart page. This app does not work for drawer carts.</li>
				</ul>
				<h5>App Uninstall/Reinstall Instructions</h5>
				<ul class="limit">
					<li>To uninstall the app, just delete the app from your <a href="<?php echo $appUrl ?>">App Section.</a></li>
					<li>To delete <b>One Page Bulk Order page</b> app just go to your <a href="<?php echo $pageUrl ?>">Page Section</a> and delete the <b>Bulk Order page.</b></li>
					<li>Once the above two steps are completed remove the app from your store.</li>
					<li>To reinstall the app, go to Shopify App Store and find the <a href="https://apps.shopify.com/bulk-order" target="_blank">One Page Bulk Order</a> app.</li>
					<li>Then install the app and it will automatically create the Bulk Order page.</li>
				</ul>
				<a class="goback" href="{{ url('dashboard') }}"><img src="{!! asset('image/back.png') !!}">Go Back</a>
			</div>
		</div>
	</div>
</div>
@endsection
<style>
.limit {
	margin-left: 20px;
}
ul.limit li {
	list-style-type: disc !important;	
}
</style>