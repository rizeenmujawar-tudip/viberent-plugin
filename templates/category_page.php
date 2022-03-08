<?php
global $wpdb;
$query = $_GET;
$full_path = explode('templates', plugin_dir_url(__FILE__));
array_pop($full_path);
$full_path = implode('templates', $full_path); 
$query_result = http_build_query($query);
if (isset($_GET['pageno'])) {
    $page_no_cat = sanitize_text_field($_GET['pageno']);
} else {
    $page_no_cat = 1;
}
$result = $wpdb->get_results("SELECT * from " . $wpdb->prefix . "viberent_clients_company_info");
if (!empty($result)) {
    $companyID = $result[0]->companyID;
    $resapikey = $wpdb->get_results("SELECT * from " . $wpdb->prefix . "viberent_apikey");
    $apikey = $resapikey[0]->apikey;
    $api_args = array( 'timeout' => 10,
    'headers'     => array(
        'ApiKey' => $apikey,
        'CompanyId' => $companyID
    )
); 
    $responseperiod = wp_remote_get($viberent_api_url . 'item/rental-periodtype?companyid=' . $companyID, $api_args);
    if (is_wp_error($responseperiod) || wp_remote_retrieve_response_code($responseperiod) != 200) {
      return false;
    }
    $responsbody = wp_remote_retrieve_body($responseperiod);
    $respperiod = json_decode($responsbody, 1);
    $countresp = 0;
    foreach($respperiod as $myresp) {
    if($countresp==0){
        $firstRental_period = $myresp["name"];
        $firstRental_value = $myresp["value"];
        $startDate = date("Y-m-d");
        if( ($myresp["name"] == "Exclude Sat / Sun") || ($myresp["name"] == "Exclude Sat / Sun Daily") ){
                $d = new DateTime($startDate);
                $t = $d->getTimestamp();
                // loop for X days
                for ($i = 1; $i < $firstRental_value; $i++) {
                    // add 1 day to timestamp
                    $addDay = 86400;
                    // get what day it is next day
                    $nextDay = date('w', ($t + $addDay));
                    // if it's Saturday or Sunday get $i-1
                    if ($nextDay == 0 || $nextDay == 6) {
                        $i--;
                    }
                    // modify timestamp, add 1 day
                    $t = $t + $addDay;
                }
                $d->setTimestamp($t);
                $firstRental_showValue = $d->format('Y-m-d');
        }elseif($myresp["name"] == "Exclude Sun"){
                    $d = new DateTime($startDate);
                    $t = $d->getTimestamp();
                    for ($i = 0; $i < $firstRental_value; $i++) {
                        $addDay = 86400;
                        $nextDay = date('w', ($t + $addDay));
                        if ($nextDay == 0) {
                            $i--;
                        }
                        $t = $t + $addDay;
                    }
                    $d->setTimestamp($t);
                    $firstRental_showValue = $d->format('Y-m-d');
        }elseif($myresp["name"] == "Monthly"){
                $firstRental_value = $firstRental_value -1;
                $firstRental_showValue = date('Y-m-d', strtotime($startDate . '+' . $firstRental_value . 'days'));
        }else{
                $firstRental_value = $firstRental_value -1;
                $firstRental_showValue = date('Y-m-d', strtotime($startDate . '+' . $firstRental_value . 'days')); 
        }     
        }
        $countresp = $countresp + 1;
    }
    if (isset($_POST["rentalratesName"])) {
        $rental_period = sanitize_text_field($_POST["rentalratesName"]);
    } else {
        $rental_period = sanitize_text_field($firstRental_period);
    }
}
$viberent_mypagename = $wpdb->get_results("SELECT * from " . $wpdb->prefix . "viberent_pagename");
if (!empty($viberent_mypagename)) {
    $mypagename = sanitize_title($viberent_mypagename[0]->pagename);
}
if (isset($_GET["category"])) {
    $categoryName = sanitize_text_field($_GET["category"]);
} else {
    $categoryName =  "all";
}
if (isset($_POST["add_to_cart"]) && $_GET["action"] && isset($_GET["GUID"]) && isset($_GET["rental_period"])) {
    $item_layout = array(
        "product_name" => sanitize_text_field($_POST['itemName']),
        "product_image" => sanitize_text_field($_POST['image']),
        "price" => sanitize_text_field($_POST['price']),
        "quantity" => sanitize_text_field($_POST['quantity']),
        "category_name" => $categoryName,
        "code" => sanitize_text_field($_POST["itemCode"]),
        "GUID" => sanitize_text_field($_POST["itemGUID"]),
        "hireTypeID" => sanitize_text_field($_POST["hireTypeID"]),
        "locationID" => sanitize_text_field($_POST["locationID"]),
        "rental_period" => $rental_period,
        "startDate" => sanitize_text_field($_POST["start-date"]),
        "endDate" => sanitize_text_field($_POST["end-date"]),
        "sessionID" => sanitize_text_field($_POST["sessionID"])
    );
    $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "viberent_tbl_product WHERE GUID ='" . sanitize_text_field($_GET["GUID"]) . "' AND rental_period='" . sanitize_text_field($_GET["rental_period"]) . "'"));
    $GUID = array();
    $rentalPeriod = array();
    foreach ($results as $val) {
        $GUID = sanitize_text_field($val->GUID);
        $rentalPeriod = sanitize_text_field($val->rental_period);
    }
    if ($GUID != $_GET["GUID"] || $rentalPeriod != $_GET["rental_period"]) {
        $wpdb->insert( $wpdb->prepare( $wpdb->prefix . 'viberent_tbl_product', $item_layout));
    }
}
if (!empty($_GET["action"])) {
    switch ($_GET["action"]) {
        case "add":
            if (!empty($_POST["quantity"])) {
                $productByCode = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "viberent_tbl_product WHERE GUID='" . sanitize_text_field($_GET["GUID"]) . "' AND rental_period='" . sanitize_text_field($_GET["rental_period"]) . "'"));
                $itemArray = array($productByCode[0]->sessionID => array(
                    'product_name' => $productByCode[0]->product_name,
                    'code' => $productByCode[0]->code,
                    "GUID" => $productByCode[0]->GUID,
                    "hireTypeID" => $productByCode[0]->hireTypeID,
                    "locationID" => $productByCode[0]->locationID,
                    'quantity' => $productByCode[0]->quantity,
                    'price' => $productByCode[0]->price,
                    'product_image' => $productByCode[0]->product_image,
                    'rental_period' => $productByCode[0]->rental_period,
                    'startDate' => $productByCode[0]->startDate,
                    'productAvailble' => sanitize_text_field($_POST["productAvailable"]),
                    'endDate' => $productByCode[0]->endDate,
                    'sessionID' => $productByCode[0]->sessionID
                ));
                if (!empty($_SESSION["cart_item"])) {
                    if (in_array($productByCode[0]->sessionID, array_keys($_SESSION["cart_item"]))) {
                        foreach ($_SESSION["cart_item"] as $k => $v) {
                            if ($productByCode[0]->sessionID == $k) {
                                if (empty($_SESSION["cart_item"][$k]["quantity"])) {
                                    $_SESSION["cart_item"][$k]["quantity"] = 0;
                                }
                                $_SESSION["cart_item"][$k]["quantity"] += sanitize_text_field($_POST["quantity"]);
                            }
                        }
                    } else {
                        $_SESSION["cart_item"] = array_merge($_SESSION["cart_item"], $itemArray);
                    }
                } else {
                    $_SESSION["cart_item"] = $itemArray;
                }
            }
            break;
        case "empty":
            unset($_SESSION["cart_item"]);
            $wpdb->query("TRUNCATE TABLE " . $wpdb->prefix  . "viberent_tbl_product");
            break;
    }
}
$cart_count = isset($_SESSION["cart_item"]) ? count(array_keys($_SESSION["cart_item"])) : 0;
?>
<input type="hidden" id="totalQuantity" value="<?php echo $cart_count; ?>">
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
$page_no = 1;
$resapikey = $wpdb->get_results("SELECT * from " . $wpdb->prefix . "viberent_apikey");
$apikey = $resapikey[0]->apikey;
$api_args = array( 'timeout' => 10,
    'headers'     => array(
        'ApiKey' => $apikey,
        'CompanyId' => $companyID
    )
); 
$curlgetcategorylist = wp_remote_get($viberent_api_url . 'item/subcategories?companyid=' . $companyID .  '&pageSize=10&pageNumber=' . $page_no, $api_args);
if (is_wp_error($curlgetcategorylist) || wp_remote_retrieve_response_code($curlgetcategorylist) != 200) {
    return false;
}
$response_body = wp_remote_retrieve_body($curlgetcategorylist);
$resp_body = json_decode($response_body, 1);
$curlperiod = wp_remote_get($viberent_api_url . 'item/rental-periodtype?companyid=' . $companyID, $api_args);
if (is_wp_error($curlperiod) || wp_remote_retrieve_response_code($curlperiod) != 200) {
    return false;
}
$responseperiod = wp_remote_retrieve_body($curlperiod);
$respperiod = json_decode($responseperiod, 1);
$startFrom_date = date("Y-m-d");
$startEnd_date = sanitize_text_field($firstRental_showValue);
if ($dateFormatfromAPi == "dd/MM/yyyy") {
    $date_Format = "DD/MM/YYYY";
} else if ($dateFormatfromAPi == "MM/dd/yyyy") {
    $date_Format = "MM/DD/YYYY";
} else if ($dateFormatfromAPi == "MM-dd-yyyy") {
    $date_Format = "MM-DD-YYYY";
}
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 21600)) {
    session_unset(); 
    session_destroy();
    $wpdb->query("TRUNCATE TABLE " . $wpdb->prefix  . "viberent_tbl_product");
}
