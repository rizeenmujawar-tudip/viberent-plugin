<?php
$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$slugName = basename(parse_url($url, PHP_URL_PATH));
$resuli = $wpdb->get_results("SELECT * from wp_viberent_pagename");
if (!empty($resuli)) {
    $mypagetitle = $resuli[0]->pagename;
    $mypagename = sanitize_title($mypagetitle);
}
if ($mypagetitle) {
    $pageTitle =  $mypagetitle;
} elseif ($slugName == "my-cart") {
    $pageTitle =  "My cart";
} elseif ($slugName == "place-my-order") {
    $pageTitle =  "Place my order";
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo $pageTitle; ?></title>
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
    <?php //wp_head(); ?>
    <link rel="profile" href="http://gmpg.org/xfn/11" />
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
    <link rel="stylesheet" href="<?php echo plugins_url(); ?>/viberent/assets/css/all.css">
    <link rel="stylesheet" href="<?php echo plugins_url(); ?>/viberent/assets/css/custom.css">
    <link rel="stylesheet" href="<?php echo plugins_url(); ?>/viberent/assets/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="<?php echo plugins_url(); ?>/viberent/assets/js/jquery.js"></script>
    <script src="<?php echo plugins_url(); ?>/viberent/assets/js/moment.min.js"></script>
    <script src="<?php echo plugins_url(); ?>/viberent/assets/js/bootstrap.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script>
        jQuery('document').ready(function($) {
            /*spinner code start here*/
            $(window).on('load', function() {
                setTimeout(function() {
                    $('.loader').hide(300);
                }, 7000);
            });
            // FOR DEMO PURPOSE	
            $(window).on('load', function() {
                var loadingCounter = setInterval(function() {
                    var count = parseInt($('.countdown').html());
                    if (count !== 0) {
                        $('.countdown').html(count - 1);
                    } else {
                        clearInterval();
                    }
                }, 1000);
            });
            /*end here*/
            $('#end-date').change(function() {
                $('#my-dates').click();
            });
            $("select#period").change(function() {
                var selectperiod = $(this).val().trim();
                localStorage.setItem('rentalPeriod', selectperiod);
            });

            var minimized_elements = $('p.minimize');
            minimized_elements.each(function() {
                var t = $(this).text();
                if (t.length < 20) return;

                $(this).html(
                    t.slice(0, 20) + '<span>... </span><a href="#" class="more">More</a>' +
                    '<span style="display:none;">' + t.slice(20, t.length) + ' <a href="#" class="less">Less</a></span>'
                );

            });

            $('.product-quantity').change(function() {
                if ($(this).val() == 0 || $(this).val() == "") {
                    $(this).val(1);
                }
            });
            $(".product-quantity[type='number']").keydown(function(e) {
                if (e.keyCode === 8) {
                    return false;
                };
                if (e.keyCode === 46) {
                    return false;
                };
            });

            $('a.more', minimized_elements).click(function(event) {
                event.preventDefault();
                $(this).hide().prev().hide();
                $(this).next().show();
            });

            $('a.less', minimized_elements).click(function(event) {
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
            $('.productQuantity').change(function() {
                if ($(this).val() == 0 || $(this).val() == "") {
                    $(this).val(1);
                }
                $(this).next('.quantity-submit').click();
            });
            $('.productQuantity').keydown(function(e) {
                if (e.keyCode === 8) {
                    return false;
                };
                if (e.keyCode === 46) {
                    return false;
                };
            });
            $("#btn_place_order").click(function() {
                $("#btn_place_order").prop('disabled', true);
            });

            $("table.tbl-cart tr .item-row").each(function(index, elem) {
                var productAvailable = $(elem).find('form .product_available').val();
                $(elem).find(".productQuantity").change(function() {
                    if (parseInt($(this).val()) > parseInt(productAvailable)) {
                        alert("Product available only " + productAvailable);
                        $(this).val(productAvailable);
                    }
                });
            });
            /*end here*/
            /*place my order js start here*/
            $('#placeOrderForm input, #placeOrderForm select').change(function() {
                if ($(this).val().length > 0) {
                    $(this).removeClass('a-form-error');
                } else {
                    $(this).addClass('a-form-error');
                }
            });

            $('#confirm_btn').click(function() {
                if (!$('input[type=text], input[type=email], select').val()) {
                    $('input[type=text], input[type=email],select').addClass('a-form-error');
                }
            });

            var ckbox = $('#diff_shippin');
            $('input#diff_shippin').on('click', function() {
                if (ckbox.is(':checked')) {
                    $('#hidden_shippin_addr input, select').attr('required', 'required');
                } else {
                    $('#hidden_shippin_addr input, select').removeAttr('required');
                }
            });
            /*end here*/

            $(".item-category-box").each(function(index, elem) {
                var productAvailable = $(elem).find('span.product_available').text();
                var priceNotavailable = $(elem).find('.price-not-available').text();
                var rentalratesvalue = $(elem).find('.rentalratesvalue').val();
                if (productAvailable == 0) {
                    $(elem).find(".add-to-cart-component  .btnAddAction").attr("disabled", true);
                }

                $(elem).find(".product-quantity").change(function() {
                    if (parseInt($(this).val()) > parseInt(productAvailable)) {
                        alert("Product available only " + productAvailable);
                        $(this).val(productAvailable);
                    }
                });
                $('select#period option[value="<?php if (isset($_POST["period"])) {
                                                    echo esc_js($_POST["period"]);
                                                } elseif (isset($_POST["rentalratesName"])) {
                                                    echo esc_js($_POST["rentalratesName"]);
                                                } else {
                                                    echo esc_js($firstRental_period);
                                                } ?>"]').attr("selected", true);
            });

            <?php if (isset($_POST["rentalratesName"])) { ?>
                $('#my-dates').click();
            <?php } ?>

            if ((localStorage.getItem('rentalPeriod'))) {
                var selectperiod = $("#period").val();
                if ((localStorage.getItem('rentalPeriod') != selectperiod)) {
                    $("select#period").val(localStorage.getItem('rentalPeriod'));
                    $('#my-dates').click();
                }
            }

        });
    </script>
</head>
