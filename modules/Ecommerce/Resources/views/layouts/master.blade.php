<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from portotheme.com/html/porto_ecommerce/demo-6/ by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 07 Sep 2019 03:39:38 GMT -->

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>eCommerce</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="keywords" content="ecommerce" />
    <meta name="description" content="eCommerce">
    <meta name="author" content="SW-THEMES">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('porto-ecommerce/assets/images/icons/favicon.ico') }}">

    <!-- Plugins CSS File -->
    <link rel="stylesheet" href="{{ asset('porto-ecommerce/assets/css/bootstrap.min.css') }}">

    <!-- Main CSS File -->
    <link rel="stylesheet" href="{{ asset('porto-ecommerce/assets/css/style.min.css') }}">
    <link rel="stylesheet" href="{{ asset('porto-ecommerce/assets/css/custom.css') }}">

    <link rel="stylesheet" href="{{ asset('porto-ecommerce/assets/css/rating.css') }}">

    <!-- Fontawesome -->
    <link rel="stylesheet" href="{{ asset('porto-ecommerce/assets/font-awesome/css/fontawesome-all.min.css') }}">


    @if (file_exists(public_path('theme/custom_styles_ecommerce.css')))
        <link rel="stylesheet" href="{{ asset('theme/custom_styles_ecommerce.css') }}" />
    @endif
</head>
<style>


</style>
<body>
    <div class="page-wrapper">

        @include('ecommerce::layouts.partials_ecommerce.header')
        <main class="main">
            <br> <!-- layout-  info_boxez-->

            <div class="container">
                <div class="row">
                    <div class="col-lg-9">

                    @php
                        $tagid = Request::segment(3);
                    @endphp

                        @if(!$tagid)
                            @include('ecommerce::layouts.partials_ecommerce.home_slider')
                        @endif

                        <div class="row">

                            {{--<div class="col-md-4">
                                <div class="banner banner-image">
                                    <a href="#">
                                        <img src="{{ asset('porto-ecommerce/assets/images/banners/banner-1.jpg' ) }}"
                                            alt="banner">
                                    </a>
                                </div><!-- End .banner -->
                            </div><!-- End .col-md-4 -->

                            <div class="col-md-4">
                                <div class="banner banner-image">
                                    <a href="#">
                                        <img src="{{ asset('porto-ecommerce/assets/images/banners/banner-2.jpg' ) }}"
                                            alt="banner">
                                    </a>
                                </div><!-- End .banner -->
                            </div><!-- End .col-md-4 -->

                            <div class="col-md-4">
                                <div class="banner banner-image">
                                    <a href="#">
                                        <img src="{{ asset('porto-ecommerce/assets/images/banners/banner-3.jpg' ) }}"
                                            alt="banner">
                                    </a>
                                </div><!-- End .banner -->
                            </div><!-- End .col-md-4 --> --}}
                        </div><!-- End .row -->

                        <div class="mb-3"></div><!-- margin -->

                        {{-- @include('ecommerce::layouts.partials_ecommerce.featured_products') --}}
                        <div class="row row-sm">
                            @include('ecommerce::layouts.partials_ecommerce.list_products')
                        </div>

                        <div class="row float-right">
                          <div class="col-md-12 col-lg-12">
                            {{ $dataPaginate->links() }}
                          </div>
                        </div>

                        <div class="mb-6"></div><!-- margin -->

                        <div class="row">
                            <!-- layout-  products_main-->

                        </div><!-- End .row -->

                        <div class="mb-3"></div><!-- margin -->

                        <div class="row">
                            <!-- layout-  features_box -->


                        </div><!-- End .row -->
                    </div><!-- End .col-lg-9 -->

                    <aside class="sidebar-home col-lg-3 order-lg-first">
                        <div class="side-menu-container">
                            <h2>CATEGORIAS</h2>
                            @include('ecommerce::layouts.partials_ecommerce.sidemenu')
                        </div><!-- End .side-menu-container -->

                        <!-- SlidShow Side Bar, Carousel a Editar -->

                <div class="box-carousel">
                   <div class="widget-banners-slider owl-carousel owl-theme">
                     @forelse($records as $data)
                            @if($data->apply_store === 1)

                    <figure class="product-image-container boxing">
                        <a href="/ecommerce/item/{{ $data->id }}" class="product-image">
                            <img src="{{ asset('storage/uploads/items/'.$data->image) }}" alt="product" class="image">
                        </a>
                        <a href="{{route('item_partial', ['id' => $data->id])}}" class="btn-quickview">Vista Rápida</a>
                     <span class="product-label label-hot">New Sales Recent</span>
                                <span class="product-label">{{$data->description}}</span>
                    </figure>
                            @endif
                        @empty
                            <div class="widget widget-banner">
                                <div class="banner banner-image">
                                    <a href="#">
                                        <img src="{{ asset('porto-ecommerce/assets/images/banners/banner-sidebar.jpg') }}"
                                            alt="Banner Desc">
                                    </a>
                            </div><!-- End .banner -->
                        </div>
                    @endforelse
                            <!-- End .banner -->
                 </div><!-- End .banner-slider -->
                       <!-- End .widget or  SlideShow-->
                </div>



                        <!-- <div class="widget widget-newsletters">
                           <h3 class="widget-title">Newsletter</h3>
                            <p>Get all the latest information on Events, Sales and Offers. </p>
                            <form action="#">
                                <div class="form-group">
                                    <input type="email" class="form-control" id="wemail">
                                    <label for="wemail"><i class="icon-envolope"></i>Email Address</label>
                                </div>
                                <input type="submit" class="btn btn-block" value="Subscribe Now">
                            </form>
                        </div>-->

                        {{-- <div class="widget widget-testimonials">
                            @include('ecommerce::layouts.partials_ecommerce.testimonials')
                        </div> --}}
                        <!-- End .widget -->

                        <div class="widget">
                            <!-- layout-  news -->

                        </div><!-- End .widget -->
                    </aside><!-- End .col-lg-3 -->
                </div><!-- End .row -->
            </div><!-- End .container -->

            <div class="mb-4"></div><!-- margin -->
        </main><!-- End .main -->

        <footer class="footer">
            @include('ecommerce::layouts.partials_ecommerce.footer')
        </footer><!-- End .footer -->
    </div><!-- End .page-wrapper -->

    <div class="mobile-menu-overlay"></div><!-- End .mobil-menu-overlay -->

    <div class="mobile-menu-container">

        @include('ecommerce::layouts.partials_ecommerce.mobile_menu')

    </div><!-- End .mobile-menu-container -->

    <div class="newsletter-popup mfp-hide" id="newsletter-popup-form">
        <!-- style="background-image: url(assets/images/newsletter_popup_bg.jpg)" -->
        <div class="newsletter-popup-content">
            <img src="{{ asset('porto-ecommerce/assets/images/logo-black.png') }}" alt="Logo" class="logo-newsletter">
            <h2>BE THE FIRST TO KNOW</h2>
            <p>Subscribe to the Porto eCommerce newsletter to receive timely updates from your favorite products.</p>
            <form action="#">
                <div class="input-group">
                    <input type="email" class="form-control" id="newsletter-email" name="newsletter-email"
                        placeholder="Email address" required>
                    <input type="submit" class="btn" value="Go!">
                </div><!-- End .from-group -->
            </form>
            <div class="newsletter-subscribe">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="1">
                        Don't show this popup again
                    </label>
                </div>
            </div>
        </div><!-- End .newsletter-popup-content -->
    </div><!-- End .newsletter-popup -->

    <a id="scroll-top" href="#top" title="Top" role="button"><i class="icon-angle-up"></i></a>

    <!-- Plugins JS File -->
    <script src="{{ asset('porto-ecommerce/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('porto-ecommerce/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('porto-ecommerce/assets/js/plugins.min.js') }}"></script>

    <!-- Main JS File -->
    <script src="{{ asset('porto-ecommerce/assets/js/main.min.js') }}"></script>

    <script src="{{ asset('porto-ecommerce/assets/js/vue.min.js') }}"></script>



    @stack('scripts')
</body>

<!-- Mirrored from portotheme.com/html/porto_ecommerce/demo-6/ by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 07 Sep 2019 03:39:54 GMT -->

</html>

