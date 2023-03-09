define("Arun_FreeShippingBar/js/add-to-cart-mixin", ["jquery"], function ($) {
  "use strict";

  return function () {
    $(document).on("click", ".tocart", function () {
      // Making the AJAX request to update the free shipping bar when the product is add to cart
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
        error: function (_xhr, _status, error) {
          console.log(error.message);
        }
      });
    });
  };
});
