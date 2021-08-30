<div class="dropdown cart-dropdown">
    <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-display="static">
        <i class="icon-bag-2 fa-2x text-white"></i>
        <span class="cart-count">0</span>
    </a>
    <div class="dropdown-menu">
        <div class="dropdownmenu-wrapper">

            <div class="dropdown-cart-products">

            </div><!-- End .cart-product -->

            <div class="dropdown-cart-total">
                <span>Total</span>

                <span class="cart-total-price">S/ 0</span>
            </div><!-- End .dropdown-cart-total -->

            <div class="dropdown-cart-action">
                <a  href="{{ route('tenant_detail_cart') }}" class="btn">Ver Carrito</a>
                <!--<a href="#" class="btn">Checkout</a> -->
            </div><!-- End .dropdown-cart-total -->
        </div><!-- End .dropdownmenu-wrapper -->
    </div><!-- End .dropdown-menu -->
</div><!-- End .dropdown -->


@push('scripts')
<script type="text/javascript">

	function remove(id)
	{
		
		let array = localStorage.getItem('products_cart');
		array = JSON.parse(array);
		let indexFound = array.findIndex( x=> x.id == id)
		array.splice(indexFound, 1);
		localStorage.setItem('products_cart', JSON.stringify( array ) );
		populate();
		calculatetotal();
	
	}

	function calculatetotal()
	{
		let array = localStorage.getItem('products_cart');
		array = JSON.parse(array);
		let total = 0;
		array.forEach(element => {
			total += parseFloat(element.sale_unit_price)
		});

		$(".cart-total-price").empty();
		$(".cart-total-price").append(total.toFixed(2));

	}

	function populate()
	{
		$(".dropdown-cart-products").empty();
			$(".cart-count").empty();
			let count = 0;
			//get data local syrogare prodicts
			let array = localStorage.getItem('products_cart');
			array = JSON.parse(array)
			count = array.length;
				
			array.forEach(element => {
				
				$(".dropdown-cart-products").append( `
						<div class="product">
							<div class="product-details">
							<h4 class="product-title">
								<a href="$">${element.description}</a>
							</h4>
							<span class="cart-product-info">
								<span class="cart-product-qty">1</span> x ${element.sale_unit_price}
							</span>
							</div>
							<figure class="product-image-container">
								<a href="#" class="product-image">
									<img alt="product" src="/storage/uploads/items/${element.image_small}" />
								</a>
								<a href="#" onclick="remove(${element.id})" class="btn-remove" title="Remove Product">
									<i class="icon-cancel"></i>
								</a>
							</figure>
						</div>` 
					);
			});
			
			$(".cart-count").append(count);
	}

	
	$(function(){
    'use strict';
		populate();
		calculatetotal();
    });
</script>
@endpush