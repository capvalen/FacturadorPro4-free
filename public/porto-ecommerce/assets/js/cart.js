
 function cart_add(data) {

    try {

        let array = localStorage.getItem('products_cart');
        array = JSON.parse(array);

        let item = JSON.parse(data)
        let found = array.find(x => x.id == item.id)

        if (!found) {
            array.push(item);
            localStorage.setItem('products_cart', JSON.stringify(array));
            productsCartDropDown();

            jQuery('#moda-succes-add-product').modal('show');

            calculateTotalCart();

            $('#product_added').html(`
                            <h1 class="product-title">${item.description}</h1>
                            <div class="price-box">
                                <span class="product-price">S/ ${ Number(item.sale_unit_price).toFixed(2) }</span>
                            </div>
                            <div class="product-desc">
                                <p>${item.name}</p>
                            </div>	`);

            $('#product_added_image').html(`<img src="/storage/uploads/items/${item.image_medium}" class="img" alt="product">`)
        }
        else {
            jQuery('#modal-already-product').modal('show');
        }

    } catch ({error}) {
        console.log(error)
    }


}

function productsCartDropDown()
{

	jQuery(".dropdown-cart-products").empty();
	jQuery(".cart-count").empty();
	let count = 0;
	//get data local syrogare prodicts
	let array = localStorage.getItem('products_cart');
	array = JSON.parse(array)
	count = array.length;

	array.forEach(element => {

		jQuery(".dropdown-cart-products").append( `
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

    jQuery(".cart-count").append(count);

}


function calculateTotalCart()
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

function logout()
{
	$.ajax({
		url: "/ecommerce/logout",
		method: 'get',
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		success: function (data) {
			location.reload()
		},
		error: function (error_data) {

		}
	});
}
