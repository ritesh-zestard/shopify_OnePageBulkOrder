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
    title: 'Dashboard',
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
                label: 'Quick Order Help',
                href: '{{ url('quick_help') }}?shop=<?php echo $shop; ?>',
                loading: true
            },
            {
                label: 'Bulk Order Help',
                href: '{{ url('help') }}?shop=<?php echo $shop; ?>',
                loading: false
            }
        ]
    }
    });
});
</script>
<link rel="stylesheet" type="text/css" href="{{asset('css/new_design/uptown.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('css/new_design/custom.css')}}">
<body>
    <main class="full-width easy-donation-main ">
        <header>
            <div class="container">
                <div class="adjust-margin toc-block">
                    <h1>Dashboard</h1>
                </div>
            </div>
        </header>
        <section>
            <div class="full-width">
                <article>
                    <div class="columns six card dash-section" data-step="2" data-intro="Once donation gets create, please click on 'Configure' button to make general settings. It will allow to enable/disable application and to enter other general information for the donation box.">
                        <h3>BulkOrder Settings</h3>
                        <img src="{{asset('images/bulkorder_image.png')}}" width="80px">
                        <div class="row">
                            <p>Allows you to set sections label and manage CSS of Bulk Order Page.</p>
                            <a href="{{url('dashboard')}}?shop=<?php echo $shop;?>"><button class="btn btn-primary" type="submit" name="save">BulkOrder</button></a>
                        </div>
                    </div>
                    <div class="columns six card dash-section" data-step="3" data-intro="This section will provide information for the donations made throughout the year against each month. Use 'Track' button to know the overall statistics information of the donations.">
                        <h3>QuickOrder Settings</h3>
                        <img src="{{asset('images/quickorder_image.png')}}" width="80px">
                        <div class="row">
                            <p>Allows you to set sections label and manage CSS of Quick Order Page.</p>
                            <a href="{{ url('quick_order_dashboard_save') }}?shop=<?php echo $shop; ?>"><button class="btn btn-primary" type="submit" name="save">QuickOrder</button></a>
                        </div>
                    </div>
                </article>
                <!-- Documentation Start -->
                <!-- Documentation End -->
            </div>
        </section>

        <footer></footer>
    </main>
</body>
@endsection
