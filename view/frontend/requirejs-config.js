var config = {
  map: {
    "*": {
      "Magento_Checkout/template/summary.html": "Arun_FreeShippingBar/template/summary.html"
    }
  },
  config: {
    mixins: {
      "Magento_Catalog/js/catalog-add-to-cart": {
        "Arun_FreeShippingBar/js/add-to-cart-mixin": true
      },
      "Magento_Checkout/js/view/minicart": {
        "Arun_FreeShippingBar/js/minicart-mixin": true
      }
    }
  }
};
