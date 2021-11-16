<?php

/*
Template name: Viberent my-cart
 */

?>

<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="<?php echo plugins_url(); ?>/viberent/assets/css/category.css" type="text/css" media="screen" />
<script>
    jQuery(document).ready(function() {
        var totalQuantity = $("#totalQuantity").val();
        if (totalQuantity > 0) {
            $(".btn_mycart").find("span.has-badge").attr('data-count', totalQuantity);
        } else {
            $(".btn_mycart").find("span.has-badge").attr('data-count', '0');
        }
    });
    // The function below will start the confirmation dialog
    function confirmAction(itemcode) {
        var confirmAction = confirm("Are you sure to delete the item?");
        if (confirmAction) {
            var url = window.location.pathname + "/?action=remove&code=" + itemcode;
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
<?php
session_start();
$query = $_GET;
$query_result = http_build_query($query);
if (isset($_GET['pageno'])) {
    $page_no_cat = $_GET['pageno'];
} else {
    $page_no_cat = 1;
}
global $wpdb;


if (!empty($_GET["action"])) {
    switch ($_GET["action"]) {
        case "remove":
            if (!empty($_SESSION["cart_item"])) {
                foreach ($_SESSION["cart_item"] as $k => $v) {
                    if ($_GET["code"] == $k)
                        unset($_SESSION["cart_item"][$k]);
                    $delete_id = trim($_GET["code"]);
                    $wpdb->delete('wp_tbl_product', array('code' => $delete_id));

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

// $cart_products = $wpdb->get_results("SELECT id from wp_tbl_product WHERE `id` IS NOT NULL");
// $count_cart_products = count($cart_products);

?>

<div class="cart_page">

<div id="shopping-cart">

    <div id="my-shopping-cart">

        <div id="my_shop_nav">
            <div id="empty_cart">
                <a id="btnEmpty" href="#" onclick="confirmAll()">Empty Cart</a>
            </div>
            <a id="btn_mycart" class="btn_mycart" href="<?php echo site_url() . "/my-cart/" ?>">
                <span class="fa-stack fa-2x has-badge cart" data-count="0">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-shopping-cart fa-stack-1x fa-inverse"></i>
                </span>
            </a>
        </div>

    </div>

    <?php
    if (isset($_SESSION["cart_item"])) {
        $total_quantity = 0;
        $total_price = 0;
        $cart_count = 0;
    ?>
        <table class="tbl-cart" cellpadding="10" cellspacing="1">
            <tbody>
                <tr>
                    <th style="text-align:left;" width="20%">Name</th>
                    <th style="text-align:left;" width="10%">Rental Period</th>
                    <th style="text-align:left;" width="10%">Start Date</th>
                    <th style="text-align:left;" width="10%">End Date</th>
                    <th style="text-align:center;" width="5%">Quantity</th>
                    <th style="text-align:center;" width="20%">Unit Price</th>
                    <th style="text-align:center;" width="20%">Amount</th>
                    <th style="text-align:center;" width="5%">Remove</th>
                </tr>
                <?php

                $result = $wpdb->get_results("SELECT * from wp_viberent_clients_company_info");

                $currencysymbol = $result[0]->currencysymbol;

                $dateFormatfromAPi = $result[0]->dateFormat;

                if ($dateFormatfromAPi == "dd/MM/yyyy") {
                    $dateFormat = "j/m/Y";
                } else if ($dateFormatfromAPi == "MM/dd/yyyy") {
                    $dateFormat = "m/j/Y";
                } else if ($dateFormatfromAPi == "MM-dd-yyyy") {
                    $dateFormat = "m-j-Y";
                }

                foreach ($_SESSION["cart_item"] as $item) {
                    if ($item["productAvailble"] >= $item["quantity"]) {
                        $productAvailable = $item["quantity"];
                        $getcode = $item["code"];
                        $item_price = $productAvailable * $item["price"];
                        $wpdb->query($wpdb->prepare("UPDATE wp_tbl_product
                        SET quantity= " . $item["quantity"] . "
                        WHERE code= %s", $getcode));
                    } else {
                        $productAvailable = $item["productAvailble"];
                        $item_price = $productAvailable * $item["price"];
                    }

                ?>
                    <tr>
                        <td><img src="<?php echo $item["product_image"]; ?>" class="cart-item-image" />
                            <p><?php echo $item["product_name"]; ?></p>
                        </td>
                        <td><?php echo $item["rental_period"]; ?></td>
                        <td><?php echo date($dateFormat, strtotime($item["startDate"])); ?></td>
                        <td><?php echo date($dateFormat, strtotime($item["endDate"])); ?></td>
                        <td style="text-align:center;"><?php echo $productAvailable; ?></td>
                        <td style="text-align:center;"><?php echo $currencysymbol . " " . $item["price"]; ?></td>
                        <td style="text-align:center;"><?php echo $currencysymbol . " " . number_format($item_price, 2); ?></td>
                        <td style="text-align:center;"><a href='#' class="btnRemoveAction" onclick="confirmAction('<?php echo $item['code']; ?>')"><img src="<?php echo plugins_url(); ?>/viberent/assets/images/icon-delete.png" alt="Remove Item" /></a></td>
                    </tr>
                <?php
                    $total_quantity += $productAvailable;
                    $total_price += ($item["price"] * $productAvailable);
                    $cart_count = count(array_keys($_SESSION["cart_item"]));
                }
                ?>
                <input type="hidden" id="totalQuantity" value="<?php echo $cart_count; ?>">
                <tr>
                    <td colspan="4" align="right">Total:</td>
                    <td align="center"><?php echo $total_quantity; ?></td>
                    <td></td>
                    <td align="center" colspan="1"><strong><?php echo $currencysymbol . " " . number_format($total_price, 2); ?></strong></td>
                </tr>
            </tbody>
        </table>
    <?php
    } else {
    ?>
        <div class="no-records">Your Cart is Empty<br><br>Please add items to place an order</div>
    <?php
    }
    ?>
</div>

<div id="place_order_div">

    <?php
    if (isset($total_quantity)) {
        if ($total_quantity != 0) {
    ?>
            <a href="<?php echo site_url() . "/place-my-order/";
                        ?>">
                <button type="submit" name="my-place-order" id="btn_place_order">
                    <h2>Place Order</h2>
                </button>
            </a>
    <?php
        }
    }
    ?>

</div>

</div>