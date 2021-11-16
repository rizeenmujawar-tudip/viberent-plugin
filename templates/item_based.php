<?php
session_start();
/* Template name: Viberent item-based layout */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Category based layout</title>
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="<?php echo plugins_url(); ?>/viberent/assets/css/item.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	<script>
		jQuery(document).ready(function() {
			$('select#period').change(function() {
				$('#my-dates').click();
			});
			$('#start-date').change(function() {
				$('#my-dates').click();
			});
			$('#end-date').change(function() {
				$('#my-dates').click();
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

			$('a.more', minimized_elements).click(function(event) {
				event.preventDefault();
				$(this).hide().prev().hide();
				$(this).next().show();
			});

			$('a.less', minimized_elements).click(function(event) {
				event.preventDefault();
				$(this).parent().hide().prev().show().prev().show();
			});
			var totalQuantity = $("#totalQuantity").val();
			if (totalQuantity > 0) {
				$(".btn_mycart").find("span.has-badge").attr('data-count', totalQuantity);
			} else {
				$(".btn_mycart").find("span.has-badge").attr('data-count', '0');
			}
			$(".item-category-box").each(function(index, elem) {
				var productAvailable = $(elem).find('span.product_available').text();
				var priceNotavailable = $(elem).find('.price-not-available').text();
				var rentalratesvalue = $(elem).find('.rentalratesvalue').val();
				if (productAvailable == 0) {
					$(elem).find(".add-to-cart-component  .btnAddAction").attr("disabled", true);
				}
				$(elem).find(".product-quantity").keyup(function() {
					if (parseInt($(this).val()) > parseInt(productAvailable)) {
						alert("Product available only " + productAvailable);
						$(this).val(productAvailable);
					}
				});
				$('select#period option[value="<?php if (isset($_POST["period"])) {
													echo $_POST["period"];
												} else {
													echo "Daily";
												} ?>"]').attr("selected", true);
			});

		});

		function limitText(limitField, limitNum) {
			if (limitField.value.length > limitNum) {
				limitField.value = limitField.value.substring(0, limitNum);
			}
		}
	</script>
</head>

<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">
	<?php
	$query = $_GET;

	$query_result = http_build_query($query);

	if (isset($_GET['pageno'])) {
		$page_no_cat = $_GET['pageno'];
	} else {
		$page_no_cat = 1;
	}

	if (isset($_POST["rentalratesName"])) {
		$rental_period = $_POST["rentalratesName"];
	} else {
		$rental_period = "Daily";
	}
	global $wpdb;


	if (isset($_POST["add_to_cart"]) && $_GET["action"] && isset($_GET["code"])) {
		$item_layout = array(
			"product_name" => $_POST['itemName'],
			"product_image" => $_POST['image'],
			"price" => $_POST['price'],
			"quantity" => $_POST['quantity'],
			"category_name" => "all",
			"code" => $_POST["itemCode"],
			"GUID" => $_POST["itemGUID"],
			"hireTypeID" => $_POST["hireTypeID"],
			"locationID" => $_POST["locationID"],
			"rental_period" => $rental_period,
			"startDate" => $_POST["start-date"],
			"endDate" => $_POST["end-date"]
		);
		$results = $wpdb->get_results("SELECT * FROM wp_tbl_product WHERE code ='" . $_GET["code"] . "'");

		$code = array();
		foreach ($results as $code) {
			$code = $code->code;
		}

		if ($code != $_GET["code"]) {
			$wpdb->insert('wp_tbl_product', $item_layout);
		}
	?>
		<script>
			window.setTimeout(function confirmReload() {
				var field = 'pageno';
				var url = window.location.href;
				var allCategory = window.location.href.split("&");
				if (url.indexOf('?' + field + '=') != -1) {
					window.location = allCategory[0];
					return true;
				} else if (url.indexOf('&' + "action" + '=') != -1) {
					window.location = allCategory[0];
					return true;
				} else {
					return false;
				}
			}, 3000);
		</script>
	<?php
	}
	if (!empty($_GET["action"])) {
		switch ($_GET["action"]) {
			case "add":
				if (!empty($_POST["quantity"])) {
					$productByCode = $wpdb->get_results("SELECT * FROM wp_tbl_product WHERE code='" . $_GET["code"] . "'");
					$itemArray = array($productByCode[0]->code => array(
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
						'productAvailble' => $_POST["productAvailable"],
						'endDate' => $productByCode[0]->endDate
					));

					if (!empty($_SESSION["cart_item"])) {
						if (in_array($productByCode[0]->code, array_keys($_SESSION["cart_item"]))) {
							foreach ($_SESSION["cart_item"] as $k => $v) {
								if ($productByCode[0]->code == $k) {
									if (empty($_SESSION["cart_item"][$k]["quantity"])) {
										$_SESSION["cart_item"][$k]["quantity"] = 0;
									}
									$_SESSION["cart_item"][$k]["quantity"] += $_POST["quantity"];
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
				$wpdb->query("TRUNCATE TABLE `wp_tbl_product`");
				break;
		}
	}
	?>
	<?php
	$cart_count = isset($_SESSION["cart_item"]) ? count(array_keys($_SESSION["cart_item"])) : 0;
	?>
	<input type="hidden" id="totalQuantity" value="<?php echo $cart_count; ?>">
	<?php

	if (isset($_GET["pageno"])) {
		$page_nos  = $_GET["pageno"];
	} else {
		$page_nos = 1;
	}



	$result = $wpdb->get_results("SELECT * from wp_viberent_clients_company_info");
	$curl2 = curl_init();


	$currencysymbol = $result[0]->currencysymbol;

	$dateFormatfromAPi = $result[0]->dateFormat;

	if ($dateFormatfromAPi == "dd/MM/yyyy") {
		$dateFormat = "j/m/Y";
	} else if ($dateFormatfromAPi == "MM/dd/yyyy") {
		$dateFormat = "m/j/Y";
	} else if ($dateFormatfromAPi == "MM-dd-yyyy") {
		$dateFormat = "m-j-Y";
	}


	$companyID = $result[0]->companyID;
	curl_setopt_array($curl2, array(
		CURLOPT_URL => 'https://viberent-api.azurewebsites.net/api/Item/item-list?&companyid=' . $companyID . '&pageSize=10&pageNumber=' . $page_nos,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'GET',
		CURLOPT_POSTFIELDS => array('companyid' => $companyID),
		CURLOPT_HTTPHEADER => array(
			'Cookie: ARRAffinity=79e06db539acb57119e709978d2cf1da299e8341753d6f6345007fcab3f69bc5; ARRAffinitySameSite=79e06db539acb57119e709978d2cf1da299e8341753d6f6345007fcab3f69bc5'
		),
	));

	$response2 = curl_exec($curl2);

	curl_close($curl2);

	$resp2 = json_decode($response2, 1);


	$curlperiod = curl_init();

	curl_setopt_array($curlperiod, array(
		CURLOPT_URL => 'https://viberent-api.azurewebsites.net/api/item/rental-periodtype?companyid=' . $companyID,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'GET',
		CURLOPT_POSTFIELDS => array(),
		CURLOPT_HTTPHEADER => array(
			'Cookie: ARRAffinity=79e06db539acb57119e709978d2cf1da299e8341753d6f6345007fcab3f69bc5; ARRAffinitySameSite=79e06db539acb57119e709978d2cf1da299e8341753d6f6345007fcab3f69bc5'
		),
	));

	$responseperiod = curl_exec($curlperiod);

	curl_close($curlperiod);

	$respperiod = json_decode($responseperiod, 1);
	$startFrom_date = date("Y-m-d");
	$startEnd_date = date("Y-m-d", strtotime('+1 day'));

	if ($dateFormatfromAPi == "dd/MM/yyyy") {
		$date_Format = "DD/MM/YYYY";
	} else if ($dateFormatfromAPi == "MM/dd/yyyy") {
		$date_Format = "MM/DD/YYYY";
	} else if ($dateFormatfromAPi == "MM-dd-yyyy") {
		$date_Format = "MM-DD-YYYY";
	}
	?>

	<div id="main-container" class="main-container container">
		<div>
			<div id="my-shopping-cart">

				<div id="my_shop_nav">
					<a id="btn_mycart" class="btn_mycart" target="_blank" href="<?php echo site_url() . "/index.php/my-cart/" ?>">
						<span class="fa-stack fa-2x has-badge cart" data-count="0">
							<i class="fa fa-circle fa-stack-2x"></i>
							<i class="fa fa-shopping-cart fa-stack-1x fa-inverse"></i>
						</span>
					</a>
				</div>

			</div>
		</div>
		<div class="row">
			<div class="col-lg-3 col-sm-12 col-md-12">
				<div class="rental-period">
					<form method="post">

						<label for="period">Choose Rental Period:</label>
						<select name="period" id="period" required>

							<?php
							foreach ($respperiod as $retrieved_period) {
							?>
								<option value="<?php echo $retrieved_period["name"]; ?>"><?php echo $retrieved_period["name"]; ?></option>
							<?php
							}
							?>
						</select>
						<br>

						<label for="start-date">Start Date:</label>
						<input type="date" data-date="" data-date-format="<?php echo $date_Format; ?>" value="<?php if (isset($_POST['start-date'])) {
																													echo $_POST['start-date'];
																												} else {
																													echo date("Y-m-d", strtotime($startFrom_date));
																												} ?>" id="start-date" name="start-date" placeholder="Select Start Date" required><br />

						<label for="end-date">End Date:</label>
						<input type="date" data-date="" data-date-format="<?php echo $date_Format; ?>" value="<?php if (isset($_POST['end-date'])) {
																													echo $_POST['end-date'];
																												} else {
																													echo date("Y-m-d", strtotime($startEnd_date));
																												} ?>" id="end-date" name="end-date" placeholder="Select End Date" required>
						<button type="submit" name="my-dates" id="my-dates" style="visibility: hidden;">Check Availability</button>
					</form>

				</div>
			</div>
			<script>
				$("input[type='date']").on("change", function() {
					this.setAttribute(
						"data-date",
						moment(this.value, "YYYY-MM-DD")
						.format(this.getAttribute("data-date-format"))
					)
				}).trigger("change")
			</script>

			<div class="col-lg-9 col-sm-12 col-md-12">

				<h2 class="new-booking">New Booking: <span>

						<?php
						if (isset($_POST["period"])) {
							$rentalPeriod = $_POST["period"];
						} else {
							$rentalPeriod = "Daily";
						}

						$my_from_date = date("j/M/Y");
						$my_to_date = date("j/M/Y", strtotime('+1 day'));

						$show_from_date = date($dateFormat);
						$show_to_date = date($dateFormat, strtotime('+1 day'));

						$start_from_date = date("Y-m-d");
						$end_to_date = date("Y-m-d", strtotime('+1 day'));


						if (isset($_POST["my-dates"])) {

							$my_from_date = $_POST["start-date"];
							$my_to_date = $_POST["end-date"];

							$show_from_date = date($dateFormat, strtotime($_POST["start-date"]));
							$show_to_date = date($dateFormat, strtotime($_POST["end-date"]));

							$start_from_date = date('Y-m-d', strtotime($_POST["start-date"]));
							$end_to_date = date('Y-m-d', strtotime($_POST["end-date"]));
						}

						echo $show_from_date . ' - ' . $show_to_date;

						?>

					</span> </h2>

				<?php

				if (isset($resp2)) {
					foreach ($resp2 as $retrieved_data) {
						$curlavail = curl_init();
						curl_setopt_array($curlavail, array(
							CURLOPT_URL => 'https://viberent-api.azurewebsites.net/api/Item/item-availability?itemGUID=' . $retrieved_data["itemGUID"] . '&companyid=' . $companyID . '&fromDate=' . $my_from_date . '&todate=' . $my_to_date . '&locationID=0',
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_ENCODING => '',
							CURLOPT_MAXREDIRS => 10,
							CURLOPT_TIMEOUT => 0,
							CURLOPT_FOLLOWLOCATION => true,
							CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							CURLOPT_CUSTOMREQUEST => 'GET',
							CURLOPT_POSTFIELDS => array(),
							CURLOPT_HTTPHEADER => array(
								'Cookie: ARRAffinity=79e06db539acb57119e709978d2cf1da299e8341753d6f6345007fcab3f69bc5; ARRAffinitySameSite=79e06db539acb57119e709978d2cf1da299e8341753d6f6345007fcab3f69bc5'
							),
						));

						$responseavail = curl_exec($curlavail);

						curl_close($curlavail);

						$respavail = json_decode($responseavail, 1);
						// echo "<pre>";
						// print_r($respavail);
						// echo "</pre>";

				?>
						<div class="item-category-box ng-star-inserted">
							<form method="post" action="<?php echo site_url();
														if (isset($query['pageno'])) { ?>/viberent-based-listing?pageno=<?php echo $query['pageno'];
																													} else { ?>/viberent-based-listing?pageno=1<?php } ?>&action=add&code=<?php echo $retrieved_data['itemCode']; ?>">
								<div class="item-info">

									<div class="item-details">

										<div class="product-title">
											<h2 class="product-name"><?php echo $retrieved_data["itemName"]; ?></h2><br>
											<h2 class="product-pricing">
												<?php
												if (isset($_POST["period"])) {
													$my_rental_period = $_POST["period"];
												} else {
													$my_rental_period = "Daily";
												}
												if (isset($_POST["my-dates"])) {
													$is_present = 0;
													$i = 1;
													foreach ($retrieved_data["rentalRates"] as $rentalRate) {
														if ($rentalRate['rentalratesName'] == $my_rental_period) {
															echo $currencysymbol;
															echo $rentalRate['rentalratesvalue'];
															echo " - ";
															echo $my_rental_period;
															$is_present = 1;
															if ($i == 1) {
												?>
																<input type="hidden" name="price" class="rentalratesvalue" value="<?php echo $rentalRate['rentalratesvalue']; ?>" />
														<?php
															}
															$i++;
														} ?>

													<?php
													}
													if ($is_present !== 1) {

													?><span class="price-not-available"><?php echo "pricing not available"; ?></span>
														<input type="hidden" name="price" class="rentalratesvalue" value="<?php echo 0; ?>" />
														<?php
													}
												} else {
													$is_daily = 0;
													$i = 1;
													foreach ($retrieved_data["rentalRates"] as $rentalRate) {
														if ($rentalRate['rentalratesName'] == "Daily") {
															echo $currencysymbol;
															echo $rentalRate['rentalratesvalue'];
															echo " - Daily";
															$is_daily = 1;
															if ($i == 1) {
														?>
																<input type="hidden" name="price" class="rentalratesvalue" value="<?php echo $rentalRate['rentalratesvalue']; ?>" />
														<?php
															}
															$i++;
														}
														?>
													<?php
													}
													if ($is_daily !== 1) {

													?><div class="price-not-available"><?php echo "pricing not available"; ?></div>
														<input type="hidden" name="price" class="rentalratesvalue" value="<?php echo 0; ?>" />
												<?php
													}
												}
												?>
											</h2>
										</div>

										<p class="product-available">
											<?php
											echo "Available: <span class='product_available'>" . $respavail[0]['available'] . "</span>";
											?>
										</p>

										<div class="add-to-cart-component buy-items-btn ng-star-inserted">
											<input type=" text" class="product-quantity" name="quantity" value="1" size="2" /><input type="submit" name="add_to_cart" value="Add to Cart" class="btnAddAction" />
										</div>

										<div class="product-quantity-message">
											<?php
											if (isset($_SESSION["cart_item"])) {
												foreach ($_SESSION["cart_item"] as $item) {
													if ($item["productAvailble"] >= $item["quantity"]) {
														$productAvailable = $item["quantity"];
													} else {
														$productAvailable = $item["productAvailble"];
													}
													if ($retrieved_data['itemCode'] == $item['code']) {
														echo $productAvailable . " item(s) added to cart";
													}
												}
											}
											?>
										</div>

										<div class="item-summary">

											<p class="minimize"><?php echo $retrieved_data["itemDescription"]; ?></p>

										</div>

									</div>

									<div class="product-image">
										<img src=<?php
													if (empty($retrieved_data["images"])) {
														echo "https://viberent.blob.core.windows.net/attachement/no_image.png";
													} else {
														$count = 0;
														foreach ($retrieved_data["images"] as $image) {
															if ($count == 0) {
																echo $image['blobUrl'];
															}
															$count++;
														}
													}
													?>>
									</div>

								</div>
								<input type="hidden" name="image" value="<?php if (empty($retrieved_data["images"])) {
																				echo "https://viberent.blob.core.windows.net/attachement/no_image.png";
																			} else {
																				$count = 0;
																				foreach ($retrieved_data["images"] as $image) {
																					if ($count == 0) {
																						echo $image['blobUrl'];
																					}
																					$count++;
																				}
																			}
																			?>" />
								<input type="hidden" name="productAvailable" value="<?php echo $respavail[0]['available']; ?>" />
								<input type="hidden" name="itemCode" value="<?php echo $retrieved_data['itemCode']; ?>" />
								<input type="hidden" name="itemGUID" value="<?php echo $retrieved_data['itemGUID']; ?>" />
								<input type="hidden" name="hireTypeID" value="<?php echo $retrieved_data['hireTypeID']; ?>" />
								<input type="hidden" name="locationID" value="<?php echo $retrieved_data['locationID']; ?>" />
								<input type="hidden" name="itemName" value="<?php echo $retrieved_data["itemName"]; ?>" />
								<input type="hidden" name="rentalratesName" value="<?php echo $rentalPeriod; ?>" />
								<input type="hidden" name="start-date" value="<?php echo $start_from_date; ?>" />
								<input type="hidden" name="end-date" value="<?php echo $end_to_date; ?>" />
							</form>
							<hr>
						</div>


				<?php
					}


					$are_pages = $retrieved_data["totalRows"] % 10;
					$not_final_pages = intval($retrieved_data["totalRows"] / 10);


					if ($are_pages == 0) {
						$total_pages = $not_final_pages;
					} else {
						$total_pages = $not_final_pages + 1;
					}
				}

				?>





				<div class="pagination">
					<ul>
						<?php
						$query = $_GET;
						$pagLink = "";

						if ($total_pages > 1) {
							if ($page_nos >= 2) {
								echo "<li class='prev'><span><a href='" . site_url() . "/viberent-based-listing?pageno=" . ($page_nos - 1) . "'>Prev</a></span></li>";
							}

							for ($x = 1; $x <= $page_nos; $x++) {
								$query['pageno'] =  $x;
								$query_result = http_build_query($query);
								if ($x == $page_nos) {
									$pagLink .= "<li class='active'><span><a href='" . site_url() . "/viberent-based-listing?pageno="
										. $x . "'>" . $x . " </a></span></li>";
								} else {
									$pagLink .= "<li><span><a href='" . site_url() . "/viberent-based-listing?pageno=" . $x . "'>   
                                                        " . $x . " </a></span></li>";
								}
							}

							if ($page_nos < $total_pages) {
								$pagLink .= '<li class="disabled"><span>...</span></li>';
								$pagLink .= "<li><span><a href='" . site_url() . "/viberent-based-listing?pageno=" . ($page_nos + 1) . "'>Next</a></span></li>";
							}
							echo $pagLink;
						}
						?>

					</ul>
				</div>

			</div>
		</div>

	</div>
</body>
</html>