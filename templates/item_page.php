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
$companyID = $result[0]->companyID;
$currencysymbol = $result[0]->currencysymbol;
$dateFormatfromAPi = $result[0]->dateFormat;
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
	  	} elseif($myresp["name"] == "Exclude Sun"){
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
	  	} elseif($myresp["name"] == "Monthly"){
			$firstRental_value = $firstRental_value -1;
			$firstRental_showValue = date('Y-m-d', strtotime($startDate . '+' . $firstRental_value . 'days'));
	  	} else{
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

?>
<?php
$cart_count = isset($_SESSION["cart_item"]) ? count(array_keys($_SESSION["cart_item"])) : 0;
?>
<input type="hidden" id="totalQuantity" value="<?php echo esc_attr($cart_count); ?>">
<?php
if (isset($_GET["pageno"])) {
	$page_nos  = sanitize_text_field($_GET["pageno"]);
} else {
	$page_nos = 1;
}
if ($dateFormatfromAPi == "dd/MM/yyyy") {
	$dateFormat = "j/m/Y";
} else if ($dateFormatfromAPi == "MM/dd/yyyy") {
	$dateFormat = "m/j/Y";
} else if ($dateFormatfromAPi == "MM-dd-yyyy") {
	$dateFormat = "m-j-Y";
}
$response = wp_remote_get($viberent_api_url . 'Item/item-list?&companyid=' . $companyID . '&pageSize=10&pageNumber=' . $page_nos, $api_args);
    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) {
      return false;
    }
$body     = wp_remote_retrieve_body($response);
$resp2 = json_decode($body, 1);
$startFrom_date = date("Y-m-d");
$startEnd_date = $firstRental_showValue;
if ($dateFormatfromAPi == "dd/MM/yyyy") {
	$date_Format = "DD/MM/YYYY";
} else if ($dateFormatfromAPi == "MM/dd/yyyy") {
	$date_Format = "MM/DD/YYYY";
} else if ($dateFormatfromAPi == "MM-dd-yyyy") {
	$date_Format = "MM-DD-YYYY";
}
$viberent_mypagename = $wpdb->get_results("SELECT * from " . $wpdb->prefix . "viberent_pagename");
if (!empty($viberent_mypagename)) {
	$mypagename = sanitize_title($viberent_mypagename[0]->pagename);
}

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 21600)) {
	session_unset();
	session_destroy();
    $wpdb->query("TRUNCATE TABLE " . $wpdb->prefix  . "viberent_tbl_product");
}
