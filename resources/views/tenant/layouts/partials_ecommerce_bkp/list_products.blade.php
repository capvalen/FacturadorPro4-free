@php
    $tagid = request()->query('tagid');
@endphp

@foreach ($items as $item)

    @if($tagid)

        @if(in_array($tagid, $item->tags))
            <div class="col-6 col-md-4">
                <div class="product">
                    <figure class="product-image-container">
                        <a href="/ecommerce/item/{{ $item->id }}" class="product-image">
                            <img src="{{ asset('storage/uploads/items/'.$item->image) }}" alt="product">
                        </a>
                        <a href="{{route('item_partial', ['id' => $item->id])}}" class="btn-quickview">Vista Rápida</a>
                        <span class="product-label label-sale">-20%</span>
                        <span class="product-label label-hot">New</span>
                    </figure>
                    <div class="product-details">
                        <div class="ratings-container">
                            <div class="product-ratings">
                                <span class="ratings" style="width:0%"></span>
                            </div>
                        </div>
                        <h2 class="product-title">
                            <a href="product.html">{{$item->name}}</a>
                        </h2>
                        <div class="price-box">
                            <span class="old-price">S/ {{ number_format( ($item->sale_unit_price * 1.2 ) , 2 )}}</span>
                            <span class="product-price">S/ {{ number_format($item->sale_unit_price, 2) }}</span>
                        </div>
                        <div class="product-action">
                            <a href="#" class="paction add-cart" data-product="{{ json_encode( $item ) }}" title="Add to Cart">
                                <span>Agregar a Carrito</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        @endif



    @else
            <div class="col-6 col-md-4">
                <div class="product">
                    <figure class="product-image-container">
                        <a href="/ecommerce/item/{{ $item->id }}" class="product-image">
                            <img src="{{ asset('storage/uploads/items/'.$item->image) }}" alt="product">
                        </a>
                        <a href="{{route('item_partial', ['id' => $item->id])}}" class="btn-quickview">Vista Rápida</a>
                        <span class="product-label label-sale">-20%</span>
                        <span class="product-label label-hot">New</span>
                    </figure>
                    <div class="product-details">
                        <div class="ratings-container">
                            <div class="product-ratings">
                                <span class="ratings" style="width:0%"></span>
                            </div>
                        </div>
                        <h2 class="product-title">
                            <a href="product.html">{{$item->name}}</a>
                        </h2>
                        <div class="price-box">
                            <span class="old-price">S/ {{ number_format( ($item->sale_unit_price * 1.2 ) , 2 )}}</span>
                            <span class="product-price">S/ {{ number_format($item->sale_unit_price, 2) }}</span>
                        </div>
                        <div class="product-action">
                            <a href="#" class="paction add-cart" data-product="{{ json_encode( $item ) }}" title="Add to Cart">
                                <span>Agregar a Carrito</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
    @endif




@endforeach
