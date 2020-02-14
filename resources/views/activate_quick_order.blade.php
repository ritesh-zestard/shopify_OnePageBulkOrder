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
            buttons: {
                secondary: [
                    {
                        label: 'Bulk Order Settings',
                        href: '{{ url('dashboard') }}?shop=<?php echo $shop; ?>',
                        loading: false
                    }
                ]
            }
        });
    });
</script>





<div class="container bulk-order-container">
    <div class="row">
        <div class="card">
            <div class="card-content">
                <p>To use quick order first you have to activate it.</p>
                <form action="{{ route('activate_quick_order') }}" method="GET" name="activate quick order form"> 
                    <input type="submit" name="activate_quick_order" class="activate_quick_order" id="activate_quick_order" value="Activate Quick Order" />
                </form>                
            </div>
        </div>        
    </div>
</div>

<style>
    .activate_quick_order{
        position: relative;
        display: inline-block;
        min-height: 3.6rem;
        min-width: 3.6rem;
        margin: 0;
        padding: .7rem 1.6rem;
        background: -webkit-linear-gradient(top, #6371c7, #5563c1);
        background: linear-gradient(180deg, #6371c7, #5563c1);
        border: .1rem solid #3f4eae;
        box-shadow: inset 0 1px 0 0 #6774c8, 0 1px 0 0 rgba(22, 29, 37, 0.05), 0 0 0 0 transparent;
        color: #fff;
        fill: #fff;
        border-radius: .3rem;
        line-height: normal;
        text-align: center;
        text-decoration: none;
        -webkit-transition-property: background,border,box-shadow;
        transition-property: background,border,box-shadow;
        -webkit-transition-duration: .2s;
        transition-duration: .2s;
        -webkit-transition-timing-function: cubic-bezier(0.64, 0, 0.35, 1);
        transition-timing-function: cubic-bezier(0.64, 0, 0.35, 1);
        box-sizing: border-box;
        cursor: pointer;
        white-space: nowrap;
        text-transform: none;
        font-family: -apple-system, "BlinkMacSystemFont", "San Francisco", "Roboto", "Segoe UI", "Helvetica Neue", sans-serif;
        font-weight: normal;
        vertical-align: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        -webkit-appearance: none;
        -webkit-tap-highlight-color: transparent;
    }
    
</style>    


@endsection