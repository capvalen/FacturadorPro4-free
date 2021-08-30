<div class="widget widget-featured">
            <h3 class="widget-title">Productos Destacados</h3>

            <div class="widget-body">
                <div class="owl-carousel widget-featured-products">
                    <div class="featured-col">

                        @php
                        $row3 = $items->take(3);
                        $row3inverse = $items->take(-3);

                        @endphp

                        @foreach ($row3 as $item)
                        <div class="product product-sm">
                            <figure class="product-image-container">
                                <a href="/ecommerce/item/{{ $item->id }}" class="product-image">
                                    <img src="{{ asset('storage/uploads/items/'.$item->image) }}" alt="product">
                                </a>
                            </figure>
                            <div class="product-details">
                                <h2 class="product-title">
                                    <a href="/ecommerce/item/{{ $item->id }}">{{$item->description}}</a>
                                </h2>
                                <div class="ratings-container">
                                    <div class="product-ratings">
                                        <span class="ratings" style="width:80%"></span>
                                        <!-- End .ratings -->
                                    </div><!-- End .product-ratings -->
                                </div><!-- End .product-container -->
                                <div class="price-box">
                                    <span class="product-price">{{ $item->currency_type_symbol }} {{ number_format($item->sale_unit, 2) }}</span>
                                </div><!-- End .price-box -->
                            </div><!-- End .product-details -->
                        </div><!-- End .product -->
                        @endforeach



                    </div><!-- End .featured-col -->

                    <div class="featured-col">

                        @foreach ($row3inverse as $item)
                        <div class="product product-sm">
                            <figure class="product-image-container">
                                <a href="/ecommerce/item/{{ $item->id }}" class="product-image">
                                    <img src="{{ asset('storage/uploads/items/'.$item->image) }}" alt="product">
                                </a>
                            </figure>
                            <div class="product-details">
                                <h2 class="product-title">
                                    <a href="/ecommerce/item/{{ $item->id }}">{{$item->description}}</a>
                                </h2>
                                <div class="ratings-container">
                                    <div class="product-ratings">
                                        <span class="ratings" style="width:80%"></span>
                                        <!-- End .ratings -->
                                    </div><!-- End .product-ratings -->
                                </div><!-- End .product-container -->
                                <div class="price-box">
                                    <span class="product-price">
                                        {{ $item->currency_type_symbol }} {{ number_format($item->sale_unit, 2) }}</span>
                                </div><!-- End .price-box -->
                            </div><!-- End .product-details -->
                        </div><!-- End .product -->
                        @endforeach

                    </div><!-- End .featured-col -->
                </div><!-- End .widget-featured-slider -->
            </div><!-- End .widget-body -->
        </div><!-- End .widget -->
