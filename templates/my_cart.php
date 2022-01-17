<?php
session_start();
/* Template name: Viberent my-cart */
get_header();
?>
<script>
    jQuery('document').ready(function($) {
        var totalQuantity = $("#totalQuantity").val();
        if (totalQuantity > 0) {
            $(".btn_mycart").find("span.has-badge").attr('data-count', totalQuantity);
        } else {
            $(".btn_mycart").find("span.has-badge").attr('data-count', '0');
        }
    });
</script>
<?php
$query = $_GET;
$query_result = http_build_query($query);
if (isset($_GET['pageno'])) {
    $page_no_cat = sanitize_text_field($_GET['pageno']);
} else {
    $page_no_cat = 1;
}
global $wpdb;
$pID = isset($_POST['pID']) ? sanitize_text_field($_POST['pID']) : "";
$quan = isset($_POST['quan']) ? sanitize_text_field($_POST['quan']) : "";
$rental_period = isset($_POST['rental_period']) ? sanitize_text_field($_POST['rental_period']) : "";
$session_ID = isset($_POST['sessionID']) ? sanitize_text_field($_POST['sessionID']) : "";
$productByCode = $wpdb->get_results("SELECT * from " . $wpdb->prefix . "tbl_product WHERE sessionID='" . $session_ID . "'");
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
                    $wpdb->delete($wpdb->prefix . 'tbl_product', array('sessionID' => $delete_id));
                    if (empty($_SESSION["cart_item"]))
                        unset($_SESSION["cart_item"]);
                }
            }
            break;
        case "empty":
            unset($_SESSION["cart_item"]);
            $wpdb->query("TRUNCATE TABLE " . $wpdb->prefix  . "tbl_product");
            break;
    }
}
$resuli = $wpdb->get_results("SELECT * from " . $wpdb->prefix . "viberent_pagename");
$slug_name = sanitize_title($resuli[0]->pagename);
?>
<div class="viberent_my_cart">
    <div class="cart_page container pt-3" id="shopping-cart">
        <div class="d-flex justify-content-between">
            <a href="<?php echo site_url() . "/" . $slug_name; ?>">
                <i class="viberent_back_button fas fa-arrow-circle-left fa-stack-2x text-left"></i>
            </a>
            <div id="empty_cart">
                <a id="btnEmpty" href="#" onclick="confirmAll()">Empty Cart</a>
            </div>
        </div>
        <h4 class="text-center my_cart_heading m-0">My Cart
            <a id="empty_mycart" class="btn_mycart pt-1">
                (<span class="has-badge" data-count="0"></span>)
            </a>
        </h4>
        <?php
        if (isset($_SESSION["cart_item"])) {
            $total_quantity = 0;
            $total_price = 0;
            $cart_count = 0;
            $resuli = $wpdb->get_results("SELECT * from " . $wpdb->prefix . "viberent_pagename");
            $slug_name = sanitize_title($resuli[0]->pagename);
        ?>
            <table class="tbl-cart" cellpadding="10" cellspacing="1">
                <tbody>
                    <tr>
                        <th style="text-align:left !important;background-color: #4d96d5;" width="20%">Name</th>
                        <th style="text-align:left !important;background-color: #4d96d5;" width="10%">Rental Period</th>
                        <th style="text-align:left !important;background-color: #4d96d5;" width="10%">Start Date</th>
                        <th style="text-align:left !important;background-color: #4d96d5;" width="10%">End Date</th>
                        <th style="text-align:right !important;background-color: #4d96d5;" width="8%">Quantity</th>
                        <th style="text-align:right !important;background-color: #4d96d5;" width="10%">Period Unit</th>
                        <th style="text-align:right !important;background-color: #4d96d5;" width="15%">Unit Price</th>
                        <th style="text-align:right !important;background-color: #4d96d5;" width="10%">Amount</th>
                        <th style="text-align:center !important;background-color: #4d96d5;" width="7%">Remove</th>
                    </tr>
                    <?php
                    $result = $wpdb->get_results("SELECT * from " . $wpdb->prefix . "viberent_clients_company_info");
                    $companyID = sanitize_text_field($result[0]->companyID);
                    $currencysymbol = sanitize_text_field($result[0]->currencysymbol);
                    $dateFormatfromAPi = sanitize_text_field($result[0]->dateFormat);
                    if ($dateFormatfromAPi == "dd/MM/yyyy") {
                        $dateFormat = "j/m/Y";
                    } else if ($dateFormatfromAPi == "MM/dd/yyyy") {
                        $dateFormat = "m/j/Y";
                    } else if ($dateFormatfromAPi == "MM-dd-yyyy") {
                        $dateFormat = "m-j-Y";
                    }
                    $resapikey = $wpdb->get_results("SELECT * from " . $wpdb->prefix . "viberent_apikey");
                    $apikey = $resapikey[0]->apikey;
                    $api_args = array(
                        'timeout' => 10,
                        'headers'     => array(
                            'ApiKey' => $apikey,
                            'CompanyId' => $companyID
                        )
                    );
                    foreach ($_SESSION["cart_item"] as $key => $item) {
                        $responseperiod = wp_remote_get('https://viberent-api.azurewebsites.net/api/item/rental-periodtype?companyid=' . $companyID, $api_args);
                        if (is_wp_error($responseperiod) || wp_remote_retrieve_response_code($responseperiod) != 200) {
                            return false;
                        }
                        $responsbody = wp_remote_retrieve_body($responseperiod);
                        $respperiod = json_decode($responsbody, 1);
                        foreach ($respperiod as $retrieved_period) {
                            if ($item["rental_period"] == $retrieved_period["name"]) {
                                $curlavail = wp_remote_get('https://viberent-api.azurewebsites.net/api/Item/item-availability?itemGUID=' . $item["GUID"] . '&companyid=' . $companyID . '&fromDate=' . $item["startDate"] . '&todate=' . $item["endDate"] . '&PeriodTypeId=' . $retrieved_period["periodTypeId"] . '&locationID=' . $item["locationID"], $api_args);

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
                        $pID = isset($_POST['pID']) ? sanitize_text_field($_POST['pID']) : sanitize_text_field($getcode);
                        $quan = isset($_POST['quan']) ? sanitize_text_field($_POST['quan']) : sanitize_text_field($productAvailable);
                        $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->prefix . "tbl_product
                             SET quantity= %s
                             WHERE code= %s", $quan, $pID));
                        if (isset($productAvailable) && isset($item["price"]) && isset($respavail[0]["periodUnits"])) {
                            $item_price = (int)$productAvailable * (float)$item["price"] * (float)$respavail[0]["periodUnits"];
                        }
                    ?>
                        <tr style="text-align:left;">
                            <td style="text-align:left !important;"><img src="<?php echo esc_url($item["product_image"]); ?>" class="cart-item-image" />
                                <p class="my-auto"><?php echo esc_html($item["product_name"]); ?></p>
                            </td>
                            <td style="text-align:left !important;"><?php echo esc_html($item["rental_period"]); ?></td>
                            <td style="text-align:left !important;"><?php echo date($dateFormat, strtotime($item["startDate"])); ?></td>
                            <td style="text-align:left !important;"><?php echo date($dateFormat, strtotime($item["endDate"])); ?></td>
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
                        <td colspan="4" style="text-align:right;">Total:</td>
                        <td style="text-align:right;"><?php echo esc_html($total_quantity); ?></td>
                        <td></td>
                        <td></td>
                        <td colspan="1" style="text-align:right;"><strong><?php echo $currencysymbol . " " . number_format($total_price, 2); ?></strong></td>
                    </tr>
                </tbody>
            </table>
        <?php
        } else {
            $resuli = $wpdb->get_results("SELECT * from " . $wpdb->prefix . "viberent_pagename");
            $slug_name = sanitize_title($resuli[0]->pagename);
        ?>
            <div class="no-records">Your Cart is Empty!<br>Please add items to place an order<br><br>
                <a href="<?php echo site_url() . "/" . $slug_name; ?>" class="viberent_shop_now text-center text-white m-auto btn btn-primary border-0 h4 p-1 px-3 rounded">Shop Now</a>
            </div>
            <?php
        }
        if (isset($total_quantity)) {
            if ($total_quantity != 0) {
            ?>
                <a href="<?php echo site_url() . "/place-my-order/" ?>">
                    <button type="submit" name="my-place-order" id="btn_place_order" class="mb-5">
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
<div class="loading-spinner">
    <div class="loader text-center">
        <div class="loader-inner">
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
            <h4 class="text-uppercase font-weight-bold">Loading Data</h4>
            <p class="font-italic text-muted">This loading window will be removed after <strong class="countdown text-dark font-weight-bold">7 </strong> Seconds</p>
        </div>
    </div>
</div>
<?php get_footer(); ?>
