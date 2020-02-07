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
    
    <!--<link rel="stylesheet" href="{{ asset('css/style.css') }}" >-->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/quick_oder_style.css') }}" />
    <!-- toastr CSS -->
    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/custom-style.css') }}">
    <!-- flag CSS -->
    <!--<link rel="stylesheet" href="{{ asset('css/phoca-flags.css') }}">-->
        
    <!-- shopify Script for fast load -->
	<script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
  <script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
  <!-- For Datatable -->
  <script src="{{ asset('js/datatable/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('js/datatable/dataTables.bootstrap.min.js') }}"></script>
  <script src="{{ asset('js/datatable/buttons.html5.min.js') }}"></script>
  <script src="{{ asset('js/datatable/dataTables.buttons.min.js') }}"></script>
  <!-- For Datatable -->
    <script src="https://cdn.shopify.com/s/assets/external/app.js"></script>
  
  <!-- spectrun datepicker -->
  <link rel="stylesheet" href="{{ asset('css/spectrum.css') }}">
  <script src="{{ asset('js/spectrum.js') }}"></script>
  
  <link rel="stylesheet" href="{{ asset('css/introjs.css') }}">
        <script src="{{ asset('js/intro.js') }}"></script>
    <script>
      ShopifyApp.init({
        apiKey: '3b74510dbc98e99200509223407d2e4b',
        shopOrigin: '<?php echo "https://".session('shop'); ?>'
      });

      ShopifyApp.ready(function() {
          ShopifyApp.Bar.initialize({
            icon: "{{ asset('image/bulk-order-icon3.jpg') }}",
            title: '',
            buttons: {}
          });
        });
    </script>
    <style>
      .review-div {
                text-align: center !important;
                width: 100%;
                background-color: #fafad0;
                padding: 10px;
                box-shadow: 1px 1px 2px #777;
                display: inline-block;
                position: sticky;
                top: 0px;
                left: 0px;
                z-index: 100;
            }
    </style>
  </head>

  <body>

@yield('navigation')
<div class="review-div">
  Are you loving this app? Please spend 2mins to help us <a href="https://apps.shopify.com/bulk-order?reveal_new_review=true" target="_blank">write a review</a>. 
</div>
<!--<div class="navbar">
    <nav class="bulk-order-nav">
      <div class="nav-wrapper container">
        <div class="row">
          <div class="col s12">
            <p class="brand-logo center">
                <a href="{{ url('/dashboard') }}">One Page Bulk Order</a>
            </p>
            <a href="{{ url('/help') }}" class="help">Help?</a>
          </div>
        </div>
      </div>
    </nav>
  </div>-->
@yield('content')
<!--<script src="{{ asset('js/materialize.min.js') }}"></script>-->
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/jquery.copy-to-clipboard.js') }}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="{{ asset('js/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('js/javascript.js') }}"></script>
<script>
    $('.info_css').magnificPopup({
        type: 'image'
    });
</script>
<script>
  @if(Session::has('notification'))
      
    var type = "{{ Session::get('notification.alert-type') }}";    
    toastr.options = {
      "closeButton": true,
      "debug": false,
      "newestOnTop": false,
      "progressBar": false,
      "positionClass": "toast-top-right",
      "preventDuplicates": false,
      "onclick": null,
      "showDuration": "300",
      "hideDuration": "1000",
      "timeOut": "5000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    }
    switch(type){
        case 'info':
            toastr.info("{{ Session::get('notification.message') }}");
            break;
        case 'warning':
            toastr.warning("{{ Session::get('notification.message') }}");
            break;
        case 'success':
            toastr.success("{{ Session::get('notification.message') }}");
            break;
        case 'error':
            toastr.error("{{ Session::get('notification.message') }}");
            break;
        case 'options':
            toastr.warning("{{ Session::get('notification.message') }}");
            break;
    }
  @endif
</script>

<script type="text/javascript">
  jQuery(".header_background_color").spectrum({
  preferredFormat: "hex",
          showInput: true,
          showAlpha: true,
          palette: [["red", "rgba(0, 255, 0, .5)", "rgb(0, 0, 255)"]]
  });
  jQuery(".show_options_background_color").spectrum({
  preferredFormat: "hex",
          showInput: true,
          showAlpha: true,
          palette: [["red", "rgba(0, 255, 0, .5)", "rgb(0, 0, 255)"]]
  });
  jQuery(".add_to_cart_background_color").spectrum({
  preferredFormat: "hex",
          showInput: true,
          showAlpha: true,
          palette: [["red", "rgba(0, 255, 0, .5)", "rgb(0, 0, 255)"]]
  });
  jQuery(".show_options_text_color").spectrum({
  preferredFormat: "hex",
          showInput: true,
          showAlpha: true,
          palette: [["red", "rgba(0, 255, 0, .5)", "rgb(0, 0, 255)"]]
  });
  jQuery(".add_to_cart_text_color").spectrum({
  preferredFormat: "hex",
          showInput: true,
          showAlpha: true,
          palette: [["red", "rgba(0, 255, 0, .5)", "rgb(0, 0, 255)"]]
  });
  jQuery(".sold_out_background_color").spectrum({
  preferredFormat: "hex",
          showInput: true,
          showAlpha: true,
          palette: [["red", "rgba(0, 255, 0, .5)", "rgb(0, 0, 255)"]]
  });
  jQuery(".sold_out_text_color").spectrum({
  preferredFormat: "hex",
          showInput: true,
          showAlpha: true,
          palette: [["red", "rgba(0, 255, 0, .5)", "rgb(0, 0, 255)"]]
  });
</script>


<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5a2e20e35d3202175d9b7782/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
</body>
</html>
