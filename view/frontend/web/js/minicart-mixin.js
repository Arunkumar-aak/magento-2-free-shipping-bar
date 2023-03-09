define("Arun_FreeShippingBar/js/minicart-mixin", ["jquery"], function ($) {
  "use strict";

  return function (Component) {
    return Component.extend({
      /**
       * @override
       */
      update: function () {
        this._super();
        // Making the AJAX request to update the free shipping bar when the product is updated in mini-cart
        var url = "freeshippingbar/ajax/index";
        $.ajax({
          url: url,
          type: "POST",
          dataType: "json",
          success: function (response) {
            var success = response.success;

            if (success) {
              // block-container is the id of id showing the free shipping message
              $("#block-container").html(response.html);
            } else {
              console.log(response.message);
            }
          },
          error: function (xhr, status, error) {
            console.log(error.message);
          }
        });
      }
    });
  };
});
