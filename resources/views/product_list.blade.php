@extends('header')
@section('content')

<script type="text/javascript">
    ShopifyApp.ready(function(){
        ShopifyApp.Bar.initialize({
            buttons: {
                secondary: [
                    {
                        label: 'HELP',
                        href: '{{ url('help') }}',
                        loading: false
                    },
                    {
                        label: 'DASHBOARD',
                        href: '{{ url('dashboard') }}',
                        loading: false
                    }
                ]
            }
        });
        
    });
    //allow_out_of_stock
</script>

<div class="container bulk-order-container">

    <div class="row">

        <div class="card">

            <div class="card-content">
                <table id="bulk_order_products">
                    <thead>
                        <tr>
                            <th style="width:30px">Select Products</th>
                            <th>Product Image</th>
                            <th>Product Name</th> 
                            <th>Product Price</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td><input type="checkbox" name="product_select[]" class="product_select" value="{{ $product->id }}"
                                <?php if(count($selected_products > 0)){
                                    if(in_array($product->id, $selected_products)){
                                        echo "checked";
                                    }
                                } ?> onChange="product_select_model($(this).val())" ></td>
                            <td><?php if(count($product->image) > 0){ ?><img src="{{ $product->image->src }}" width="50px" height="50px"/><?php } ?></td>
                            <td>{{ $product->title }}</td>

                            <td>{{ $currency }}{{ $product->variants[0]->price }}</td>
                        </tr>
                    @endforeach
                    </tbody>    
                </table>

            <form name="addProduct" action="{{ url('update_products') }}">
                <input type="hidden" id="selected_product" name="selected_product" @if(count($selected_products > 0)) value='<?php echo json_encode($selected_products); ?>'@endif>
                <input type="submit" class="btn" id="save_update_products" value="Save"/>
            </form>
            </div>

        </div>

        
    </div>
</div>

<script>
    var selected_products = new Array();
    $(document).ready(function() {
        
        $('#bulk_order_products').DataTable({
            "processing": true,
            "serverSide": true,
            "ordering": false,
            "responsive": true,
            "ajax": "{{url('get_all_product')}}",
            "language": {
            "processing": "Loading. Please wait..."
            }
        });
    });

    function product_select_model(current_id){
        //var current_id = $(this).attr('id');
        
        var chk_selected = $('#'+current_id+':checkbox:checked').length;
        
        if(chk_selected == 1){
            selected_products.push(current_id);
            if ($('#selected_product').val() !=""){
                var existing_value = jQuery.parseJSON($('#selected_product').val());
                if(jQuery.inArray(current_id, existing_value)){
                }
                else{
                    existing_value.push(current_id);
                    $('#selected_product').val(JSON.stringify(existing_value));
                }
            }
            else{
                $('#selected_product').val(JSON.stringify(selected_products));
            }
        }
        else{
            selected_products.pop(current_id);
            if ($('#selected_product').val() !=""){
                var existing_value = jQuery.parseJSON($('#selected_product').val());
                if(jQuery.inArray(current_id, existing_value)){
                    existing_value.push(current_id);
                    $('#selected_product').val(JSON.stringify(existing_value));
                }  
            }
            else{
                $('#selected_product').val(JSON.stringify(selected_products));
            }
        }
       
    }	
</script>

@endsection