<template>
  <div class="dropdown-cart-products">
    <div class="product">
      <div class="product-details">
        <h4 class="product-title">
          <a href="product.html">Woman Ring</a>
        </h4>

        <span class="cart-product-info">
          <span class="cart-product-qty">1</span>
          x $99.00
        </span>
      </div>
      <figure class="product-image-container">
        <a href="product.html" class="product-image">
          <img alt="product" />
        </a>
        <a href="#" class="btn-remove" title="Remove Product">
          <i class="icon-cancel"></i>
        </a>
      </figure>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      records: []
    };
  },
  created() {
    //this.getData()
    console.log("cargado");
  },
  methods: {
    //getData() {}
  }
};
</script>
