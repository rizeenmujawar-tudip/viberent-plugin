<?php
session_start();
/*
Template name: Viberent my-cart
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>My Cart</title>
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
    <link rel="profile" href="http://gmpg.org/xfn/11" />
    <link rel="stylesheet" href="<?php echo plugins_url(); ?>/viberent/assets/css/all.css">
    <link rel="stylesheet" href="<?php echo plugins_url(); ?>/viberent/assets/css/custom.css">
    <link rel="stylesheet" href="<?php echo plugins_url(); ?>/viberent/assets/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo plugins_url(); ?>/viberent/assets/css/category.css" type="text/css" media="screen" />
    <script src="<?php echo plugins_url(); ?>/viberent/assets/js/jquery.js"></script>
    <script src="<?php echo plugins_url(); ?>/viberent/assets/js/bootstrap.js"></script>

    <script>
        jQuery(document).ready(function() {
            /*spinner code start here*/
            $(window).on('load', function() {
                setTimeout(function() {
                    $('.loader').hide(300);
                }, 2500);
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

        });

        // The function below will start the confirmation dialog
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
    </script>
</head>
<?php
$query = $_GET;
$query_result = http_build_query($query);
if (isset($_GET['pageno'])) {
    $page_no_cat = sanitize_text_field($_GET['pageno']);
} else {
    $page_no_cat = 1;
}
global $wpdb;

$pID = isset($_POST['pID']) ? $_POST['pID'] : "";
$quan = isset($_POST['quan']) ? $_POST['quan'] : "";
$rental_period = isset($_POST['rental_period']) ? sanitize_text_field($_POST['rental_period']) : "";
$session_ID = isset($_POST['sessionID']) ? sanitize_text_field($_POST['sessionID']) : "";

$productByCode = $wpdb->get_results("SELECT * FROM wp_tbl_product WHERE sessionID='" . $session_ID . "'");
if ((!empty($_SESSION["cart_item"])) && isset($_POST['sessionID'])) {
    foreach ($_SESSION["cart_item"] as $k => $v) {
        if ($productByCode[0]->sessionID == $k) {
            if (empty($_SESSION["cart_item"][$k]["quantity"])) {
                $_SESSION["cart_item"][$k]["quantity"] = 0;
            }
            if ($quan <= $v["productAvailble"]) {
                $_SESSION["cart_item"][$k]["quantity"] = $quan;
            }
        }
    }
}
if (!empty($_GET["action"])) {
    switch ($_GET["action"]) {
        case "remove":
            if (!empty($_SESSION["cart_item"])) {
                foreach ($_SESSION["cart_item"] as $k => $v) {
                    if ($_GET["sessionID"] == $k)
                        unset($_SESSION["cart_item"][$k]);
                    $delete_id = trim($_GET["sessionID"]);
                    $wpdb->delete('wp_tbl_product', array('sessionID' => $delete_id));
                    if (empty($_SESSION["cart_item"]))
                        unset($_SESSION["cart_item"]);
                }
            }
            break;
        case "empty":
            unset($_SESSION["cart_item"]);
            $wpdb->query("TRUNCATE TABLE `wp_tbl_product`");
            break;
    }
}

$result = $wpdb->get_results("SELECT * from wp_viberent_clients_company_info");
$logo_result = sanitize_text_field($result[0]->logo);
$logo = isset($logo_result) ?  $logo_result : "Logo";
?>

<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">
    <link rel="stylesheet" href="<?php echo plugins_url(); ?>/viberent/assets/css/my_cart.css" type="text/css" media="screen" />
    <nav class="navbar navbar-light bg-light sticky-top justify-content-between px-5 py-0">
        <a class="navbar-brand p-0" href="#"><?php echo "<img class='logo-image' src='data:image/jpeg;base64, $logo' />"; ?></a>
        <div id="empty_cart">
            <a id="btnEmpty" href="#" onclick="confirmAll()">Empty Cart</a>
        </div>
    </nav>


    <div class="cart_page px-5 pt-3">

        <div id="shopping-cart">


            <?php
            if (isset($_SESSION["cart_item"])) {
                $total_quantity = 0;
                $total_price = 0;
                $cart_count = 0;
                $resuli = $wpdb->get_results("SELECT * from wp_viberent_pagename");
                $slug_name = sanitize_title($resuli[0]->pagename);

            ?>
                <a href="<?php echo site_url() . "/" . $slug_name; ?>">
                    <i class="back_button fas fa-arrow-circle-left fa-stack-2x text-left pl-2"></i>
                </a>

                <h4 class="text-center my_cart_heading">My Cart
                    <a id="btn_mycart" class="btn_mycart pt-1">
                        (<span class="has-badge" data-count="0"></span>)
                    </a>
                </h4>

                <table class="tbl-cart" cellpadding="10" cellspacing="1">
                    <tbody>
                        <tr>
                            <th style="text-align:left;background-color: #4d96d5;" width="20%">Name</th>
                            <th style="text-align:left;background-color: #4d96d5;" width="10%">Rental Period</th>
                            <th style="text-align:left;background-color: #4d96d5;" width="10%">Start Date</th>
                            <th style="text-align:left;background-color: #4d96d5;" width="10%">End Date</th>
                            <th style="text-align:right;background-color: #4d96d5;" width="5%">Quantity</th>
                            <th style="text-align:right;background-color: #4d96d5;" width="15%">Period Unit</th>
                            <th style="text-align:right;background-color: #4d96d5;" width="15%">Unit Price</th>
                            <th style="text-align:right;background-color: #4d96d5;" width="15%">Amount</th>
                            <th style="text-align:center;background-color: #4d96d5;" width="5%">Remove</th>
                        </tr>
                        <?php

                        $result = $wpdb->get_results("SELECT * from wp_viberent_clients_company_info");

                        $currencysymbol = sanitize_text_field($result[0]->currencysymbol);

                        $dateFormatfromAPi = sanitize_text_field($result[0]->dateFormat);

                        if ($dateFormatfromAPi == "dd/MM/yyyy") {
                            $dateFormat = "j/m/Y";
                        } else if ($dateFormatfromAPi == "MM/dd/yyyy") {
                            $dateFormat = "m/j/Y";
                        } else if ($dateFormatfromAPi == "MM-dd-yyyy") {
                            $dateFormat = "m-j-Y";
                        }

                        $companyID = sanitize_text_field($result[0]->companyID);
                        foreach ($_SESSION["cart_item"] as $key => $item) {
                            $responseperiod = wp_remote_get('https://viberent-api.azurewebsites.net/api/item/rental-periodtype?companyid=' . $companyID);

                            if (is_wp_error($responseperiod) || wp_remote_retrieve_response_code($responseperiod) != 200) {
                                return false;
                            }

                            $responsbody = wp_remote_retrieve_body($responseperiod);
                            $respperiod = json_decode($responsbody, 1);

                            foreach ($respperiod as $retrieved_period) {
                                if ($item["rental_period"] == $retrieved_period["name"]) {
                                    $curlavail = wp_remote_get('https://viberent-api.azurewebsites.net/api/Item/item-availability?itemGUID=' . $item["GUID"] . '&companyid=' . $companyID . '&fromDate=' . $item["startDate"] . '&todate=' . $item["endDate"] . '&PeriodTypeId=' . $retrieved_period["periodTypeId"] . '&locationID=' . $item["locationID"]);

                                    if (is_wp_error($curlavail) || wp_remote_retrieve_response_code($curlavail) != 200) {
                                        return false;
                                    }
                                }
                            }
                            $responseavail = wp_remote_retrieve_body($curlavail);
                            $respavail = json_decode($responseavail, 1);
                            $getcode = $item['GUID'];
                            if ($item["productAvailble"] >= $item["quantity"]) {
                                $productAvailable = $item["quantity"];
                            } else {
                                $productAvailable = $item["productAvailble"];
                            }

                            $pID = isset($_POST['pID']) ? $_POST['pID'] : $getcode;
                            $quan = isset($_POST['quan']) ? $_POST['quan'] : $productAvailable;

                            $wpdb->query($wpdb->prepare("UPDATE wp_tbl_product
                                     SET quantity= " . $quan . "
                                     WHERE code= %s", $pID));

                            if (isset($productAvailable) && isset($item["price"]) && isset($respavail[0]["periodUnits"])) {
                                $item_price = (int)$productAvailable * (float)$item["price"] * (float)$respavail[0]["periodUnits"];
                            }
                        ?>
                            <tr style="text-align:left;">
                                <td style="text-align:left;"><img src="<?php echo esc_url($item["product_image"]); ?>" class="cart-item-image" />
                                    <p class="my-auto"><?php echo $item["product_name"]; ?></p>
                                </td>
                                <td><?php echo $item["rental_period"]; ?></td>
                                <td><?php echo date($dateFormat, strtotime($item["startDate"])); ?></td>
                                <td><?php echo date($dateFormat, strtotime($item["endDate"])); ?></td>
                                <td style="text-align:right;" class="item-row">
                                    <form style="margin-bottom: -20px;" action="<?php echo site_url() . "/my-cart/"; ?>" method="post">
                                        <input type='hidden' value="<?php echo esc_attr($item['sessionID']); ?>" name='sessionID'>
                                        <input type='hidden' class="product_available" value="<?php echo esc_attr($item['productAvailble']); ?>">
                                        <input type="number" min="1" class="productQuantity" name="quan" value="<?php echo esc_attr($productAvailable); ?>">
                                        <input type="submit" class="quantity-submit" name="quantity-submit" style="visibility: hidden;">
                                    </form>
                                </td>
                                <td style="text-align:right;"><?php echo esc_html($respavail[0]["periodUnits"]); ?></td>
                                <td style="text-align:right;"><?php echo esc_html($currencysymbol . " " . $item["price"]); ?></td>
                                <td style="text-align:right;"><?php echo esc_html($currencysymbol . " " . number_format($item_price, 2)); ?></td>
                                <td style="text-align:center;"><a href='#' class="btnRemoveAction" onclick="confirmAction('<?php echo $item['sessionID']; ?>')"><img src="<?php echo plugins_url(); ?>/viberent/assets/images/icon-delete.png" alt="Remove Item" /></a></td>
                            </tr>
                        <?php
                            (int)$total_quantity += (int)$productAvailable;
                            (float)$total_price += (float)($item["price"] * (int)$productAvailable) * (float)$respavail[0]["periodUnits"];
                            $cart_count = count(array_keys($_SESSION["cart_item"]));
                        }
                        ?>
                        <input type="hidden" id="totalQuantity" value="<?php echo esc_attr($cart_count); ?>">
                        <tr>
                            <td colspan="4" align="right">Total:</td>
                            <td align="right"><?php echo esc_html($total_quantity); ?></td>
                            <td></td>
                            <td></td>
                            <td align="right" colspan="1"><strong><?php echo $currencysymbol . " " . number_format($total_price, 2); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            <?php
            } else {
                $resuli = $wpdb->get_results("SELECT * from wp_viberent_pagename");
                $slug_name = sanitize_title($resuli[0]->pagename);

            ?>

                <h4 class="text-center my_cart_heading">My Cart
                    <a id="btn_mycart" class="btn_mycart pt-1">
                        (<span class="has-badge" data-count="0"></span>)
                    </a>
                </h4>

                <div class="no-records">Your Cart is Empty!<br>Please add items to place an order<br><br>
                    <a href="<?php echo site_url() . "/" . $slug_name; ?>" class="text-center text-white m-auto btn btn-primary border-0 h4 p-1 px-3 rounded" style="font-size: 1.5rem;">Shop Now</a>
                </div>
            <?php
            }
            ?>
        </div>

        <div id="place_order_div">

            <?php
            if (isset($total_quantity)) {
                if ($total_quantity != 0) {
            ?>
                    <a href="<?php echo site_url() . "/place-my-order/" ?>">
                        <button type="submit" name="my-place-order" id="btn_place_order">
                            <h5 class="m-0 p-2">Place Order</h5>
                        </button>
                    </a>
            <?php
                }
            }
            ?>

        </div>

    </div>
    <!-- Loading Spinner Wrapper-->
    <div class="loader text-center">
        <div class="loader-inner">

            <!-- Animated Spinner -->
            <div class="lds-roller mb-3">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>

            <!-- Spinner Description Text [For Demo Purpose]-->
            <h4 class="text-uppercase font-weight-bold">Loading Data</h4>
            <p class="font-italic text-muted">This loading window will be removed after <strong class="countdown text-dark font-weight-bold">3 </strong> Seconds</p>
        </div>
    </div>
</body>

</html>