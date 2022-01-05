<?php
session_start();
/*
Template name: Viberent place-my-order
 */

$result = $wpdb->get_results("SELECT * from wp_viberent_clients_company_info");
$logo_result = sanitize_text_field($result[0]->logo);
$logo = isset($logo_result) ?  $logo_result : "Logo";

?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>Place My order</title>
  <meta name="description" content="">
  <meta name="author" content="">
  <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
  <link rel="profile" href="http://gmpg.org/xfn/11" />
  <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
  <link rel="stylesheet" href="<?php echo plugins_url(); ?>/viberent/assets/css/all.css" type="text/css" media="screen" />
  <link rel="stylesheet" href="<?php echo plugins_url(); ?>/viberent/assets/css/custom.css">
  <link rel="stylesheet" href="<?php echo plugins_url(); ?>/viberent/assets/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="<?php echo plugins_url(); ?>/viberent/assets/js/jquery.js"></script>
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

    });
  </script>

</head>

<body>
  <link rel="stylesheet" href="<?php echo plugins_url(); ?>/viberent/assets/css/place_order.css" type="text/css" media="screen" />
  <nav class="navbar navbar-light bg-light sticky-top justify-content-between px-5 py-0">
    <a class="navbar-brand p-0" href="#"><?php echo "<img class='logo-image' src='data:image/jpeg;base64, $logo' />"; ?></a>
    <a id="btn_mycart" class="btn_mycart pt-1" href="<?php echo site_url() . "/my-cart/" ?>">
      <span class="fa-stack fa-2x has-badge cart" data-count="0">
        <i class="fa fa-shopping-cart fa-stack-1x"></i>
      </span>
    </a>
  </nav>

  <?php
  $query = $_GET;
  $query_result = http_build_query($query);
  if (isset($_GET['pageno'])) {
    $page_no_cat = sanitize_text_field($_GET['pageno']);
  } else {
    $page_no_cat = 1;
  }
  global $wpdb;
  if (isset($_SESSION["cart_item"])) {
    $total_quantity = 0;
    $total_price = 0;
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
    $all_items = array();

    $result = $wpdb->get_results("SELECT * from wp_viberent_clients_company_info");
    $companyID = sanitize_text_field($result[0]->companyID);

    $resapikey = $wpdb->get_results("SELECT * from wp_viberent_apikey");
    $apikey = $resapikey[0]->apikey;
    $api_args = array( 'timeout' => 10,
        'headers'     => array(
          'ApiKey' => $apikey,
            'CompanyId' => $companyID
        )
    ); 
    foreach ($_SESSION["cart_item"] as $item) {
      //print_r($item);
      $getcode = $item["code"];
      $mystartDate = date($dateFormat, strtotime($item["startDate"]));
      $myendDate = date($dateFormat, strtotime($item["endDate"]));
      $rentalp = sanitize_text_field($item["rental_period"]);

      if ($item["productAvailble"] >= $item["quantity"]) {
        $myquanti = $item["quantity"];
      } else {
        $myquanti = $item["productAvailble"];
      }

      $responseperiod = wp_remote_get('https://viberent-api.azurewebsites.net/api/item/rental-periodtype?companyid=' . $companyID, $api_args);

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
  ?>

    <?php
    if (isset($_POST['confirm_order'])) {
      $companyID = $result[0]->companyID;
      $custoname = sanitize_text_field($_POST["customer_name"]);
      $custoCompany = sanitize_text_field($_POST["customer_company"]);
      $billing_address = sanitize_text_field($_POST["billing_address"]);
      $city_bill = sanitize_text_field($_POST["city_bill"]);
      $state_bill = sanitize_text_field($_POST["state_bill"]);
      $postalCode_bill = sanitize_text_field($_POST["postalCode_bill"]);
      $country_bill = sanitize_text_field($_POST["country_bill"]);
      $email_bill = sanitize_text_field($_POST["email_bill"]);
      $phone_bill = sanitize_text_field($_POST["phone_bill"]);

      if (isset($_POST['diff_shippin'])) {
        $shipping_address = sanitize_text_field($_POST["shipping_address"]);
        $city_ship = sanitize_text_field($_POST["city_ship"]);
        $state_ship = sanitize_text_field($_POST["state_ship"]);
        $postalCode_ship = sanitize_text_field($_POST["postalCode_ship"]);
        $country_ship = sanitize_text_field($_POST["country_ship"]);
        $email_ship = sanitize_text_field($_POST["email_ship"]);
        $phone_ship = sanitize_text_field($_POST["phone_ship"]);
      } else {
        $shipping_address = sanitize_text_field($_POST["billing_address"]);
        $city_ship = sanitize_text_field($_POST["city_bill"]);
        $state_ship = sanitize_text_field($_POST["state_bill"]);
        $postalCode_ship = sanitize_text_field($_POST["postalCode_bill"]);
        $country_ship = sanitize_text_field($_POST["country_bill"]);
        $email_ship = sanitize_text_field($_POST["email_bill"]);
        $phone_ship = sanitize_text_field($_POST["phone_bill"]);
      }

      $datatest = array('custoname' => $custoname, 'companyid' => $companyID, "billing_address" => $billing_address, "city_bill" => $city_bill, "state_bill" => $state_bill, "postalCode_bill" => $postalCode_bill, "country_bill" => $country_bill, "email_bill" => $email_bill, "phone_bill" => $phone_bill, "shipping_address" => $shipping_address, "city_ship" => $city_ship, "state_ship" => $state_ship, "postalCode_ship" => $postalCode_ship, "country_ship" => $country_ship, "email_ship" => $email_ship, "phone_ship" => $phone_ship);


      $resulty = $wpdb->get_results("SELECT custoname from wp_viberent_post_array WHERE `custoname` IS NOT NULL");

      if (count($resulty) == 0) {

        $wpdb->insert('wp_viberent_post_array', $datatest);
      }

      $url = site_url() . "/thank-shopping";
    ?>
      <script>
        window.location = '<?php echo $url; ?>';
      </script>
    <?php

      // }
    }

    $resuli = $wpdb->get_results("SELECT * from wp_viberent_pagename");
    $slug_name = sanitize_title($resuli[0]->pagename);

    if (isset($_SESSION['cart_item'])) {
      $cart_count = count($_SESSION['cart_item']);
    }
    ?>


    <input type="hidden" id="totalQuantity" value="<?php echo $cart_count; ?>">

    <a href="<?php echo site_url() . "/my-cart"; ?>">
      <i class="back_button fas fa-arrow-circle-left fa-stack-2x text-left pl-3 pt-3"></i>
    </a>

    <h3 class="text-center pt-2" id="delivery_heading">Delivery Details</h3>

    <div class="container mx-auto" id="orderDet_div">
      <form method="post" class="placeOrderForm p-4" id="placeOrderForm">
        <div class="row field">
          <div class="col-25">
            <label for="custname">Name<span class="formAsterisk">*</span></label>
          </div>
          <div class="col-75">
            <input type="text" id="custname" name="customer_name" placeholder="Enter Name" required>
          </div>
        </div>
        <div class="row field">
          <div class="col-25">
            <label for="custCompany">Company Name<span class="formAsterisk">*</span></label>
          </div>
          <div class="col-75">
            <input type="text" id="custCompany" name="customer_company" placeholder="Enter Company Name" required>
          </div>
        </div>
        <div class="row field">
          <div class=" col-25">
            <label for="billing">Billing Address<span class="formAsterisk">*</span></label>
          </div>
          <div class="col-75 field">

            <input type="text" id="billing" name="billing_address" placeholder="Enter Billing Address" required>

            <div class="dis-flex field">
              <input type="text" id="city_bill" name="city_bill" placeholder="Enter City" required>
              <input type="text" id="state_bill" name="state_bill" placeholder="Enter State" required>
            </div>

            <div class="dis-flex">

              <input type="text" id="postalCode_bill" name="postalCode_bill" placeholder="Enter Postal Code" required>

              <select id="country_bill" name="country_bill" required>
                <option value="" selected disabled>Please select a Country</option>
                <option value="AF">Afghanistan</option>
                <option value="AL">Albania</option>
                <option value="DZ">Algeria</option>
                <option value="AS">American Samoa</option>
                <option value="AD">Andorra</option>
                <option value="AO">Angola</option>
                <option value="AI">Anguilla</option>
                <option value="AQ">Antarctica</option>
                <option value="AG">Antigua and Barbuda</option>
                <option value="AR">Argentina</option>
                <option value="AM">Armenia</option>
                <option value="AW">Aruba</option>
                <option value="AU">Australia</option>
                <option value="AT">Austria</option>
                <option value="AZ">Azerbaijan</option>
                <option value="BS">Bahamas</option>
                <option value="BH">Bahrain</option>
                <option value="BD">Bangladesh</option>
                <option value="BB">Barbados</option>
                <option value="BY">Belarus</option>
                <option value="BE">Belgium</option>
                <option value="BZ">Belize</option>
                <option value="BJ">Benin</option>
                <option value="BM">Bermuda</option>
                <option value="BT">Bhutan</option>
                <option value="BO">Bolivia</option>
                <option value="BA">Bosnia and Herzegowina</option>
                <option value="BW">Botswana</option>
                <option value="BV">Bouvet Island</option>
                <option value="BR">Brazil</option>
                <option value="IO">British Indian Ocean Territory</option>
                <option value="BN">Brunei Darussalam</option>
                <option value="BG">Bulgaria</option>
                <option value="BF">Burkina Faso</option>
                <option value="BI">Burundi</option>
                <option value="KH">Cambodia</option>
                <option value="CM">Cameroon</option>
                <option value="CA">Canada</option>
                <option value="CV">Cape Verde</option>
                <option value="KY">Cayman Islands</option>
                <option value="CF">Central African Republic</option>
                <option value="TD">Chad</option>
                <option value="CL">Chile</option>
                <option value="CN">China</option>
                <option value="CX">Christmas Island</option>
                <option value="CC">Cocos (Keeling) Islands</option>
                <option value="CO">Colombia</option>
                <option value="KM">Comoros</option>
                <option value="CG">Congo</option>
                <option value="CD">Congo, the Democratic Republic of the</option>
                <option value="CK">Cook Islands</option>
                <option value="CR">Costa Rica</option>
                <option value="CI">Cote d'Ivoire</option>
                <option value="HR">Croatia (Hrvatska)</option>
                <option value="CU">Cuba</option>
                <option value="CY">Cyprus</option>
                <option value="CZ">Czech Republic</option>
                <option value="DK">Denmark</option>
                <option value="DJ">Djibouti</option>
                <option value="DM">Dominica</option>
                <option value="DO">Dominican Republic</option>
                <option value="TP">East Timor</option>
                <option value="EC">Ecuador</option>
                <option value="EG">Egypt</option>
                <option value="SV">El Salvador</option>
                <option value="GQ">Equatorial Guinea</option>
                <option value="ER">Eritrea</option>
                <option value="EE">Estonia</option>
                <option value="ET">Ethiopia</option>
                <option value="FK">Falkland Islands (Malvinas)</option>
                <option value="FO">Faroe Islands</option>
                <option value="FJ">Fiji</option>
                <option value="FI">Finland</option>
                <option value="FR">France</option>
                <option value="FX">France, Metropolitan</option>
                <option value="GF">French Guiana</option>
                <option value="PF">French Polynesia</option>
                <option value="TF">French Southern Territories</option>
                <option value="GA">Gabon</option>
                <option value="GM">Gambia</option>
                <option value="GE">Georgia</option>
                <option value="DE">Germany</option>
                <option value="GH">Ghana</option>
                <option value="GI">Gibraltar</option>
                <option value="GR">Greece</option>
                <option value="GL">Greenland</option>
                <option value="GD">Grenada</option>
                <option value="GP">Guadeloupe</option>
                <option value="GU">Guam</option>
                <option value="GT">Guatemala</option>
                <option value="GN">Guinea</option>
                <option value="GW">Guinea-Bissau</option>
                <option value="GY">Guyana</option>
                <option value="HT">Haiti</option>
                <option value="HM">Heard and Mc Donald Islands</option>
                <option value="VA">Holy See (Vatican City State)</option>
                <option value="HN">Honduras</option>
                <option value="HK">Hong Kong</option>
                <option value="HU">Hungary</option>
                <option value="IS">Iceland</option>
                <option value="IN">India</option>
                <option value="ID">Indonesia</option>
                <option value="IR">Iran (Islamic Republic of)</option>
                <option value="IQ">Iraq</option>
                <option value="IE">Ireland</option>
                <option value="IL">Israel</option>
                <option value="IT">Italy</option>
                <option value="JM">Jamaica</option>
                <option value="JP">Japan</option>
                <option value="JO">Jordan</option>
                <option value="KZ">Kazakhstan</option>
                <option value="KE">Kenya</option>
                <option value="KI">Kiribati</option>
                <option value="KP">Korea, Democratic People's Republic of</option>
                <option value="KR">Korea, Republic of</option>
                <option value="KW">Kuwait</option>
                <option value="KG">Kyrgyzstan</option>
                <option value="LA">Lao People's Democratic Republic</option>
                <option value="LV">Latvia</option>
                <option value="LB">Lebanon</option>
                <option value="LS">Lesotho</option>
                <option value="LR">Liberia</option>
                <option value="LY">Libyan Arab Jamahiriya</option>
                <option value="LI">Liechtenstein</option>
                <option value="LT">Lithuania</option>
                <option value="LU">Luxembourg</option>
                <option value="MO">Macau</option>
                <option value="MK">Macedonia, The Former Yugoslav Republic of</option>
                <option value="MG">Madagascar</option>
                <option value="MW">Malawi</option>
                <option value="MY">Malaysia</option>
                <option value="MV">Maldives</option>
                <option value="ML">Mali</option>
                <option value="MT">Malta</option>
                <option value="MH">Marshall Islands</option>
                <option value="MQ">Martinique</option>
                <option value="MR">Mauritania</option>
                <option value="MU">Mauritius</option>
                <option value="YT">Mayotte</option>
                <option value="MX">Mexico</option>
                <option value="FM">Micronesia, Federated States of</option>
                <option value="MD">Moldova, Republic of</option>
                <option value="MC">Monaco</option>
                <option value="MN">Mongolia</option>
                <option value="MS">Montserrat</option>
                <option value="MA">Morocco</option>
                <option value="MZ">Mozambique</option>
                <option value="MM">Myanmar</option>
                <option value="NA">Namibia</option>
                <option value="NR">Nauru</option>
                <option value="NP">Nepal</option>
                <option value="NL">Netherlands</option>
                <option value="AN">Netherlands Antilles</option>
                <option value="NC">New Caledonia</option>
                <option value="NZ">New Zealand</option>
                <option value="NI">Nicaragua</option>
                <option value="NE">Niger</option>
                <option value="NG">Nigeria</option>
                <option value="NU">Niue</option>
                <option value="NF">Norfolk Island</option>
                <option value="MP">Northern Mariana Islands</option>
                <option value="NO">Norway</option>
                <option value="OM">Oman</option>
                <option value="PK">Pakistan</option>
                <option value="PW">Palau</option>
                <option value="PA">Panama</option>
                <option value="PG">Papua New Guinea</option>
                <option value="PY">Paraguay</option>
                <option value="PE">Peru</option>
                <option value="PH">Philippines</option>
                <option value="PN">Pitcairn</option>
                <option value="PL">Poland</option>
                <option value="PT">Portugal</option>
                <option value="PR">Puerto Rico</option>
                <option value="QA">Qatar</option>
                <option value="RE">Reunion</option>
                <option value="RO">Romania</option>
                <option value="RU">Russian Federation</option>
                <option value="RW">Rwanda</option>
                <option value="KN">Saint Kitts and Nevis</option>
                <option value="LC">Saint LUCIA</option>
                <option value="VC">Saint Vincent and the Grenadines</option>
                <option value="WS">Samoa</option>
                <option value="SM">San Marino</option>
                <option value="ST">Sao Tome and Principe</option>
                <option value="SA">Saudi Arabia</option>
                <option value="SN">Senegal</option>
                <option value="SC">Seychelles</option>
                <option value="SL">Sierra Leone</option>
                <option value="SG">Singapore</option>
                <option value="SK">Slovakia (Slovak Republic)</option>
                <option value="SI">Slovenia</option>
                <option value="SB">Solomon Islands</option>
                <option value="SO">Somalia</option>
                <option value="ZA">South Africa</option>
                <option value="GS">South Georgia and the South Sandwich Islands</option>
                <option value="ES">Spain</option>
                <option value="LK">Sri Lanka</option>
                <option value="SH">St. Helena</option>
                <option value="PM">St. Pierre and Miquelon</option>
                <option value="SD">Sudan</option>
                <option value="SR">Suriname</option>
                <option value="SJ">Svalbard and Jan Mayen Islands</option>
                <option value="SZ">Swaziland</option>
                <option value="SE">Sweden</option>
                <option value="CH">Switzerland</option>
                <option value="SY">Syrian Arab Republic</option>
                <option value="TW">Taiwan, Province of China</option>
                <option value="TJ">Tajikistan</option>
                <option value="TZ">Tanzania, United Republic of</option>
                <option value="TH">Thailand</option>
                <option value="TG">Togo</option>
                <option value="TK">Tokelau</option>
                <option value="TO">Tonga</option>
                <option value="TT">Trinidad and Tobago</option>
                <option value="TN">Tunisia</option>
                <option value="TR">Turkey</option>
                <option value="TM">Turkmenistan</option>
                <option value="TC">Turks and Caicos Islands</option>
                <option value="TV">Tuvalu</option>
                <option value="UG">Uganda</option>
                <option value="UA">Ukraine</option>
                <option value="AE">United Arab Emirates</option>
                <option value="GB">United Kingdom</option>
                <option value="US">United States</option>
                <option value="UM">United States Minor Outlying Islands</option>
                <option value="UY">Uruguay</option>
                <option value="UZ">Uzbekistan</option>
                <option value="VU">Vanuatu</option>
                <option value="VE">Venezuela</option>
                <option value="VN">Viet Nam</option>
                <option value="VG">Virgin Islands (British)</option>
                <option value="VI">Virgin Islands (U.S.)</option>
                <option value="WF">Wallis and Futuna Islands</option>
                <option value="EH">Western Sahara</option>
                <option value="YE">Yemen</option>
                <option value="YU">Yugoslavia</option>
                <option value="ZM">Zambia</option>
                <option value="ZW">Zimbabwe</option>
              </select>

            </div>
            <div class="dis-flex field">
              <input type="email" id="email_bill" name="email_bill" placeholder="Enter Email" required>
              <input type="text" id="phone_bill" name="phone_bill" placeholder="Enter Contact Number" required>
            </div>
          </div>
        </div>




        <script type="text/javascript">
          function ShowHideDiv() {
            var hidden_shippin_addr = document.getElementById("hidden_shippin_addr");
            hidden_shippin_addr.style.display = diff_shippin.checked ? "block" : "none";
          }
        </script>




        <input type="checkbox" id="diff_shippin" name="diff_shippin" value="different shipping address" onclick="ShowHideDiv(this)" />
        <label for="diff_shippin"> Use a different shipping address</label><br>

        <hr />

        <div class="row" id="hidden_shippin_addr" style="display: none;">
          <div class="col-25">
            <label for="shipping">Shipping Address<span class="formAsterisk">*</span></label>
          </div>
          <div class="col-75">

            <input type="text" id="shipping" name="shipping_address" placeholder="Enter Shipping Address">

            <div class="dis-flex">
              <input type="text" id="city_ship" name="city_ship" placeholder="Enter City">
              <input type="text" id="state_ship" name="state_ship" placeholder="Enter State">
            </div>

            <div class="dis-flex">

              <input type="text" id="postalCode_ship" name="postalCode_ship" placeholder="Enter Postal Code">

              <select id="country_ship" name="country_ship">
                <option value="" selected disabled>Please select a Country</option>
                <option value="AF">Afghanistan</option>
                <option value="AL">Albania</option>
                <option value="DZ">Algeria</option>
                <option value="AS">American Samoa</option>
                <option value="AD">Andorra</option>
                <option value="AO">Angola</option>
                <option value="AI">Anguilla</option>
                <option value="AQ">Antarctica</option>
                <option value="AG">Antigua and Barbuda</option>
                <option value="AR">Argentina</option>
                <option value="AM">Armenia</option>
                <option value="AW">Aruba</option>
                <option value="AU">Australia</option>
                <option value="AT">Austria</option>
                <option value="AZ">Azerbaijan</option>
                <option value="BS">Bahamas</option>
                <option value="BH">Bahrain</option>
                <option value="BD">Bangladesh</option>
                <option value="BB">Barbados</option>
                <option value="BY">Belarus</option>
                <option value="BE">Belgium</option>
                <option value="BZ">Belize</option>
                <option value="BJ">Benin</option>
                <option value="BM">Bermuda</option>
                <option value="BT">Bhutan</option>
                <option value="BO">Bolivia</option>
                <option value="BA">Bosnia and Herzegowina</option>
                <option value="BW">Botswana</option>
                <option value="BV">Bouvet Island</option>
                <option value="BR">Brazil</option>
                <option value="IO">British Indian Ocean Territory</option>
                <option value="BN">Brunei Darussalam</option>
                <option value="BG">Bulgaria</option>
                <option value="BF">Burkina Faso</option>
                <option value="BI">Burundi</option>
                <option value="KH">Cambodia</option>
                <option value="CM">Cameroon</option>
                <option value="CA">Canada</option>
                <option value="CV">Cape Verde</option>
                <option value="KY">Cayman Islands</option>
                <option value="CF">Central African Republic</option>
                <option value="TD">Chad</option>
                <option value="CL">Chile</option>
                <option value="CN">China</option>
                <option value="CX">Christmas Island</option>
                <option value="CC">Cocos (Keeling) Islands</option>
                <option value="CO">Colombia</option>
                <option value="KM">Comoros</option>
                <option value="CG">Congo</option>
                <option value="CD">Congo, the Democratic Republic of the</option>
                <option value="CK">Cook Islands</option>
                <option value="CR">Costa Rica</option>
                <option value="CI">Cote d'Ivoire</option>
                <option value="HR">Croatia (Hrvatska)</option>
                <option value="CU">Cuba</option>
                <option value="CY">Cyprus</option>
                <option value="CZ">Czech Republic</option>
                <option value="DK">Denmark</option>
                <option value="DJ">Djibouti</option>
                <option value="DM">Dominica</option>
                <option value="DO">Dominican Republic</option>
                <option value="TP">East Timor</option>
                <option value="EC">Ecuador</option>
                <option value="EG">Egypt</option>
                <option value="SV">El Salvador</option>
                <option value="GQ">Equatorial Guinea</option>
                <option value="ER">Eritrea</option>
                <option value="EE">Estonia</option>
                <option value="ET">Ethiopia</option>
                <option value="FK">Falkland Islands (Malvinas)</option>
                <option value="FO">Faroe Islands</option>
                <option value="FJ">Fiji</option>
                <option value="FI">Finland</option>
                <option value="FR">France</option>
                <option value="FX">France, Metropolitan</option>
                <option value="GF">French Guiana</option>
                <option value="PF">French Polynesia</option>
                <option value="TF">French Southern Territories</option>
                <option value="GA">Gabon</option>
                <option value="GM">Gambia</option>
                <option value="GE">Georgia</option>
                <option value="DE">Germany</option>
                <option value="GH">Ghana</option>
                <option value="GI">Gibraltar</option>
                <option value="GR">Greece</option>
                <option value="GL">Greenland</option>
                <option value="GD">Grenada</option>
                <option value="GP">Guadeloupe</option>
                <option value="GU">Guam</option>
                <option value="GT">Guatemala</option>
                <option value="GN">Guinea</option>
                <option value="GW">Guinea-Bissau</option>
                <option value="GY">Guyana</option>
                <option value="HT">Haiti</option>
                <option value="HM">Heard and Mc Donald Islands</option>
                <option value="VA">Holy See (Vatican City State)</option>
                <option value="HN">Honduras</option>
                <option value="HK">Hong Kong</option>
                <option value="HU">Hungary</option>
                <option value="IS">Iceland</option>
                <option value="IN">India</option>
                <option value="ID">Indonesia</option>
                <option value="IR">Iran (Islamic Republic of)</option>
                <option value="IQ">Iraq</option>
                <option value="IE">Ireland</option>
                <option value="IL">Israel</option>
                <option value="IT">Italy</option>
                <option value="JM">Jamaica</option>
                <option value="JP">Japan</option>
                <option value="JO">Jordan</option>
                <option value="KZ">Kazakhstan</option>
                <option value="KE">Kenya</option>
                <option value="KI">Kiribati</option>
                <option value="KP">Korea, Democratic People's Republic of</option>
                <option value="KR">Korea, Republic of</option>
                <option value="KW">Kuwait</option>
                <option value="KG">Kyrgyzstan</option>
                <option value="LA">Lao People's Democratic Republic</option>
                <option value="LV">Latvia</option>
                <option value="LB">Lebanon</option>
                <option value="LS">Lesotho</option>
                <option value="LR">Liberia</option>
                <option value="LY">Libyan Arab Jamahiriya</option>
                <option value="LI">Liechtenstein</option>
                <option value="LT">Lithuania</option>
                <option value="LU">Luxembourg</option>
                <option value="MO">Macau</option>
                <option value="MK">Macedonia, The Former Yugoslav Republic of</option>
                <option value="MG">Madagascar</option>
                <option value="MW">Malawi</option>
                <option value="MY">Malaysia</option>
                <option value="MV">Maldives</option>
                <option value="ML">Mali</option>
                <option value="MT">Malta</option>
                <option value="MH">Marshall Islands</option>
                <option value="MQ">Martinique</option>
                <option value="MR">Mauritania</option>
                <option value="MU">Mauritius</option>
                <option value="YT">Mayotte</option>
                <option value="MX">Mexico</option>
                <option value="FM">Micronesia, Federated States of</option>
                <option value="MD">Moldova, Republic of</option>
                <option value="MC">Monaco</option>
                <option value="MN">Mongolia</option>
                <option value="MS">Montserrat</option>
                <option value="MA">Morocco</option>
                <option value="MZ">Mozambique</option>
                <option value="MM">Myanmar</option>
                <option value="NA">Namibia</option>
                <option value="NR">Nauru</option>
                <option value="NP">Nepal</option>
                <option value="NL">Netherlands</option>
                <option value="AN">Netherlands Antilles</option>
                <option value="NC">New Caledonia</option>
                <option value="NZ">New Zealand</option>
                <option value="NI">Nicaragua</option>
                <option value="NE">Niger</option>
                <option value="NG">Nigeria</option>
                <option value="NU">Niue</option>
                <option value="NF">Norfolk Island</option>
                <option value="MP">Northern Mariana Islands</option>
                <option value="NO">Norway</option>
                <option value="OM">Oman</option>
                <option value="PK">Pakistan</option>
                <option value="PW">Palau</option>
                <option value="PA">Panama</option>
                <option value="PG">Papua New Guinea</option>
                <option value="PY">Paraguay</option>
                <option value="PE">Peru</option>
                <option value="PH">Philippines</option>
                <option value="PN">Pitcairn</option>
                <option value="PL">Poland</option>
                <option value="PT">Portugal</option>
                <option value="PR">Puerto Rico</option>
                <option value="QA">Qatar</option>
                <option value="RE">Reunion</option>
                <option value="RO">Romania</option>
                <option value="RU">Russian Federation</option>
                <option value="RW">Rwanda</option>
                <option value="KN">Saint Kitts and Nevis</option>
                <option value="LC">Saint LUCIA</option>
                <option value="VC">Saint Vincent and the Grenadines</option>
                <option value="WS">Samoa</option>
                <option value="SM">San Marino</option>
                <option value="ST">Sao Tome and Principe</option>
                <option value="SA">Saudi Arabia</option>
                <option value="SN">Senegal</option>
                <option value="SC">Seychelles</option>
                <option value="SL">Sierra Leone</option>
                <option value="SG">Singapore</option>
                <option value="SK">Slovakia (Slovak Republic)</option>
                <option value="SI">Slovenia</option>
                <option value="SB">Solomon Islands</option>
                <option value="SO">Somalia</option>
                <option value="ZA">South Africa</option>
                <option value="GS">South Georgia and the South Sandwich Islands</option>
                <option value="ES">Spain</option>
                <option value="LK">Sri Lanka</option>
                <option value="SH">St. Helena</option>
                <option value="PM">St. Pierre and Miquelon</option>
                <option value="SD">Sudan</option>
                <option value="SR">Suriname</option>
                <option value="SJ">Svalbard and Jan Mayen Islands</option>
                <option value="SZ">Swaziland</option>
                <option value="SE">Sweden</option>
                <option value="CH">Switzerland</option>
                <option value="SY">Syrian Arab Republic</option>
                <option value="TW">Taiwan, Province of China</option>
                <option value="TJ">Tajikistan</option>
                <option value="TZ">Tanzania, United Republic of</option>
                <option value="TH">Thailand</option>
                <option value="TG">Togo</option>
                <option value="TK">Tokelau</option>
                <option value="TO">Tonga</option>
                <option value="TT">Trinidad and Tobago</option>
                <option value="TN">Tunisia</option>
                <option value="TR">Turkey</option>
                <option value="TM">Turkmenistan</option>
                <option value="TC">Turks and Caicos Islands</option>
                <option value="TV">Tuvalu</option>
                <option value="UG">Uganda</option>
                <option value="UA">Ukraine</option>
                <option value="AE">United Arab Emirates</option>
                <option value="GB">United Kingdom</option>
                <option value="US">United States</option>
                <option value="UM">United States Minor Outlying Islands</option>
                <option value="UY">Uruguay</option>
                <option value="UZ">Uzbekistan</option>
                <option value="VU">Vanuatu</option>
                <option value="VE">Venezuela</option>
                <option value="VN">Viet Nam</option>
                <option value="VG">Virgin Islands (British)</option>
                <option value="VI">Virgin Islands (U.S.)</option>
                <option value="WF">Wallis and Futuna Islands</option>
                <option value="EH">Western Sahara</option>
                <option value="YE">Yemen</option>
                <option value="YU">Yugoslavia</option>
                <option value="ZM">Zambia</option>
                <option value="ZW">Zimbabwe</option>
              </select>

            </div>
            <div class="dis-flex">
              <input type="email" id="email_ship" name="email_ship" placeholder="Enter Email">
              <input type="text" id="phone_ship" name="phone_ship" placeholder="Enter Contact Number">
            </div>

          </div>
        </div>

        <div class="row justify-content-end">

          <div class="row justify-content-end mr-1">
            <input type="submit" name="confirm_order" value="Confirm Order" id="confirm_btn">
          </div>


        </div>
      </form>
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


  <?php

  } else {

    $resuli = $wpdb->get_results("SELECT * from wp_viberent_pagename");
    $slug_name = sanitize_title($resuli[0]->pagename);

  ?>

    <div class="pt-5 text-center my-3">Your Cart is Empty!<br>Please add items to place an order<br><br>
      <a href="<?php echo site_url() . "/" . $slug_name; ?>" class="text-center text-white m-auto btn btn-primary border-0 h4 p-1 px-3 rounded" style="font-size: 1.5rem;">Shop Now</a>
    </div>


  <?php
  }

  ?>

</body>

</html>
