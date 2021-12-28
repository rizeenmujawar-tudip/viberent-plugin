<?php
session_start();
/* Template name: Viberent item-based layout */
require_once('item_page.php');
require_once('head.php');
?>

<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">
	<link rel="stylesheet" href="<?php echo plugins_url(); ?>/viberent/assets/css/item.css" type="text/css" media="screen" />
	<nav id="my_main_nav" class="navbar navbar-light bg-light sticky-top justify-content-between py-0 px-2 px-sm-5">
		<a class="navbar-brand p-0" href="#"><?php echo "<img class='logo-image' src='data:image/jpeg;base64, $logo' />"; ?></a>
		<a id="btn_mycart" class="btn_mycart pt-1" href="<?php echo site_url() . "/my-cart/" ?>">
			<span class="fa-stack fa-2x has-badge cart" data-count="0">
				<i class="fa fa-shopping-cart fa-stack-1x"></i>
			</span>
		</a>
	</nav>

	<div id="main-container" class="container-fluid px-2 px-sm-5 py-4">
		<div class="row">
			<div class="col-sm-12 col-md-4 col-lg-3">
				<div class="rental-period">
					<form method="post" class="pt-3">

						<label for="period">Rental Period:</label>
						<select name="period" id="period" required>

							<?php
							foreach ($respperiod as $retrieved_period) {
							?>
								<option value="<?php echo esc_attr($retrieved_period["name"]); ?>"><?php echo esc_html($retrieved_period["name"]); ?></option>
								<script>
									function convert(str) {
										var date = new Date(str),
											mnth = ("0" + (date.getMonth() + 1)).slice(-2),
											day = ("0" + date.getDate()).slice(-2);
										return [date.getFullYear(), mnth, day].join("-");
									}

									function formatDateNew(date) {
										var d = new Date(date),
											month = '' + (d.getMonth() + 1),
											day = '' + d.getDate(),
											year = d.getFullYear();

										if (month.length < 2)
											month = '0' + month;
										if (day.length < 2)
											day = '0' + day;

										return [year, month, day].join('-');
									}
									jQuery('document').ready(function($) {
										// alert(convert("Fri Dec 24 2021 00:00:00 GMT+0530 (India Standard Time)"));
										var dtToday = new Date();
										var month = dtToday.getMonth() + 1;
										var day = dtToday.getDate();
										var year = dtToday.getFullYear();
										if (month < 10)
											month = '0' + month.toString();
										if (day < 10)
											day = '0' + day.toString();
										var maxDate = year + '-' + month + '-' + day;
										$('#start-date').attr('min', maxDate);
										$('#end-date').attr('min', maxDate);
										$('select#period').change(function() {
											var period = $(this).val().trim();
											var rental_period = '<?php echo $retrieved_period["name"] ?>';
											if (rental_period == period) {
												var start_date = $("#start-date").val();
												var exclude = '<?php echo $retrieved_period["value"] ?>';
												if ((period == "Exclude Sat / Sun")) {
													start_date = new Date(start_date);
													var endDate = "",
														noOfDaysToAdd = parseInt(exclude),
														count = 1;
													if (start_date.getDay() == 6 || start_date.getDay() == 0) {
														count = 0;
														while (count < noOfDaysToAdd) {
															endDate = new Date(start_date.setDate(start_date.getDate() + 1));
															if (endDate.getDay() != 0 && endDate.getDay() != 6) {
																count++;
															}
														}
													} else {
														while (count < noOfDaysToAdd) {
															endDate = new Date(start_date.setDate(start_date.getDate() + 1));
															if (endDate.getDay() != 0 && endDate.getDay() != 6) {
																count++;
															}
														}
													}

													$("#end-date").val(convert(endDate));
												} else if (period == "Exclude Sat / Sun Daily") {
													var start_date = new Date(start_date);
													var endDate = "";
													if (start_date.getDay() == 6) {
														endDate = new Date(start_date.setDate(start_date.getDate() + 2));
													} else if (start_date.getDay() == 0) {
														endDate = new Date(start_date.setDate(start_date.getDate() + 1));
													} else {
														endDate = new Date(start_date.setDate(start_date.getDate() + parseInt(exclude) - 1));
													}

													$("#end-date").val(convert(endDate));
												} else if (period == "Exclude Sun") {
													start_date = new Date(start_date.replace(/-/g, "/"));
													var endDate = "",
														noOfDaysToAdd = parseInt(exclude),
														count = 1;

													if (start_date.getDay() == 0) {
														endDate = new Date(start_date.setDate(start_date.getDate() + parseInt(exclude)));
													} else {
														while (count < noOfDaysToAdd) {
															endDate = new Date(start_date.setDate(start_date.getDate() + 1));
															if (endDate.getDay() != 0) {
																//Date.getDay() gives weekday starting from 0(Sunday) to 6(Saturday)
																count++;
															}
														}
													}
													//alert(endDate);
													$("#end-date").val(convert(endDate));
												} else if (period == "Daily") {
													var someDate = new Date(start_date);
													var dateFormated = someDate.setDate(someDate.getDate());
													var dateFormated = someDate.toISOString().substr(0, 10);
													$("#end-date").val(dateFormated);
												} else if (period == "Monthly") {
													var getFullYear = start_date.substr(0, 4);
													var getMonth = start_date.substr(5, 2);
													var getDate = start_date.substr(8, 2);

													var endDate = "";
													getMonth = parseInt(getMonth)
													var lastday = function(y, m) {
														return new Date(y, m + 0, 0).getDate();
													}

													if (getDate == 31 && getMonth == 01) {
														var exclude_date = 29;
													} else if (getDate == 31 && ((getMonth == 03) || (getMonth == 05) || getMonth == 08 || getMonth == 10 || getMonth == 11)) {
														var exclude_date = 30;
													} else if (getDate == 30 && getMonth == 09) {
														var exclude_date = 30;
													} else {
														//alert(lastday(getFullYear, getMonth));
														var exclude_date = lastday(getFullYear, getMonth);
													}
													var someDate = new Date(start_date);
													var dateFormated = someDate.setDate(someDate.getDate() + (parseInt(exclude_date) - 1));
													var dateFormated = someDate.toISOString().substr(0, 10);
													$("#end-date").val(dateFormated);
												} else {
													var someDate = new Date(start_date);
													var dateFormated = someDate.setDate(someDate.getDate() + parseInt(exclude) - 1);
													var dateFormated = someDate.toISOString().substr(0, 10);
													$("#end-date").val(dateFormated);
												}
											}
											$('#my-dates').click();
											var startDate = $("#start-date").val();
											localStorage.setItem('startDate', startDate);
											var endDate = $("#end-date").val();
											localStorage.setItem('endDate', endDate);
										});
										$('#start-date').change(function() {
											var start_date = $(this).val();
											var rental_period = '<?php echo $retrieved_period["name"] ?>';
											var exclude = '<?php echo $retrieved_period["value"] ?>';
											var period = $("#period").val();

											if (rental_period == period) {
												if (period == "Exclude Sat / Sun") {
													start_date = new Date(start_date);
													var endDate = "",
														noOfDaysToAdd = parseInt(exclude),
														count = 1;
													if (start_date.getDay() == 6 || start_date.getDay() == 0) {
														count = 0;
														while (count < noOfDaysToAdd) {
															endDate = new Date(start_date.setDate(start_date.getDate() + 1));
															if (endDate.getDay() != 0 && endDate.getDay() != 6) {
																count++;
															}
														}
													} else {
														while (count < noOfDaysToAdd) {
															endDate = new Date(start_date.setDate(start_date.getDate() + 1));
															if (endDate.getDay() != 0 && endDate.getDay() != 6) {
																count++;
															}
														}
													}

													$("#end-date").val(convert(endDate));
												} else if (period == "Exclude Sat / Sun Daily") {
													var start_date = new Date(start_date);
													var endDate = "";
													if (start_date.getDay() == 6) {
														endDate = new Date(start_date.setDate(start_date.getDate() + 2));
													} else if (start_date.getDay() == 0) {
														endDate = new Date(start_date.setDate(start_date.getDate() + 1));
													} else {
														endDate = new Date(start_date.setDate(start_date.getDate() + parseInt(exclude) - 1));
													}

													$("#end-date").val(convert(endDate));
												} else if (period == "Exclude Sun") {
													start_date = new Date(start_date.replace(/-/g, "/"));
													var endDate = "",
														noOfDaysToAdd = parseInt(exclude),
														count = 1;

													if (start_date.getDay() == 0) {
														endDate = new Date(start_date.setDate(start_date.getDate() + parseInt(exclude)));
													} else {
														while (count < noOfDaysToAdd) {
															endDate = new Date(start_date.setDate(start_date.getDate() + 1));
															if (endDate.getDay() != 0) {
																//Date.getDay() gives weekday starting from 0(Sunday) to 6(Saturday)
																count++;
															}
														}
													}
													//alert(endDate);
													$("#end-date").val(convert(endDate));
												} else if (period == "Daily") {
													var someDate = new Date(start_date);
													var dateFormated = someDate.setDate(someDate.getDate());
													var dateFormated = someDate.toISOString().substr(0, 10);
													$("#end-date").val(dateFormated);
												} else if (period == "Monthly") {
													var getFullYear = start_date.substr(0, 4);
													var getMonth = start_date.substr(5, 2);
													var getDate = start_date.substr(8, 2);

													var endDate = "";
													getMonth = parseInt(getMonth)
													var lastday = function(y, m) {
														return new Date(y, m + 0, 0).getDate();
													}

													if (getDate == 31 && getMonth == 01) {
														var exclude_date = 29;
													} else if (getDate == 31 && ((getMonth == 03) || (getMonth == 05) || getMonth == 08 || getMonth == 10 || getMonth == 11)) {
														var exclude_date = 30;
													} else if (getDate == 30 && getMonth == 09) {
														var exclude_date = 30;
													} else {
														//alert(lastday(getFullYear, getMonth));
														var exclude_date = lastday(getFullYear, getMonth);
													}
													var someDate = new Date(start_date);
													var dateFormated = someDate.setDate(someDate.getDate() + (parseInt(exclude_date) - 1));
													var dateFormated = someDate.toISOString().substr(0, 10);
													$("#end-date").val(dateFormated);
												} else {
													var someDate = new Date(start_date);
													var dateFormated = someDate.setDate(someDate.getDate() + parseInt(exclude) - 1);
													var dateFormated = someDate.toISOString().substr(0, 10);
													$("#end-date").val(dateFormated);
												}
											}
											$('#my-dates').click();
											var startDate = $("#start-date").val();
											localStorage.setItem('startDate', startDate);
											var endDate = $("#end-date").val();
											localStorage.setItem('endDate', endDate);
										});
										if ((localStorage.getItem('startDate'))) {
											var startDate = $("#start-date").val();
											if ((localStorage.getItem('startDate') != startDate)) {
												$("#start-date").val(formatDateNew(localStorage.getItem('startDate')));
												$('#my-dates').click();
											}
										}
										if ((localStorage.getItem('endDate'))) {
											var endDate = $("#end-date").val();
											if ((localStorage.getItem('endDate') != endDate)) {
												$("#end-date").val(formatDateNew(localStorage.getItem('endDate')));
												$('#my-dates').click();
											}
										}
									});
								</script>
							<?php
							}
							?>
						</select>


						<label for="start-date">Start Date:</label>
						<input type="date" data-date="" data-date-format="<?php echo esc_attr($date_Format); ?>" value="<?php if (isset($_POST['start-date'])) {
																															echo esc_attr($_POST['start-date']);
																														} else {
																															echo esc_attr($startFrom_date);
																														} ?>" id="start-date" name="start-date" placeholder="Select Start Date" required>

						<label for="end-date">End Date:</label>
						<input type="date" data-date="" data-date-format="<?php echo esc_attr($date_Format); ?>" value="<?php if (isset($_POST['end-date'])) {
																															echo esc_attr($_POST['end-date']);
																														} else {
																															echo esc_attr($startEnd_date);
																														} ?>" id="end-date" name="end-date" placeholder="Select End Date" required>
						<button class="p-0" type="submit" name="my-dates" id="my-dates" style="visibility: hidden;">Check Availability</button>
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

			<div class="col-sm-12 col-md-8 col-lg-9">

				<h5 class="new-booking">New Booking: <span>

						<?php
						if (isset($_POST["period"])) {
							$rentalPeriod = sanitize_text_field($_POST["period"]);
						} else {
							$rentalPeriod = sanitize_text_field($firstRental_period);
						}
						$my_from_date = date("j/M/Y");
						$my_to_date = date("Y-m-d", strtotime($firstRental_showValue));

						$show_from_date = date($dateFormat);
						$show_to_date = date($dateFormat, strtotime($firstRental_showValue));

						if (isset($_POST["my-dates"])) {
							$my_from_date = sanitize_text_field($_POST["start-date"]);
							$my_to_date = sanitize_text_field($_POST["end-date"]);

							$show_from_date = date($dateFormat, strtotime($_POST["start-date"]));
							$show_to_date = date($dateFormat, strtotime($_POST["end-date"]));

							$start_from_date = date('Y-m-d', strtotime($_POST["start-date"]));
							$end_to_date = date('Y-m-d', strtotime($_POST["end-date"]));
						} else {
							$start_from_date = date("Y-m-d");
							$end_to_date = date("Y-m-d", strtotime($firstRental_showValue));
						}

						echo esc_html($show_from_date . ' - ' . $show_to_date);

						?>

					</span> </h5>

				<?php

				if (isset($resp2)) {
					foreach ($resp2 as $retrieved_data) {
						$curlavail = wp_remote_get('https://viberent-api.azurewebsites.net/api/Item/item-availability?itemGUID=' . $retrieved_data["itemGUID"] . '&companyid=' . $companyID . '&fromDate=' . $my_from_date . '&todate=' . $my_to_date . '&PeriodTypeId=27&locationID=0');

						    if (is_wp_error($curlavail) || wp_remote_retrieve_response_code($curlavail) != 200) {
						      return false;
						    }

						$responseavail = wp_remote_retrieve_body($curlavail);
						$respavail = json_decode($responseavail, 1);
				?>
						<div class="item-category-box ng-star-inserted px-3 px-xl-5">
							<form method="post" action="<?php echo site_url();
														if (isset($query['pageno'])) { ?>/<?php echo $mypagename; ?>?pageno=<?php echo $query['pageno'];
														} else { ?>/<?php echo $mypagename; ?>?pageno=1<?php } ?>&action=add&GUID=<?php echo trim($retrieved_data['itemGUID']); ?>&rental_period=<?php echo $rentalPeriod; ?>">
								<div class="item-info">

									<div class="item-details">

										<div class="product-title">
											<h5 class="product-name"><?php echo $retrieved_data["itemName"]; ?></h5>
											<h5 class="product-pricing m-0">
												<?php
												if (isset($_POST["period"])) {
													$my_rental_period = sanitize_text_field($_POST["period"]);
												} else {
													$my_rental_period = sanitize_text_field($firstRental_period);
												}
												if (isset($_POST["my-dates"])) {
													$is_present = 0;
													$i = 1;
													foreach ($retrieved_data["rentalRates"] as $rentalRate) {
														if ($rentalRate['rentalratesName'] == $my_rental_period) {
															echo $currencysymbol;
															echo $rentalRate['rentalratesvalue'];
															echo " : " . $my_rental_period;
															$is_present = 1;
															if ($i == 1) {
												?>
																<input type="hidden" name="price" class="rentalratesvalue" value="<?php echo esc_attr($rentalRate['rentalratesvalue']); ?>" />
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
														if ($rentalRate['rentalratesName'] == $firstRental_period) {
															echo $currencysymbol;
															echo $rentalRate['rentalratesvalue'];
															echo " : " . $firstRental_period;
															$is_daily = 1;
															if ($i == 1) {
														?>
																<input type="hidden" name="price" class="rentalratesvalue" value="<?php echo esc_attr($rentalRate['rentalratesvalue']); ?>" />
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
											</h5>
										</div>

										<p class="product-available pt-0">
											<?php
											echo "Available: <span class='product_available'>" . esc_attr($respavail[0]['available']) . "</span>";
											?>
										</p>

										<div class="add-to-cart-component buy-items-btn ng-star-inserted">
											<input type="number" class="product-quantity" name="quantity" min="1" value="1" size="2" /><input type="submit" name="add_to_cart" value="Add to Cart" class="btnAddAction" />
										</div>

										<div class="product-quantity-message">
											<?php
											if (isset($_SESSION["cart_item"])) {
												foreach ($_SESSION["cart_item"] as $k => $item) {
													if ($item["productAvailble"] >= $item["quantity"]) {
														$productAvailable = $item["quantity"];
													} else {
														$productAvailable = $item["productAvailble"];
													}
													if ($retrieved_data['itemGUID'] == $item['GUID'] && $rentalPeriod == $item['rental_period']) {
														echo "<b>" . esc_html($productAvailable) . " item(s) added to cart</b>";
													}
												}
											}
											?>
										</div>

										<div class="item-summary">

											<p class="minimize"><?php echo esc_html($retrieved_data["itemDescription"]); ?></p>

										</div>

									</div>

									<div class="product-image">
										<img src=<?php
													if (empty($retrieved_data["images"])) {
														echo $full_path . 'assets/images/no_image.png';;
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
																				echo $full_path . 'assets/images/no_image.png';;
																			} else {
																				$count = 0;
																				foreach ($retrieved_data["images"] as $image) {
																					if ($count == 0) {
																						echo esc_attr($image['blobUrl']);
																					}
																					$count++;
																				}
																			}
																			?>" />
								<input type="hidden" name="productAvailable" value="<?php echo esc_attr($respavail[0]['available']); ?>" />
								<input type="hidden" name="itemCode" value="<?php echo esc_attr($retrieved_data['itemCode']); ?>" />
								<input type="hidden" name="itemGUID" value="<?php echo esc_attr($retrieved_data['itemGUID']); ?>" />
								<input type="hidden" name="hireTypeID" value="<?php echo esc_attr($retrieved_data['hireTypeID']); ?>" />
								<input type="hidden" name="locationID" value="<?php echo esc_attr($retrieved_data['locationID']); ?>" />
								<input type="hidden" name="itemName" value="<?php echo esc_attr($retrieved_data["itemName"]); ?>" />
								<input type="hidden" name="rentalratesName" value="<?php echo esc_attr($rentalPeriod); ?>" />
								<input type="hidden" name="start-date" value="<?php echo esc_attr($start_from_date); ?>" />
								<input type="hidden" name="end-date" value="<?php echo esc_attr($end_to_date); ?>" />
								<input type="hidden" name="sessionID" value="<?php echo trim($retrieved_data['itemGUID'] . $rentalPeriod); ?>" />
							</form>
						</div>


					<?php
					}


					$are_pages = $retrieved_data["totalRows"] % 10;
					$not_final_pages = intval($retrieved_data["totalRows"] / 10);


					if ($are_pages == 0) {
						$total_pages = (int)$not_final_pages;
					} else {
						$total_pages = (int)$not_final_pages + 1;
					}
				}
				if (isset($total_pages)) {
					?>
					<div class="pagination">
						<ul>
						<?php
						$query = $_GET;
						$pagLink = "";

						if ($total_pages > 1) {
							if ($page_nos >= 2) {
								echo "<li class='prev'><span><a href='" . site_url() . "/" . $mypagename . "?pageno=" . ($page_nos - 1) . "'>Prev</a></span></li>";
							}

							for ($x = 1; $x <= $page_nos; $x++) {
								$query['pageno'] =  $x;
								$query_result = http_build_query($query);
								if ($x == $page_nos) {
									$pagLink .= "<li class='active'><span><a href='" . site_url() . "/" . $mypagename . "?pageno="
										. $x . "'>" . $x . " </a></span></li>";
								} else {
									$pagLink .= "<li><span><a href='" . site_url() . "/" . $mypagename . "?pageno=" . $x . "'>   
                                                        " . $x . " </a></span></li>";
								}
							}

							if ($page_nos < $total_pages) {
								$pagLink .= '<li class="disabled"><span>...</span></li>';
								$pagLink .= "<li><span><a href='" . site_url() . "/" . $mypagename . "?pageno=" . ($page_nos + 1) . "'>Next</a></span></li>";
							}
							echo $pagLink;
						}
					}
						?>

						</ul>
					</div>

			</div>
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
			<p class="font-italic text-muted">This loading window will be removed after <strong class="countdown text-dark font-weight-bold">7 </strong> Seconds</p>
		</div>
	</div>
</body>

</html>