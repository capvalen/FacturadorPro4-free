@extends('ecommerce::layouts.layout_ecommerce_cart.index')
@section('content')

<div id="app">
    <ul class="checkout-progress-bar">
        <li :class="{active : myform.shipping}">
            <span>Shipping</span>
        </li>
        <li :class="{active : myform.review}">
            <span>Review &amp; Payments</span>
        </li>
    </ul>
    <div class="row">
        <div class="col-lg-8">
            <ul class="checkout-steps">
                <li>
                    <h2 class="step-title">Shipping Address</h2>
                    @guest
                <li><a href="{{route('tenant_ecommerce_login')}}" class="btn btn-primary login-link">LOG IN</a></li>
                @else
                <form action="#">
                    <div class="form-group required-field">
                        <label>First Name </label>
                        <input type="text" class="form-control" required>
                    </div><!-- End .form-group -->

                    <div class="form-group required-field">
                        <label>Last Name </label>
                        <input type="text" class="form-control" required>
                    </div><!-- End .form-group -->

                    <div class="form-group">
                        <label>Company </label>
                        <input type="text" class="form-control">
                    </div><!-- End .form-group -->

                    <div class="form-group required-field">
                        <label>Street Address </label>
                        <input type="text" class="form-control" required>
                        <input type="text" class="form-control" required>
                    </div><!-- End .form-group -->

                    <div class="form-group required-field">
                        <label>City </label>
                        <input type="text" class="form-control" required>
                    </div><!-- End .form-group -->

                    <div class="form-group">
                        <label>State/Province</label>
                        <div class="select-custom">
                            <select class="form-control">
                                <option value="CA">California</option>
                                <option value="TX">Texas</option>
                            </select>
                        </div><!-- End .select-custom -->
                    </div><!-- End .form-group -->

                    <div class="form-group required-field">
                        <label>Zip/Postal Code </label>
                        <input type="text" class="form-control" required>
                    </div><!-- End .form-group -->

                    <div class="form-group">
                        <label>Country</label>
                        <div class="select-custom">
                            <select class="form-control">
                                <option value="USA">United States</option>
                                <option value="Turkey">Turkey</option>
                                <option value="China">China</option>
                                <option value="Germany">Germany</option>
                            </select>
                        </div><!-- End .select-custom -->
                    </div><!-- End .form-group -->

                    <div class="form-group required-field">
                        <label>Phone Number </label>
                        <div class="form-control-tooltip">
                            <input type="tel" class="form-control" required>
                            <span class="input-tooltip" data-toggle="tooltip" title="For delivery questions."
                                data-placement="right"><i class="icon-question-circle"></i></span>
                        </div><!-- End .form-control-tooltip -->
                    </div><!-- End .form-group -->
                </form>
                </li>

                <li>
                    <div class="checkout-step-shipping">
                        <h2 class="step-title">Shipping Methods</h2>

                        <table class="table table-step-shipping">
                            <tbody>
                                <tr>
                                    <td><input type="radio" name="shipping-method" value="flat"></td>
                                    <td><strong>$20.00</strong></td>
                                    <td>Fixed</td>
                                    <td>Flat Rate</td>
                                </tr>

                                <tr>
                                    <td><input type="radio" name="shipping-method" value="best"></td>
                                    <td><strong>$15.00</strong></td>
                                    <td>Table Rate</td>
                                    <td>Best Way</td>
                                </tr>
                            </tbody>
                        </table>
                    </div><!-- End .checkout-step-shipping -->
                </li>
                @endguest
            </ul>
        </div><!-- End .col-lg-8 -->


        <div class="col-lg-4">
            <div class="order-summary">
                <h3>Summary</h3>

                <h4>
                    <a data-toggle="collapse" href="#order-cart-section" class="collapsed" role="button"
                        aria-expanded="false" aria-controls="order-cart-section">2 products in Cart</a>
                </h4>

                <div class="collapse" id="order-cart-section">
                    <table class="table table-mini-cart">
                        <tbody>
                            <tr v-for="(row, index) in records">
                                <td class="product-col">
                                    <figure class="product-image-container">
                                        <a href="#" class="product-image">
                                            <img :src=" '/storage/uploads/items/' + row.image" alt="product">
                                        </a>
                                    </figure>
                                    <div>
                                        <h2 class="product-title">
                                            <a href="#">@{{ row.name }}</a>
                                        </h2>

                                        <span class="product-qty">Cantidad: 1</span>
                                    </div>
                                </td>
                                <td class="price-col">S/ @{{ row.sale_unit_price }}</td>
                            </tr>


                        </tbody>
                    </table>
                </div><!-- End #order-cart-section -->
            </div><!-- End .order-summary -->
        </div><!-- End .col-lg-4 -->
    </div><!-- End .row -->
    @guest
    @else
    <div class="row">
        <div class="col-lg-8">
            <div class="checkout-steps-action">
                <button @click="next" class="btn btn-primary float-right">NEXT</button>
            </div><!-- End .checkout-steps-action -->
        </div><!-- End .col-lg-8 -->
    </div><!-- End .row -->

    @endguest

</div>



@endsection


@push('scripts')
<script type="text/javascript">
    function check(event) {
        console.log(event)
    }
    var app = new Vue({
        el: '#app',
        data: {
            records: [],
            message: 'Hello Vue!',
            summary: {
                subtotal: '0.0',
                tax: '0.0',
                total: '0.0'
            },
            myform: {
                shipping: true,
                review: false
            }
        },
        mounted() {
            // let contex = this
            /*$(".input_quantity").change(function (e) {
                 let value = parseFloat($(this).val())
                 let id = $(this).data('product')
                 let row = contex.records.find(x => x.id == id)
                 row.sub_total = (parseFloat(row.sale_unit_price) * value).toFixed(2)
                 row.cantidad = value

                 contex.calculateSummary()
             });*/



            //this.calculateSummary()


        },
        created() {
            let array = localStorage.getItem('products_cart');
            array = JSON.parse(array)
            if (array) {
                this.records = array
            }

        },
        methods: {
            next()
            {
                this.myform.shipping = false
                this.myform.review = true
            }
            /*clearShoppingCart() {
                this.records = []
                localStorage.setItem('products_cart', JSON.stringify([]))
            },
            calculateSummary() {
                let subtotal = 0.00
                this.records.forEach(function (item) {
                    //console.log(item)
                    subtotal += parseFloat(item.sub_total)
                })

                this.summary.subtotal = subtotal.toFixed(2)
                let tax = (subtotal * 0.18)
                this.summary.tax = tax.toFixed(2)
                this.summary.total = (subtotal + tax).toFixed(2)



            }*/
        }
    })

</script>
@endpush
