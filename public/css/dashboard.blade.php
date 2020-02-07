<?php
//dd($shop_domain);
?>
@extends('header')
@section('content')

<script type="text/javascript">
    ShopifyApp.ready(function () {
        ShopifyApp.Bar.initialize({
            buttons: {
                primary: {
                    label: 'HELP',
                    href: '{{ url('/help') }}',
                    loading: false
                }
            },
        });
    });
</script>
<div class="container bulk-order-container">

    <div class="row">

        <div class="card">

            <div class="card-content">

                <P>

                    The Bulk Order Has Been Installed!

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

    </div>

</div>
@endsection