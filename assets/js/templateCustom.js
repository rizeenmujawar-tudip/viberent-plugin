 jQuery('document').ready(function ($) {
     /*spinner code start here*/
    setTimeout(function () {
        $('.loading-spinner .loader').hide(300);
    }, 3000);
     // FOR DEMO PURPOSE    
    var loadingCounter = setInterval(function () {
        var count = parseInt($('.loading-spinner .countdown').html());
        if (count !== 0) {
            $('.loading-spinner .countdown').html(count - 1);
        } else {
            clearInterval();
        }
    }, 1000);
     /*end here*/
    $('#end-date').change(function () {
         $('#my-dates').click();
         var endDate = $(this).val();
         localStorage.setItem('endDate', endDate);
     });
     $("select#period").change(function () {
         var selectperiod = $(this).val().trim();
         localStorage.setItem('rentalPeriod', selectperiod);
     });

     var minimized_elements = $('p.minimize');
     minimized_elements.each(function () {
         var t = $(this).text();
         if (t.length < 20) return;

         $(this).html(
             t.slice(0, 20) + '<span>... </span><a href="#" class="more">More</a>' +
             '<span style="display:none;">' + t.slice(20, t.length) + ' <a href="#" class="less">Less</a></span>'
         );

     });

     $('.product-quantity').change(function () {
         if ($(this).val() == 0 || $(this).val() == "") {
             $(this).val(1);
         }
     });
     $(".product-quantity[type='number']").keydown(function (e) {
         if (e.keyCode === 8) {
             return false;
         };
         if (e.keyCode === 46) {
             return false;
         };
     });

     $('a.more', minimized_elements).click(function (event) {
         event.preventDefault();
         $(this).hide().prev().hide();
         $(this).next().show();
     });

     $('a.less', minimized_elements).click(function (event) {
         event.preventDefault();
         $(this).parent().hide().prev().show().prev().show();
     });

     /* my cart js start here*/
     var totalQuantity = $("#totalQuantity").val();
     if (totalQuantity > 0) {
         $(".btn_mycart").find("span.has-badge").attr('data-count', totalQuantity);
     } else {
         $(".btn_mycart").find("span.has-badge").attr('data-count', '0');
     }
     $('.productQuantity').change(function () {
         if ($(this).val() == 0 || $(this).val() == "") {
             $(this).val(1);
         }
         $(this).next('.quantity-submit').click();
     });
     $('.productQuantity').keydown(function (e) {
         if (e.keyCode === 8) {
             return false;
         };
         if (e.keyCode === 46) {
             return false;
         };
     });
     $("#btn_place_order").click(function () {
         $("#btn_place_order").prop('disabled', true);
     });

     $("table.tbl-cart tr .item-row").each(function (index, elem) {
         var productAvailable = $(elem).find('form .product_available').val();
         $(elem).find(".productQuantity").change(function () {
             if (parseInt($(this).val()) > parseInt(productAvailable)) {
                 alert("Product available only " + productAvailable);
                 $(this).val(productAvailable);
             }
         });
     });
     /*end here*/
     /*place my order js start here*/
     $('#placeOrderForm input, #placeOrderForm select').change(function () {
         if ($(this).val().length > 0) {
             $(this).removeClass('a-form-error');
         } else {
             $(this).addClass('a-form-error');
         }
     });

     $('#confirm_btn').click(function () {
         if (!$('input[type=text], input[type=email], select').val()) {
             $('input[type=text], input[type=email],select').addClass('a-form-error');
         }
     });

     var ckbox = $('#diff_shippin');
     $('input#diff_shippin').on('click', function () {
         if (ckbox.is(':checked')) {
             $('#hidden_shippin_addr input, select').attr('required', 'required');
         } else {
             $('#hidden_shippin_addr input, select').removeAttr('required');
         }
     });
     /*end here*/

     $(".item-category-box").each(function (index, elem) {
         var productAvailable = $(elem).find('span.product_available').text();
         var priceNotavailable = $(elem).find('.price-not-available').text();
         var rentalratesvalue = $(elem).find('.rentalratesvalue').val();
         if (productAvailable == 0) {
             $(elem).find(".add-to-cart-component  .btnAddAction").attr("disabled", true);
         }

         $(elem).find(".product-quantity").change(function () {
             if (parseInt($(this).val()) > parseInt(productAvailable)) {
                 alert("Product available only " + productAvailable);
                 $(this).val(productAvailable);
             }
         });
     });

     if ((localStorage.getItem('rentalPeriod'))) {
         var selectperiod = $("#period").val();
         if ((localStorage.getItem('rentalPeriod') != selectperiod)) {
             $("select#period").val(localStorage.getItem('rentalPeriod'));
             $('#my-dates').click();
         }
     }

 });

  // My cart css start here
  function confirmAction(itemcode) {
      var confirmAction = confirm("Are you sure to delete the item?");
      if (confirmAction) {
          var url = window.location.pathname + "/?action=remove&sessionID=" + itemcode;
          window.location = url;
          confirmReload();
      } else {
          alert("Action canceled");
      }
  }

  function confirmAll() {
      var confirmAction = confirm("Are you sure to delete all the items?");
      if (confirmAction) {
          var url = window.location.pathname + "/?action=empty";
          window.location = url;
          confirmReload();
      } else {
          alert("Action canceled");
      }
  }

  window.setTimeout(function confirmReload() {
      var field = 'action';
      var url = window.location.href;
      if (url.indexOf('?' + field + '=') != -1)
          window.location = window.location.pathname;
      return true;
  }, 3000);