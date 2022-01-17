<?php
session_start();
/* Template name: Viberent category-based layout */
require_once('category_page.php');
get_header();
?>
<link rel="stylesheet" href="<?php echo plugin_dir_url('category.css', __FILE__ ); ?>viberent/assets/css/category.css" type="text/css" media="screen" />
<script>
    jQuery('document').ready(function($) {
        $(".item-category-box").each(function(index, elem) {
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
        var totalQuantity = $("#totalQuantity").val();
        if (totalQuantity > 0) {
            $(".btn_mycart").find("span.has-badge").attr('data-count', totalQuantity);
        } else {
            $(".btn_mycart").find("span.has-badge").attr('data-count', '0');
        }
    });
</script>
<div class="viberent_category_layout">
    <div id="main-container" class="container px-2 px-sm-5 py-5">
        <div class="d-flex justify-content-end">
            <a class="btn_mycart pt-1" href="<?php echo site_url() . "/my-cart/" ?>">
                <span class="fa-stack fa-2x has-badge cart" data-count="0">
                    <i class="fa fa-shopping-cart fa-stack-1x"></i>
                </span>
            </a>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-4 col-lg-3">
                <div class="availablity-section">
                    <form method="post" class="pt-3">
                        <label for="period">Rental Period:</label>
                        <select name="period" id="period">
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
                        <input type="date" data-date="" data-date-format="<?php echo esc_attr($date_Format); ?>" value="<?php if (isset($_POST['start-date'])) { echo esc_attr($_POST['start-date']); } else { echo esc_attr($startFrom_date); } ?>" id="start-date" name="start-date" placeholder="Select Start Date" required>
                        <label for="end-date">End Date:</label>
                        <input type="date" data-date="" data-date-format="<?php echo esc_attr($date_Format); ?>" value="<?php if (isset($_POST['end-date'])) { echo esc_attr($_POST['end-date']); } else { echo esc_attr($startEnd_date); } ?>" id="end-date" name="end-date" placeholder="Select End Date" required>
                        <button class="p-0" type="submit" name="my-dates" id="my-dates" style="visibility: hidden;">Check Availability</button>
                    </form>
                </div>
                <script>
                    jQuery("input[type='date']").on("change", function() {
                        this.setAttribute(
                            "data-date",
                            moment(this.value, "YYYY-MM-DD")
                            .format(this.getAttribute("data-date-format"))
                        )
                    }).trigger("change")
                </script>
                <?php
                if (isset($_GET['category'])) {
                    $sucategoryName = sanitize_text_field($_GET['category']);
                } else {
                    $sucategoryName = "";
                }
                ?>
                <?php
                if (isset($_GET["pageno"])) {
                    $page_nos  = sanitize_text_field($_GET["pageno"]);
                } else {
                    $page_nos = 1;
                }
                $curlall = wp_remote_get('https://viberent-api.azurewebsites.net/api/Item/item-list?&companyid=' . $companyID . '&pageSize=10&pageNumber=' . $page_nos, $api_args);
                if (is_wp_error($curlall) || wp_remote_retrieve_response_code($curlall) != 200) {
                    return false;
                }
                $response3 = wp_remote_retrieve_body($curlall);
                $resp3 = json_decode($response3, 1);
                if (isset($resp3)) {
                ?>
                    <div class="categories">
                        <h6 class="heading_category">Categories</h6>
                        <ul>
                            <li class="active">
                                <a href="<?php echo site_url(); ?>/<?php echo $mypagename; ?>/?category=all&pageno=1" class="all_category_btn" name="selected_category_btn">All Categories</a>
                            </li>
                            <?php
                            foreach ($resp_body as $retrieved_data1) {
                                $retrieved_data_query = str_replace(' ', '%20',$retrieved_data1["subCategoryName"]);
                            ?>
                                <li class="<?php if ($sucategoryName == $retrieved_data1["subCategoryName"]) {
                                                echo 'active';
                                            } ?>">
                                    <a href=<?php echo site_url() . "/" . $mypagename . "/?category=" . $retrieved_data_query . "&pageno=1" ?> class="selected_category_btn" name="selected_category_btn"><?php echo esc_html($retrieved_data1["subCategoryName"]); ?></a>
                                <?php
                            }
                            ?>
                            </li>
                        </ul>
                    </div>
            </div>
            <div class="col-sm-12 col-md-8 col-lg-9">
                <?php
                    if (($categoryName != "all")) {
                        if (isset($page_no_cat)) {
                ?>
                        <script>
                            $(document).ready(function() {
                                $("#col-all-items").hide();
                                $(".categories ul li:first").removeClass('active');
                            });
                        </script>
                    <?php
                        }
                        if ($dateFormatfromAPi == "dd/MM/yyyy") {
                            $dateFormat = "j/m/Y";
                        } else if ($dateFormatfromAPi == "MM/dd/yyyy") {
                            $dateFormat = "m/j/Y";
                        } else if ($dateFormatfromAPi == "MM-dd-yyyy") {
                            $dateFormat = "m-j-Y";
                        }
                        $curlcatwise = wp_remote_get('https://viberent-api.azurewebsites.net/api/Item/item-list?&companyid=' . $companyID . '&pageSize=10&pageNumber=' . $page_no_cat . '&subcategory=' . $_GET['category'], $api_args);

                        if (is_wp_error($curlcatwise) || wp_remote_retrieve_response_code($curlcatwise) != 200) {
                            return false;
                        }
                        $response4 = wp_remote_retrieve_body($curlcatwise);
                        $resp_body = json_decode($response4, 1);
                        if (isset($resp_body)) {
                    ?>
                        <div id="col-catwise-items" class="col-catwise-items">
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
                                    echo esc_html($show_from_date . " - " . $show_to_date) . "<br/>";
                                    ?>
                                </span> </h5>
                            <?php
                            foreach ($resp_body as $retrieved_datas) {
                                $curlavail = wp_remote_get('https://viberent-api.azurewebsites.net/api/Item/item-availability?itemGUID=' . $retrieved_datas["itemGUID"] . '&companyid=' . $companyID . '&fromDate=' . $my_from_date . '&todate=' . $my_to_date . '&PeriodTypeId=27&locationID=0', $api_args);
                                if (is_wp_error($curlavail) || wp_remote_retrieve_response_code($curlavail) != 200) {
                                    return false;
                                }
                                $responseavail = wp_remote_retrieve_body($curlavail);
                                $respavail = json_decode($responseavail, 1);
                            ?>
                                <div class="item-category-box ng-star-inserted p-3 p-sm-3 px-xl-5" id="catwise-item-box">
                                    <form class="m-0" method="post" action="<?php echo site_url(); ?>/<?php echo $mypagename; ?>/?category=<?php echo $query['category']; ?>&pageno=<?php echo $query['pageno']; ?>&action=add&GUID=<?php echo $retrieved_datas['itemGUID']; ?>&rental_period=<?php echo $rentalPeriod; ?>">
                                        <div class=" inner" id="item-on-category-row-2058-0">
                                            <div class="item-display">
                                                <img src=<?php
                                                            if (empty($retrieved_datas["images"])) {
                                                                echo "https://viberent.blob.core.windows.net/attachement/no_image.png";
                                                            } else {
                                                                $count = 0;
                                                                foreach ($retrieved_datas["images"] as $image) {
                                                                    if ($count == 0) {
                                                                        echo $image['blobUrl'];
                                                                    }
                                                                    $count++;
                                                                }
                                                            }
                                                            ?>>
                                            </div>
                                            <div class="item-actions">
                                                <div class="item-details">
                                                    <h4 class="m-0 p-0"><span class="field-Name"><?php echo esc_html($retrieved_datas["itemName"]); ?></span></h4>
                                                </div>
                                                <div class="ng-star-inserted item-price">
                                                    <b>
                                                        <?php
                                                        if (isset($_POST["my-dates"])) {
                                                            $is_present = 0;
                                                            $i = 1;
                                                            foreach ($retrieved_datas["rentalRates"] as $rentalRate) {
                                                                if ($rentalRate['rentalratesName'] == $_POST["period"]) {
                                                                    echo esc_html($currencysymbol);
                                                                    echo esc_html($rentalRate['rentalratesvalue']);
                                                                    echo " : ";
                                                                    echo esc_html($_POST["period"]);
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
                                                            foreach ($retrieved_datas["rentalRates"] as $rentalRate) {
                                                                if ($rentalRate['rentalratesName'] == $firstRental_period) {
                                                                    echo esc_html($currencysymbol);
                                                                    echo esc_html($rentalRate['rentalratesvalue']);
                                                                    echo " : " . esc_html($firstRental_period);
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
                                                            ?><span class="price-not-available"><?php echo "pricing not available"; ?></span>
                                                                <input type="hidden" name="price" class="rentalratesvalue" value="<?php echo 0; ?>" />
                                                        <?php
                                                            }
                                                        }
                                                        ?>
                                                    </b>
                                                </div>
                                                <div class="item-available">
                                                    <p class="mt-0 mb-0 mb-sm-1 mb-md-2">
                                                        <?php
                                                        echo "Available: <span class='product_available'>" . esc_attr($respavail[0]['available']) . "</span>";
                                                        ?>
                                                    </p>
                                                </div>
                                                <div class="add-to-cart-component ng-star-inserted">
                                                    <div class="add-to-cart-con with-plusminus">
                                                        <div class="buy-items-btn ng-star-inserted">
                                                            <input type="hidden" name="image" value="<?php if (empty($retrieved_datas["images"])) {
                                                                                                            echo "https://viberent.blob.core.windows.net/attachement/no_image.png";
                                                                                                        } else {
                                                                                                            $count = 0;
                                                                                                            foreach ($retrieved_datas["images"] as $image) {
                                                                                                                if ($count == 0) {
                                                                                                                    echo $image['blobUrl'];
                                                                                                                }
                                                                                                                $count++;
                                                                                                            }
                                                                                                        }
                                                                                                        ?>" />
                                                            <input type="hidden" name="productAvailable" value="<?php echo esc_attr($respavail[0]['available']); ?>" />
                                                            <input type="hidden" name="itemCode" value="<?php echo esc_attr($retrieved_datas['itemCode']); ?>" />
                                                            <input type="hidden" name="itemGUID" value="<?php echo esc_attr($retrieved_datas['itemGUID']); ?>" />
                                                            <input type="hidden" name="hireTypeID" value="<?php echo esc_attr($retrieved_datas['hireTypeID']); ?>" />
                                                            <input type="hidden" name="locationID" value="<?php echo esc_attr($retrieved_datas['locationID']); ?>" />
                                                            <input type="hidden" name="itemName" value="<?php echo esc_attr($retrieved_datas['itemName']); ?>" />
                                                            <input type="hidden" name="categoryName" value="<?php echo esc_attr($retrieved_datas['categoryName']); ?>" />
                                                            <input type="hidden" name="rentalratesName" value="<?php echo esc_attr($rentalPeriod); ?>" />
                                                            <input type="hidden" name="start-date" value="<?php echo esc_attr($start_from_date); ?>" />
                                                            <input type="hidden" name="end-date" value="<?php echo esc_attr($end_to_date); ?>" />
                                                            <input type="hidden" name="sessionID" value="<?php echo trim($retrieved_datas['itemGUID'] . $rentalPeriod); ?>" />
                                                            <input type="number" class="product-quantity" name="quantity" min="1" value="1" size="2" /><input type="submit" name="add_to_cart" value="Add to Cart" class="btnAddAction" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="product-quantity-message"><?php
                                                                                        if (isset($_SESSION["cart_item"])) {
                                                                                            foreach ($_SESSION["cart_item"] as $item) {
                                                                                                if ($item["productAvailble"] >= $item["quantity"]) {
                                                                                                    $productAvailable = $item["quantity"];
                                                                                                } else {
                                                                                                    $productAvailable = $item["productAvailble"];
                                                                                                }
                                                                                                if ($retrieved_datas['itemGUID'] == $item['GUID'] && $rentalPeriod == $item['rental_period']) {
                                                                                                    echo "<b>" . esc_attr($productAvailable) . " item(s) added to cart</b>";
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                        ?></div>
                                                <div class="item-summary pt-2">
                                                    <p class="minimize m-0"><?php echo esc_attr($retrieved_datas["itemDescription"]); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            <?php
                            } ?>
                            <?php
                            $are_pages_cat = '';
                            $not_final_pages_cat = '';
                            if (isset($retrieved_datas["totalRows"])) {
                                $are_pages_cat = $retrieved_datas["totalRows"] % 10;
                                $not_final_pages_cat = intval($retrieved_datas["totalRows"] / 10);
                            }
                            if ($are_pages_cat == 0) {
                                $total_pages_cat = (int)$not_final_pages_cat;
                            } else {
                                $total_pages_cat = (int)$not_final_pages_cat + 1;
                            }
                            ?>
                            <div class="pagination">
                                <ul>
                                    <?php
                                    $query = $_GET;
                                    $pagLink_category = "";
                                    if ($total_pages_cat > 1) {
                                        if ($page_no_cat >= 2) {
                                            echo "<li class='prev'><span><a href='" . site_url() . "/" . $mypagename . "/?category=" . $query['category'] . "&pageno=" . ($page_no_cat - 1) . "'>Prev</a></span></li>";
                                        }
                                        for ($x = 1; $x <= $page_no_cat; $x++) {
                                            if ($x == $page_no_cat) {
                                                $pagLink_category .= "<li class='active'><span><a href='" . site_url() . "/" . $mypagename . "/?category=" . $query['category'] . "&pageno="
                                                    . $x . "'>" . $x . " </a></span></li>";
                                            } else {
                                                $pagLink_category .= "<li><span><a href='" . site_url() . "/" . $mypagename . "/?category=" . $query['category'] . "&pageno=" . $x . "'>   
                                            " . $x . " </a></span></li>";
                                            }
                                        }
                                        if ($page_no_cat < $total_pages_cat) {
                                            $pagLink_category .= '<li class="disabled"><span>...</span></li>';
                                            $pagLink_category .= "<li><span><a href='" . site_url() . "/" . $mypagename . "/?category=" . $query['category'] . "&pageno=" . ($page_no_cat + 1) . "'>Next</a></span></li>";
                                        }
                                        echo $pagLink_category;
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    <?php }
                    } else {
                    ?>
                    <div id="col-all-items" class="col-all-items">
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
                                echo sanitize_text_field($show_from_date) . " - " . sanitize_text_field($show_to_date) . "<br/>";
                                ?>
                            </span> </h5>
                        <?php
                        foreach ($resp3 as $retrieved_data) {
                            $curlavail = wp_remote_get('https://viberent-api.azurewebsites.net/api/Item/item-availability?itemGUID=' . $retrieved_data["itemGUID"] . '&companyid=' . $companyID . '&fromDate=' . $my_from_date . '&todate=' . $my_to_date . '&PeriodTypeId=27&locationID=0', $api_args);
                            if (is_wp_error($curlavail) || wp_remote_retrieve_response_code($curlavail) != 200) {
                                return false;
                            }
                            $responseavail = wp_remote_retrieve_body($curlavail);
                            $respavail = json_decode($responseavail, 1);
                        ?>
                            <div class="item-category-box ng-star-inserted p-3 p-sm-3 px-xl-5" id="all-item-box">
                                <form class="m-0 1" method="post" action="<?php echo site_url();
                                                                            if (isset($query['pageno'])) { ?>/<?php echo $mypagename; ?>/?pageno=<?php echo $query['pageno'];
                                                                            } else { ?>/<?php echo $mypagename; ?>/?pageno=1<?php } ?>&action=add&GUID=<?php echo $retrieved_data['itemGUID']; ?>&rental_period=<?php echo $rentalPeriod; ?>">
                                    <div class="inner" id="item-on-category-row-2058-0" data-itemid="56971">
                                        <div class="item-display">
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
                                        <div class="item-actions">
                                            <div class="item-details">
                                                <h4 class="m-0 p-0"><span class="field-Name"><?php echo esc_html($retrieved_data["itemName"]); ?></span></h4>
                                            </div>
                                            <div class="ng-star-inserted item-price">
                                                <b>
                                                    <?php
                                                    if (isset($_POST["period"])) {
                                                        $rentalPeriod = sanitize_text_field($_POST["period"]);
                                                    } else {
                                                        $rentalPeriod = sanitize_text_field($firstRental_period);
                                                    }
                                                    if (isset($_POST["my-dates"])) {
                                                        $is_present = 0;
                                                        $i = 1;
                                                        foreach ($retrieved_data["rentalRates"] as $rentalRate) {
                                                            if ($rentalRate['rentalratesName'] == $rentalPeriod) {
                                                                echo esc_html($currencysymbol);
                                                                echo esc_html($rentalRate['rentalratesvalue']);
                                                                echo " : ";
                                                                echo esc_html($rentalPeriod);
                                                                $is_present = 1;
                                                                if ($i == 1) {
                                                    ?>
                                                                    <input type="hidden" name="price" class="rentalratesvalue" value="<?php echo esc_attr($rentalRate['rentalratesvalue']); ?>" />
                                                            <?php
                                                                }
                                                                $i++;
                                                            }
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
                                                        }
                                                        if ($is_daily !== 1) {

                                                            ?><span class="price-not-available"><?php echo "pricing not available"; ?></span>
                                                            <input type="hidden" name="price" class="rentalratesvalue" value="<?php echo 0; ?>" />
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </b>
                                            </div>
                                            <div class="item-available">
                                                <p class="mt-0 mb-0 mb-sm-1 mb-md-2">
                                                    <?php
                                                    echo "Available: <span class='product_available'>" . esc_attr($respavail[0]['available']) . "</span>";
                                                    ?>
                                                </p>
                                            </div>
                                            <div class="add-to-cart-component ng-star-inserted">
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
                                                <input type="hidden" name="productAvailable" value="<?php echo esc_attr($respavail[0]['available']); ?>" />
                                                <input type="hidden" name="itemCode" value="<?php echo esc_attr($retrieved_data['itemCode']); ?>" />
                                                <input type="hidden" name="itemGUID" value="<?php echo esc_attr($retrieved_data['itemGUID']); ?>" />
                                                <input type="hidden" name="hireTypeID" value="<?php echo esc_attr($retrieved_data['hireTypeID']); ?>" />
                                                <input type="hidden" name="locationID" value="<?php echo esc_attr($retrieved_data['locationID']); ?>" />
                                                <input type="hidden" name="itemName" value="<?php echo esc_attr($retrieved_data['itemName']); ?>" />
                                                <input type="hidden" name="categoryName" value="<?php echo esc_attr($retrieved_data['categoryName']); ?>" />
                                                <input type="hidden" name="rentalratesName" value="<?php echo esc_attr($rentalPeriod); ?>" />
                                                <input type="hidden" name="start-date" value="<?php echo esc_attr($start_from_date); ?>" />
                                                <input type="hidden" name="end-date" value="<?php echo esc_attr($end_to_date); ?>" />
                                                <input type="hidden" name="sessionID" value="<?php echo trim($retrieved_data['itemGUID'] . $rentalPeriod); ?>" />
                                                <input type="number" class="product-quantity" name="quantity" min="1" value="1" size="2" /><input type="submit" name="add_to_cart" value="Add to Cart" class="btnAddAction" />
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
                                                        if ($retrieved_data['itemGUID'] == $item['GUID'] && $rentalPeriod == $item['rental_period']) {
                                                            echo "<b>" . esc_attr($productAvailable) . " item(s) added to cart</b>";
                                                        }
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <div class="item-summary pt-2">
                                                <p class="minimize m-0"><?php echo esc_attr($retrieved_data["itemDescription"]); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php
                        } ?>
                        <?php
                        $are_pages = $retrieved_data["totalRows"] % 10;
                        $not_final_pages = intval($retrieved_data["totalRows"] / 10);
                        if ($are_pages == 0) {
                            $total_pages = $not_final_pages;
                        } else {
                            $total_pages = $not_final_pages + 1;
                        }
                        ?>
                        <div class="pagination">
                            <ul>
                                <?php
                                $query = $_GET;
                                $pagLink = "";
                                if ($total_pages > 1) {
                                    if ($page_nos >= 2) {
                                        echo "<li class='prev'><span><a href='" . site_url() . "/" . $mypagename . "/?pageno=" . ($page_nos - 1) . "'>Prev</a></span></li>";
                                    }
                                    for ($x = 1; $x <= $page_nos; $x++) {
                                        $query['pageno'] =  $x;
                                        $query_result = http_build_query($query);
                                        if ($x == $page_nos) {
                                            $pagLink .= "<li class='active'><span><a href='" . site_url() . "/" . $mypagename . "/?pageno="
                                                . $x . "'>" . $x . " </a></span></li>";
                                        } else {
                                            $pagLink .= "<li><span><a href='" . site_url() . "/" . $mypagename . "/?pageno=" . $x . "'>   
                                                    " . $x . " </a></span></li>";
                                        }
                                    }
                                    if ($page_nos < $total_pages) {
                                        $pagLink .= '<li class="disabled"><span>...</span></li>';
                                        $pagLink .= "<li><span><a href='" . site_url() . "/" . $mypagename . "/?pageno=" . ($page_nos + 1) . "'>Next</a></span></li>";
                                    }
                                    echo $pagLink;
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
            <?php
                    }
                }
            ?>
            </div>
        </div>
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
