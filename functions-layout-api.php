<?php
function get_page_id_by_title($title)
{
    $page = get_page_by_title($title);
    return $page->ID;
}
function create_page($title_of_the_page, $parent_id = NULL)
{
    $objPage = get_page_by_title($title_of_the_page, 'OBJECT', 'page');
    if (!empty($objPage)) {
        return $objPage->ID;
    }
    global $wpdb;

    if (null === $wpdb->get_row("SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'new-page-slug'", 'ARRAY_A')) {
        $page =  array(
            'comment_status' => 'close',
            'ping_status'    => 'close',
            'post_author'    => 1,
            'post_title'     => ucwords($title_of_the_page),
            'post_name'      => strtolower(str_replace(' ', '-', trim($title_of_the_page))),
            'post_status'    => 'publish',
            'post_content'   => '',
            'post_type'      => 'page',
            'post_parent'    =>  $parent_id
        );
        $page_id = wp_insert_post($page);
        return $page_id;
    }
}
function viberent_init()
{

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $table_name = $wpdb->prefix . 'viberent_clients_company_info';
    $sql = "CREATE TABLE $table_name (
    companyID INTEGER NOT NULL,
    companyName TEXT NOT NULL,
    useName TEXT NOT NULL,
    isAdmin BOOLEAN NOT NULL,
    isMultiLocation BOOLEAN NOT NULL,
    dateFormat TEXT NOT NULL,
    currencysymbol TEXT NOT NULL,
    logo MEDIUMTEXT NOT NULL,
    PRIMARY KEY (companyID)
  ) $charset_collate;";
    dbDelta($sql);

    $table_layout = $wpdb->prefix . 'viberent_layout';
    $sql = "CREATE TABLE $table_layout (
    companyID INTEGER NOT NULL,
    useName TEXT NOT NULL,
    selected_layout TEXT NOT NULL,
    PRIMARY KEY (companyID)
  ) $charset_collate;";
    dbDelta($sql);

    $table_pagename = $wpdb->prefix . 'viberent_pagename';
    $sql = "CREATE TABLE $table_pagename (
    companyID INTEGER NOT NULL,
    useName TEXT NOT NULL,
    pagename TEXT NOT NULL,
    PRIMARY KEY (companyID)
  ) $charset_collate;";
    dbDelta($sql);

    $table_addTocart = $wpdb->prefix . 'tbl_product';
    $sql = "CREATE TABLE $table_addTocart (
    id int(11) NOT NULL AUTO_INCREMENT,
    sessionID varchar(255) NOT NULL,
    product_name varchar(255) NOT NULL,
    category_name varchar(255) NOT NULL,
    quantity int(10) NOT NULL,
    product_image varchar(255) NOT NULL,
    code varchar(255) NOT NULL,
    GUID varchar(255) NOT NULL,
    hireTypeID varchar(255) NOT NULL,
    locationID varchar(255) NOT NULL,
    price double(10,2) NOT NULL,
    rental_period varchar(255) NOT NULL,
    periodTypeId int(10) NOT NULL,
    startDate DATE NOT NULL DEFAULT '0000-00-00',
    endDate DATE NOT NULL DEFAULT '0000-00-00',
    PRIMARY KEY (id)
    ) $charset_collate;";
    dbDelta($sql);

    $QuoteNumber = $wpdb->prefix . 'quote_number';
    $sql = "CREATE TABLE $QuoteNumber (
    id int(11) NOT NULL AUTO_INCREMENT,
    QuoteNumber TEXT NOT NULL,
    PRIMARY KEY (id)
  ) $charset_collate;";
    dbDelta($sql);

    $ViberentPostArray = $wpdb->prefix . 'viberent_post_array';
    $sql = "CREATE TABLE $ViberentPostArray (
    id int(11) NOT NULL AUTO_INCREMENT,

    custoname varchar(255) NOT NULL,
    companyID varchar(255) NOT NULL,
    billing_address varchar(512) NOT NULL,
    city_bill varchar(255) NOT NULL,
    state_bill varchar(255) NOT NULL,
    postalCode_bill varchar(255) NOT NULL,
    country_bill varchar(255) NOT NULL,
    email_bill varchar(255) NOT NULL,
    phone_bill varchar(255) NOT NULL,
    shipping_address varchar(512),
    city_ship varchar(255),
    state_ship varchar(255),
    postalCode_ship varchar(255),
    country_ship varchar(255),
    email_ship varchar(255),
    phone_ship varchar(255),
    PRIMARY KEY (id)
  ) $charset_collate;";
    dbDelta($sql);


?>
    <div id="full_page_viberent_plugin">
        <div>
            <form method="post" id="viberent_plugin_login_form">

                <br>
                <div style="display: flex; justify-content: center;">
                    <img id="viberent_logo" src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/VibeRent-LOGO.png'; ?>">
                </div><br>

                <div id="LoginForm" class="container" style="background-color: white; width: 40%; margin: auto; border: 1px solid rgba(147, 184, 189,0.8); box-shadow: 0pt 2px 5px rgb(105 108 109 / 70%), 0px 0px 8px 5px rgb(208 223 226 / 40%) inset;">

                    <h3>LOGIN</h3>

                    <label for="password"><b>Api Key</b></label><br>
                    <input type="password" placeholder="Enter Api Key" name="password" id="password" required><br><br>

                    <button type="submit" id="login" name="submit">LOGIN</button>
                    <br><br>

                </div>
            </form>
        </div>

        <?php

        $no_of_rows = $wpdb->get_results("SELECT companyID from wp_viberent_clients_company_info WHERE `companyID` IS NOT NULL");

        if (count($no_of_rows) != 0) {
        ?>
            <script language="javascript">
                document.getElementById("LoginForm").style.display = "none";
                setTimeout(function() {
                    document.getElementById("chosen_message").style.display = "block";
                }, 1500);
            </script>
            <?php

            $result = $wpdb->get_results("SELECT * from $table_name");
            $chooseLayout = $wpdb->get_results("SELECT * from wp_viberent_layout");
            if (isset($chooseLayout[0])) {
                $chooseLayout = $chooseLayout[0]->selected_layout;
            }

            $chooseLayout = isset($_POST['chosen_layout']) ? (sanitize_text_field($_POST['chosen_layout'])) : sanitize_text_field($chooseLayout);

            echo "<b>Company Name:</b> ";
            echo sanitize_text_field($result[0]->companyName);
            echo "</br>";

            ?>

            <form method="post" id="layout_options_form" name="layout_options_form">
                <p id="plz_choos_layout">Please choose the layout for your shop:</p><br>

                <div id="layout_options">
                    <div>
                        <input type="radio" id="radio_item_based" name="chosen_layout" value="item-based" <?php if ($chooseLayout == 'item-based') {
                                                                                                                echo "checked";
                                                                                                            } ?>>
                        <label for="radio_item_based">
                            Item-based listing<br>
                            <img id="layout_option_img" src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/item.png'; ?>">

                        </label><br><br>
                    </div>
                    <div>
                        <input type="radio" id="radio_category_based" name="chosen_layout" value="category-based" <?php if ($chooseLayout == 'category-based') {
                                                                                                                        echo "checked";
                                                                                                                    } ?>>
                        <label for="radio_category_based">
                            Category-based listing<br>
                            <img id="layout_option_img" src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/category.png'; ?>">
                        </label><br><br>
                    </div>
                </div>

                <label for="pagename"></label><br>

                <?php
                $resuli = $wpdb->get_results("SELECT * from wp_viberent_pagename");

                if (empty($resuli)) {
                ?>

                    <input type="text" id="pagename" name="pagename" placeholder="Enter the page name for your shop here" required><br>


                <?php

                }

                ?>

                <button type="submit" id="radio_layout_submit" name="radio_layout_submit">Save</button>

            </form>


            <form method="post">
                <button class="btn_logout" name="logout">
                    LOGOUT
                </button>
            </form>

        <?php

            $resulu = $wpdb->get_results("SELECT * from wp_viberent_layout");
            $resuli = $wpdb->get_results("SELECT * from wp_viberent_pagename");

            if (!empty($resulu)) {
                if (!empty($resuli)) {

                    if ($resulu[0]->selected_layout == "item-based") {
                        $page_id = create_page($resuli[0]->pagename);
                        update_post_meta($page_id, '_wp_page_template', 'templates/item_based.php');

                        $page_id_one = create_page('My cart');
                        update_post_meta($page_id_one, '_wp_page_template', 'templates/my_cart.php');

                        $page_id_two = create_page('Place my order');
                        update_post_meta($page_id_two, '_wp_page_template', 'templates/place_my_order.php');

                        $page_id_three = create_page('Thank shopping');
                        update_post_meta($page_id_three, '_wp_page_template', 'templates/thank_shopping.php');
                    }

                    if ($resulu[0]->selected_layout == "category-based") {
                        $page_id = create_page($resuli[0]->pagename);
                        update_post_meta($page_id, '_wp_page_template', 'templates/category_based.php');

                        $page_id_one = create_page('My cart');
                        update_post_meta($page_id_one, '_wp_page_template', 'templates/my_cart.php');

                        $page_id_two = create_page('Place my order');
                        update_post_meta($page_id_two, '_wp_page_template', 'templates/place_my_order.php');

                        $page_id_three = create_page('Thank shopping');
                        update_post_meta($page_id_three, '_wp_page_template', 'templates/thank_shopping.php');
                    }
                }
            }

        }



        if (isset($_POST["logout"])) {
            $resuli = $wpdb->get_results("SELECT * from wp_viberent_pagename");
            $page_id = get_page_id_by_title($resuli[0]->pagename, $post_type = 'page');
            $page_id_one = get_page_id_by_title('My cart', $post_type = 'page');
            $page_id_two = get_page_id_by_title('Place my order', $post_type = 'page');
            $page_id_three = get_page_id_by_title('Thank shopping', $post_type = 'page');

            $wpdb->delete('wp_posts', array('ID' => $page_id));
            $wpdb->delete('wp_posts', array('ID' => $page_id_one));
            $wpdb->delete('wp_posts', array('ID' => $page_id_two));
            $wpdb->delete('wp_posts', array('ID' => $page_id_three));
            $delete = $wpdb->query("TRUNCATE TABLE `wp_viberent_clients_company_info`");
        ?>
            <script>
              window.location.reload();
            </script>
            <?php
        }


        if (isset($_POST["submit"])) {
            $password = $_POST["password"];
            $response = wp_remote_get('https://viberent-api.azurewebsites.net/api/Customer/login-details?ApiSecretKey=' . $password);
            $body     = wp_remote_retrieve_body($response);
            $resp = json_decode($body, 1);

            if (isset($resp["companyID"])) {
                $num_of_rows = $wpdb->get_results("SELECT companyID from wp_viberent_clients_company_info WHERE `companyID` IS NOT NULL");
                $chooseLayout = $wpdb->get_results("SELECT * from wp_viberent_layout");
                if (count($num_of_rows) == 0) {
                    $wpdb->insert('wp_viberent_clients_company_info', $resp);
            ?>

                    <script language="javascript">
                        document.getElementById("LoginForm").style.display = "none";
                        setTimeout(function() {
                            document.getElementById("chosen_message").style.display = "block";
                        }, 1500);
                    </script>

                    <?php
                    $result = $wpdb->get_results("SELECT * from $table_name");

                    if (isset($chooseLayout[0])) {
                        $chooseLayout = $chooseLayout[0]->selected_layout;
                    }

                    $chooseLayout = isset($_POST['chosen_layout']) ? ($_POST['chosen_layout']) : $chooseLayout;

                    echo "</br><b>Company Name:</b> ";
                    echo sanitize_text_field($result[0]->companyName);
                    echo "</br>";

                    ?>

                    <form method="post" id="layout_options_form">
                        <p>Please choose the layout for your shop:</p><br>
                        <div id="layout_options">
                            <div>
                                <input type="radio" id="radio_item_based" name="chosen_layout" value="item-based" <?php if ($chooseLayout == 'item-based') {
                                                                                                                        echo "checked";
                                                                                                                    } ?>>
                                <label for="radio_item_based">
                                    Item-based listing<br>
                                    <img id="layout_option_img" src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/item.png'; ?>">
                                </label><br><br>
                            </div>
                            <div>
                                <input type="radio" id="radio_category_based" name="chosen_layout" value="category-based" <?php if ($chooseLayout == 'category-based') {
                                                                                                                                echo "checked";
                                                                                                                            } ?>>
                                <label for="radio_category_based">
                                    Category-based listing<br>
                                    <img id="layout_option_img" src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/category.png'; ?>">
                                </label><br><br>
                            </div>
                        </div>

                        <button type="submit" id="radio_layout_submit" name="radio_layout_submit">Save</button>

                    </form>


                    <form method="post">
                        <button class="btn_logout" name="logout">
                            LOGOUT
                        </button>
                    </form>

            <?php
                }
                $resulu = $wpdb->get_results("SELECT * from wp_viberent_layout");
                $resuli = $wpdb->get_results("SELECT * from wp_viberent_pagename");

                if (!empty($resulu)) {
                    if (!empty($resuli)) {

                        if ($resulu[0]->selected_layout == "item-based") {
                            $page_id = create_page($resuli[0]->pagename);
                            update_post_meta($page_id, '_wp_page_template', 'templates/item_based.php');

                            $page_id_one = create_page('My cart');
                            update_post_meta($page_id_one, '_wp_page_template', 'templates/my_cart.php');

                            $page_id_two = create_page('Place my order');
                            update_post_meta($page_id_two, '_wp_page_template', 'templates/place_my_order.php');

                            $page_id_three = create_page('Thank shopping');
                            update_post_meta($page_id_three, '_wp_page_template', 'templates/thank_shopping.php');
                        }

                        if ($resulu[0]->selected_layout == "category-based") {
                            $page_id = create_page($resuli[0]->pagename);
                            update_post_meta($page_id, '_wp_page_template', 'templates/category_based.php');

                            $page_id_one = create_page('My cart');
                            update_post_meta($page_id_one, '_wp_page_template', 'templates/my_cart.php');

                            $page_id_two = create_page('Place my order');
                            update_post_meta($page_id_two, '_wp_page_template', 'templates/place_my_order.php');

                            $page_id_three = create_page('Thank shopping');
                            update_post_meta($page_id_three, '_wp_page_template', 'templates/thank_shopping.php');
                        }
                    }
                }
            } else {
                echo '<script>alert("The Api Key provided is incorrect")</script>';
            }
        }


      


        if (isset($_POST["radio_layout_submit"])) {

            $resul = $wpdb->get_results("SELECT * from $table_name");
            $resulu = $wpdb->get_results("SELECT * from wp_viberent_layout");
            
            $item_layout = array(
                "companyID" => $resul[0]->companyID,
                "useName" => $resul[0]->useName,
                "selected_layout" => $_POST["chosen_layout"]
            );

            if (empty($resulu)) {
                $wpdb->insert('wp_viberent_layout', $item_layout);
            } else {
                $wpdb->query("TRUNCATE TABLE `wp_viberent_layout`");
                $wpdb->insert('wp_viberent_layout', $item_layout);
            }


            if (isset($_POST["pagename"])) {
                $mypagearr = array(
                    "companyID" => $resul[0]->companyID,
                    "useName" => $resul[0]->useName,
                    "pagename" => $_POST["pagename"]
                );
                $wpdb->insert('wp_viberent_pagename', $mypagearr);
            }
            ?>
            <script>
                window.location.reload();
            </script>
            <?php
        }

        $rows = $wpdb->get_results("SELECT companyID from wp_viberent_layout WHERE `companyID` IS NOT NULL");
        $my_selected_layout = $wpdb->get_results("SELECT * from wp_viberent_layout WHERE `companyID` IS NOT NULL");
        if (count($rows) != 0) {
            $resuli = $wpdb->get_results("SELECT * from wp_viberent_pagename");
            if (!empty($resuli)) {
                $slug_name = sanitize_title($resuli[0]->pagename);
            ?>
                <div id="chosen_message" style="display:none;">
                    <a href="<?php echo site_url() . "/" . $slug_name; ?>" type="button" target="_blank" id="change_selected_layout" name="change_selected_layout">Go to the <?php echo $my_selected_layout[0]->selected_layout; ?> layout </a>
                </div>
        <?php
            }
        }
        ?>
    </div>
<?php
}
?>