@extends('tenant.layouts.layout_ecommerce_item.record')

@section('content')

<div class="product-single-container product-single-default">
    <div class="row">
        <div class="col-lg-7 col-md-6 product-single-gallery">
            <div class="product-slider-container product-item">
                <div class="product-single-carousel owl-carousel owl-theme">
                    <div class="product-item">
                       
                    </div>
                    <!--<div class="product-item">
                        <img class="product-single-image" src="assets/images/products/zoom/product-2.jpg"
                            data-zoom-image="assets/images/products/zoom/product-2-big.jpg" />
                    </div>
                    <div class="product-item">
                        <img class="product-single-image" src="assets/images/products/zoom/product-3.jpg"
                            data-zoom-image="assets/images/products/zoom/product-3-big.jpg" />
                    </div>
                    <div class="product-item">
                        <img class="product-single-image" src="assets/images/products/zoom/product-4.jpg"
                            data-zoom-image="assets/images/products/zoom/product-4-big.jpg" />
                    </div>-->
                </div>
                <!-- End .product-single-carousel -->
                <span class="prod-full-screen">
                    <i class="icon-plus"></i>
                </span>
            </div>

        </div><!-- End .col-lg-7 -->

        <div class="col-lg-5 col-md-6">
            <div class="product-single-details">
                <h1 class="product-title">{{$record->description}}</h1>

                <div class="ratings-container">
                    <div class="product-ratings">
                        <span class="ratings" style="width:60%"></span><!-- End .ratings -->
                    </div><!-- End .product-ratings -->

                    <a href="#" class="rating-link">( 6 vistas )</a>
                </div><!-- End .product-container -->

                <div class="price-box">
                    <span class="old-price">S/ {{ number_format( ($record->sale_unit_price * 1.2 ) , 2 )}}</span>
                    <span class="product-price">S/ {{ number_format($record->sale_unit_price, 2 )}}</span>
                </div><!-- End .price-box -->

                <div class="product-desc">
                    <p>{{$record->name}}</p>
                </div><!-- End .product-desc -->

                <div class="product-filters-container">

                </div><!-- End .product-filters-container -->

                <div class="product-action product-all-icons">
                    <!--<div class="product-single-qty">
                        <input class="horizontal-quantity form-control" type="text">
                    </div>-->
                    <!-- End .product-single-qty -->

                   
                    <!-- <a href="#" class="paction add-wishlist" title="Add to Wishlist">
                        <span>Add to Wishlist</span>
                    </a>
                    <a href="#" class="paction add-compare" title="Add to Compare">
                        <span>Add to Compare</span>
                    </a> -->
                </div><!-- End .product-action -->

                <div class="product-single-share">
                    <!--<label>Share:</label> -->
                    <!-- www.addthis.com share plugin-->
                    <div class="addthis_inline_share_toolbox"></div>
                </div><!-- End .product single-share -->
            </div><!-- End .product-single-details -->
        </div><!-- End .col-lg-5 -->
    </div><!-- End .row -->
</div><!-- End .product-single-container -->

<div class="product-single-tabs">
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active"  id="product-tab-desc" data-toggle="tab" href="#product-desc-content" role="tab"
                aria-controls="product-desc-content" aria-selected="true">Descripcion</a>
        </li>
        {{-- <li class="nav-item">
            <a class="nav-link" id="product-tab-tags" data-toggle="tab" href="#product-tags-content" role="tab"
                aria-controls="product-tags-content" aria-selected="false">Tags</a>
        </li> --}}
        <li class="nav-item">
            <a class="nav-link" onclick="getRating('{{ $record->id}}')" id="product-tab-reviews" data-toggle="tab" href="#product-reviews-content" role="tab"
                aria-controls="product-reviews-content" aria-selected="false">Reviews</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade show active" id="product-desc-content" role="tabpanel"
            aria-labelledby="product-tab-desc">
            <div class="product-desc-content">
                <p> {{ $record->name}} </p>
                <p> {{ $record->description}} </p>
                {{-- <ul>
                    <li><i class="icon-ok"></i>Any Product types that You want - Simple,
                        Configurable</li>
                    <li><i class="icon-ok"></i>Downloadable/Digital Products, Virtual Products
                    </li>
                    <li><i class="icon-ok"></i>Inventory Management with Backordered items</li>
                </ul>
                <p>Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad
                    minim veniam, <br>quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. </p> --}}
            </div><!-- End .product-desc-content -->
        </div><!-- End .tab-pane -->

        {{-- <div class="tab-pane fade" id="product-tags-content" role="tabpanel" aria-labelledby="product-tab-tags">
            <div class="product-tags-content">
                <form action="#">
                    <h4>Add Your Tags:</h4>
                    <div class="form-group">
                        <input type="text" class="form-control form-control-sm" required>
                        <input type="submit" class="btn btn-primary" value="Add Tags">
                    </div><!-- End .form-group -->
                </form>
                <p class="note">Use spaces to separate tags. Use single quotes (') for phrases.
                </p>
            </div><!-- End .product-tags-content -->
        </div><!-- End .tab-pane --> --}}

        <div class="tab-pane fade" id="product-reviews-content" role="tabpanel" aria-labelledby="product-tab-reviews">
            <div class="product-reviews-content">
                <div class="collateral-box">

                    <div class="page">
                        <div class="page__demo">

                            <div class="page__group">
                                <div class="rating">
                                    <input type="radio" name="rating-star2" class="rating__control" id="rc6" onclick="sendRating(1,{{$record->id}})">
                                    <input type="radio" name="rating-star2" class="rating__control" id="rc7" onclick="sendRating(2,{{$record->id}})">
                                    <input type="radio" name="rating-star2" class="rating__control" id="rc8" onclick="sendRating(3,{{$record->id}})">
                                    <input type="radio" name="rating-star2" class="rating__control" id="rc9" onclick="sendRating(4,{{$record->id}})">
                                    <input type="radio" name="rating-star2" class="rating__control" id="rc10" onclick="sendRating(5,{{$record->id}})" >
                                    <label for="rc6" class="rating__item">
                                        <svg class="rating__star">
                                            <use xlink:href="#star"></use>
                                        </svg>
                                        <span class="rating__label">1</span>
                                    </label>
                                    <label for="rc7" class="rating__item">
                                        <svg class="rating__star">
                                            <use xlink:href="#star"></use>
                                        </svg>
                                        <span class="rating__label">2</span>
                                    </label>
                                    <label for="rc8" class="rating__item">
                                        <svg class="rating__star">
                                            <use xlink:href="#star"></use>
                                        </svg>
                                        <span class="rating__label">3</span>
                                    </label>
                                    <label for="rc9" class="rating__item">
                                        <svg class="rating__star">
                                            <use xlink:href="#star"></use>
                                        </svg>
                                        <span class="rating__label">4</span>
                                    </label>
                                    <label for="rc10" class="rating__item">
                                        <svg class="rating__star">
                                            <use xlink:href="#star"></use>
                                        </svg>
                                        <span class="rating__label">5</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" style="display: none">
                        <symbol id="star" viewBox="0 0 26 28">
                            <path
                                d="M26 10.109c0 .281-.203.547-.406.75l-5.672 5.531 1.344 7.812c.016.109.016.203.016.313 0 .406-.187.781-.641.781a1.27 1.27 0 0 1-.625-.187L13 21.422l-7.016 3.687c-.203.109-.406.187-.625.187-.453 0-.656-.375-.656-.781 0-.109.016-.203.031-.313l1.344-7.812L.39 10.859c-.187-.203-.391-.469-.391-.75 0-.469.484-.656.875-.719l7.844-1.141 3.516-7.109c.141-.297.406-.641.766-.641s.625.344.766.641l3.516 7.109 7.844 1.141c.375.063.875.25.875.719z" />
                        </symbol>
                    </svg>

                </div>

            </div>
        </div>
    </div>
</div>

@endsection
