<?php

/*
Template name: Viberent thank-shopping
 */
?>
<!DOCTYPE html>
<html>
<head>
<style>


body {
    text-align: center;
    background-color: bisque;
    padding: 3rem 7rem;

}

#thanku {
    background-color: white;
    padding: 1rem 1rem 3rem 1rem;

}

</style>
</head>







<body>

<div id="thanku">

<h1><i>Thank you for shopping with us!</i></h1>
<h4 style="color: green;">Your order was successfully processed</h4>



<?php

$result = $wpdb->get_results("SELECT QuoteNumber from wp_quote_number WHERE `id` IS NOT NULL");

if(isset($result[0]->QuoteNumber)) {



?>




<p>Please save your Quote Number for future references</p>





<?php

echo "<h3><b>Your Quote Number: </b></h3><h3 style=font-weight:normal;>";
echo $result[0]->QuoteNumber. "</h3";


    $delete = $wpdb->query("TRUNCATE TABLE `wp_quote_number`");


session_start();
$query = $_GET;
$query_result = http_build_query($query);

    unset($_SESSION["cart_item"]);
    $wpdb->query("TRUNCATE TABLE `wp_tbl_product`");

}

?>

</div>



</body>
</html>
