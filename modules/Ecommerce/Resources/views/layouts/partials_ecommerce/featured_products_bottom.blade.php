@php

$path = explode('/', request()->path());
$item_id = (isset($path[2])) ? $path[2] : 0;

@endphp
<div class="container">
    <h2 class="carousel-title">Productos Relacionados</h2>

    <div class="featured-products owl-carousel owl-theme owl-dots-top">

        @foreach ($items as $item)
            @inject('intersectTag', 'App\Services\TagsIntersect')

                @if( $intersectTag->intersect($item->tags, $item_id) )
                    <div class="product">
                        <figure class="product-image-container">
                            <a href="/ecommerce/item/{{ $item->id }}" class="product-image">
                                <img src="{{ asset('storage/uploads/items/'.$item->image) }}" alt="product">
                            </a>
                            <a href="{{route('item_partial', ['id' => $item->id])}}" class="btn-quickview">Vista Rápida</a>
                        </figure>
                        <div class="product-details">
                            <div class="ratings-container">
                                <div class="product-ratings">
                                    <span class="ratings" style="width:80%"></span><!-- End .ratings -->
                                </div><!-- End .product-ratings -->
                            </div><!-- End .product-container -->
                            <h2 class="product-title">
                                <a href="/ecommerce/item/{{ $item->id }}">{{$item->description}}</a>
                            </h2>
                            <div class="price-box">
                                <span class="product-price">{{ $item->currency_type_symbol }} {{ number_format($item->sale_unit, 2) }}</span>
                            </div><!-- End .price-box -->

                            <div class="product-action">
                                <!--<a href="#" class="paction add-wishlist" title="Add to Wishlist">
                                            <span>Add to Wishlist</span>
                                        </a>-->

                                <a href="#" class="paction add-cart" data-product="{{ json_encode( $item ) }}" title="Add to Cart">
                                    <span>Agregar a Carrito</span>
                                </a>

                                <!--<a href="#" class="paction add-compare" title="Add to Compare">
                                            <span>Add to Compare</span>
                                        </a>-->
                            </div><!-- End .product-action -->
                        </div><!-- End .product-details -->
                    </div>

                @endif

        @endforeach

    </div>
</div>
