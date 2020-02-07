@yield('header')
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="../../favicon.ico">

        <title>Bulkorder Zestard</title>

        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/magnific-popup.css') }}">
        <link href="https://fonts.googleapis.com/css?family=Muli" rel="stylesheet">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css"> 
        <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.css">

        <link rel="stylesheet" href="{{ asset('css/style.css') }}" >

        <script src="https://cdn.shopify.com/s/assets/external/app.js"></script>
        <script>
ShopifyApp.init({
    apiKey: '3b74510dbc98e99200509223407d2e4b',
    shopOrigin: '<?php echo "https://" . session('shop'); ?>'
});

ShopifyApp.ready(function () {
    ShopifyApp.Bar.initialize({
        icon: "{{ asset('image/bulk-order-icon3.jpg') }}",
        title: '',
        buttons: {}
    });
});
        </script>

    </head>
    <div class="info_css">
    </div>
    <body>

        @yield('navigation')

        @yield('content')
        <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <!--<script src="{{ asset('js/materialize.min.js') }}"></script>-->
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/jquery.copy-to-clipboard.js') }}"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        <script src="{{ asset('js/jquery.magnific-popup.min.js') }}"></script>
        <script src="{{ asset('js/javascript.js') }}"></script>
        <script>
ShopifyApp.Modal.open({
    src: '{{url("link_detail")}}',
    width: 'small',
    height: 200,

}, function (result, data) {
});
        </script>

    </body>
</html>
