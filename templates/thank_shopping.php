<?php

/*
Template name: Viberent thank-shopping
 */
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Thank You</title>
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
    <script src="<?php echo plugins_url(); ?>/viberent/assets/js/jquery.js"></script>
    <link rel="stylesheet" href="<?php echo plugins_url(); ?>/viberent/assets/css/thank.css" type="text/css" media="screen" />
      <script>
        jQuery(document).ready(function() {
            localStorage.clear();
        });
    </script>
</head>

<body>
<?php
if (isset($_SESSION["cart_item"])) {
  $total_quantity = 0;
  $total_price = 0;
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
  $all_items = array();
  $result = $wpdb->get_results("SELECT * from wp_viberent_clients_company_info");
  $companyID = $result[0]->companyID;
  foreach ($_SESSION["cart_item"] as $item) {
    $getcode = $item["code"];
    $mystartDate = date($dateFormat, strtotime($item["startDate"]));
    $myendDate = date($dateFormat, strtotime($item["endDate"]));
    $rentalp = $item["rental_period"];

    if ($item["productAvailble"] >= $item["quantity"]) {
      $myquanti = $item["quantity"];
    } else {
      $myquanti = $item["productAvailble"];
    }
    $responseperiod = wp_remote_get('https://viberent-api.azurewebsites.net/api/item/rental-periodtype?companyid=' . $companyID);
    if (is_wp_error($responseperiod) || wp_remote_retrieve_response_code($responseperiod) != 200) {
      return false;
    }
    $responsbody = wp_remote_retrieve_body($responseperiod);
    $respperiod = json_decode($responsbody, 1);
    foreach ($respperiod as $myresp) {
      if ($myresp["name"] == $item["rental_period"]) {
        $myHireID = $myresp["periodTypeId"];
      }
    }
    $each_item = array("from" => $item["startDate"], "to" => $item["endDate"], "itemGUID" => $item["GUID"], "itemCode" => $item["code"], "price" => $item["price"], "itemHireTypeID" => $myHireID, "quantity" => $myquanti, "location" => $item["locationID"]);
    array_push($all_items, $each_item);
  }
$resulty = $wpdb->get_results("SELECT * from wp_viberent_post_array WHERE `custoname` IS NOT NULL");
if(!empty($resulty)){
    $data = array('customerName' => $resulty[0]->custoname, 'companyid' => $resulty[0]->companyID, "billingAddresses" => array("isBilling" => true, "addressType" => "BillTo", "billAddrDtls" => $resulty[0]->billing_address, "city" => $resulty[0]->city_bill, "state" => $resulty[0]->state_bill, "postalCode" => $resulty[0]->postalCode_bill, "country" => $resulty[0]->country_bill, "email" => $resulty[0]->email_bill, "phone" => $resulty[0]->phone_bill, "contactName" => $resulty[0]->custoname), "shipingAddresses" => array("isBilling" => true, "addressType" => "ShipTo", "billAddrDtls" => $resulty[0]->shipping_address, "city" => $resulty[0]->city_bill, "state" => $resulty[0]->state_ship, "postalCode" => $resulty[0]->postalCode_ship, "country" => $resulty[0]->country_ship, "email" => $resulty[0]->email_ship, "phone" => $resulty[0]->phone_ship, "contactName" => $resulty[0]->custoname), "items" => $all_items);

    $curlUrl = wp_remote_post('https://viberent-api.azurewebsites.net/api/Quote/create', array(
      'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
      'body'        => json_encode($data),
      'method'      => 'POST',
      'data_format' => 'body',
    ));

    if (is_wp_error($curlUrl) || wp_remote_retrieve_response_code($curlUrl) != 200) {
      return false;
    }
    $response = wp_remote_retrieve_body($curlUrl);

    $respquote = json_decode($response, 1);

    if (isset($respquote['QuoteNumber'])) {
      $wpdb->insert('wp_quote_number', $respquote);
  }}
}

?>
    <div id="thanku">

        <h1><i>Thank you for shopping with us!</i></h1>
        <h4 style="color: green;">Your order was successfully placed</h4><?php
        $result = $wpdb->get_results("SELECT QuoteNumber from wp_quote_number WHERE `id` IS NOT NULL");

        if(isset($result[0]->QuoteNumber)) {?>
            <p>Please note your Quote Number for future reference</p><?php

            echo "<h3><b>Your Quote Number </b></h3><h3 style=font-weight:normal;>";
            echo $result[0]->QuoteNumber. "</h3";
            $wpdb->query("TRUNCATE TABLE `wp_quote_number`");
            unset($_SESSION["cart_item"]);
            $wpdb->query("TRUNCATE TABLE `wp_tbl_product`");
            $wpdb->query("TRUNCATE TABLE `wp_viberent_post_array`");

            }

            $resuli = $wpdb->get_results("SELECT * from wp_viberent_pagename");
            $slug_name = sanitize_title($resuli[0]->pagename);

        ?>

    </div>

    <div id="continue_btn">
        <a href="<?php echo site_url() . "/" . $slug_name; ?>" id="btn_shop_now">Continue Shopping</a>
    </div>

</body>
</html>
