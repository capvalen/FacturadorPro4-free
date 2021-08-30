@foreach ($dataPaginate as $item)

    <div class="col-6 col-md-4">
        <div class="product product-style {{ stock($item, $configuration) ? 'productdisabled' : '' }}">
            <figure class="product-image-container">
                <a href="/ecommerce/item/{{ $item->id }}" class="product-image product-image-list">
                    <img src="{{ asset('storage/uploads/items/'.$item->image) }}" class="image" alt="product">
                </a>
                <a href="{{route('item_partial', ['id' => $item->id])}}" class="btn-quickview">Vista Rápida</a>
                {{-- <span class="product-label label-sale">-20%</span> --}}
                @if(json_encode($item->is_new) == 1)
                    <span class="product-label label-hot">New</span>
                @endif
                @if(stock($item, $configuration))
                    <span class="product-label product-danger">AGOTADO</span>
                @endif
            </figure>
            <div class="product-details">
                <div class="ratings-container">
                    <div class="product-ratings">
                        <span class="ratings" style="width:0%"></span>
                    </div>
                </div>
                {{ $item->internal_id }}
                <h2 class="product-title">
                    <a href="/ecommerce/item/{{ $item->id }}">{{ $item->description }}</a>
                </h2>
                <div class="price-box">
                    <!-- <span class="old-price">S/ {{ number_format( ($item->sale_unit_price * 1.2 ) , 2 )}}</span> -->
                    <span class="product-price">{{ $item->currency_type['symbol'] }} {{ number_format($item->sale_unit_price, 2) }}</span>
                </div>
                <div class="product-action">
                    <a href="#" class="paction add-cart" data-product="{{ json_encode( $item ) }}" title="Add to Cart">
                        <span>Agregar a Carrito</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

@endforeach

<?php

    function stock($item, $config)
    {
        if($config) {
            $stock=0;
            foreach ($item->warehouses as $key => $value) {
                $stock += $value->stock;
            }
            return ($stock > 0) ? false : true;
        }
    }
?>

<style>
    /* .product-style {
        border-style: solid;
        border-width: 1px;
        border-color: "#ddd";
        margin: 10px 1px;
    } */
    .product-image-list {
        max-height: 210px;
        min-height: 210px;
    }
    .image {
        max-height: 210px;
    }
    .product-danger {
        float: right;
        color: #fff;
        background-color: #dc3545;
        border-color: #dc3545;
    }
    .productdisabled
    {
        pointer-events: none;
        /* opacity: 0.7; */
    }
</style>
