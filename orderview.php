<?php
/**
 * Created by PhpStorm.
 * User: mattias.nording
 * Date: 2016-09-28
 * Time: 15:17
 */
include('db.php');
include('functions.php');
global $dblink;
verifyLoggedin();
if(isset($_POST["activate"]))
{

}
if(isset($_POST["credit"]))
{

}
$id = $_GET["id"];
$getorder = mysqli_query($dblink,"SELECT * FROM `order` WHERE id = $id");
$order = mysqli_fetch_assoc($getorder);
$shippingID = $order["shipping"];
$billingID = $order["billing"];
$getshipping = mysqli_query($dblink,"SELECT * FROM `address` WHERE id = $shippingID");
$shipping = mysqli_fetch_assoc($getshipping);
$getbilling = mysqli_query($dblink,"SELECT * FROM `address` WHERE id = $billingID");
$billing = mysqli_fetch_assoc($getbilling);
$getcart = mysqli_query($dblink,"SELECT *  FROM `orderitem` WHERE `orderid` = $id");
?>
<html class="" lang="">
<head>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Klarna Invoice</title>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700'
          rel='stylesheet'
          type='text/css'>
    <link rel="stylesheet" href="styles/foundation.min.css">
    <link rel="stylesheet" href="styles/main.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.js"></script>
</head>
<body>
<?php include('menu.php'); ?>
<div class="container">
    <div class="row">
        <div class="small-6 large-3 columns">
            <?php echo gettext("Status: "); echo $order["status"] ?>
        </div>
        <div class="small-6 large-3 columns">
            <?php echo gettext("Date of order: "); echo $order["datetime"] ?>
        </div>
        <div class="small-6 large-3 columns">
            <div class="address-preview">
                <?php echo gettext("Shipping address: "); ?>
                <?php if($shipping["company"] != null)
                { ?>
                    <span><?php  echo $shipping["company"];?></span>
                <?php  }?>
                <span><?php  echo $shipping["firstname"];?></span>
                <span><?php  echo $shipping["lastname"];?></span>
                <span><?php  echo $shipping["street"];?></span>
                <span><?php echo $shipping["postal"];?>
                    <?php  echo $shipping["city"];?></span>
            </div>
        </div>
        <div class="small-6 large-3 columns">
            <div class="address-preview">
                <?php echo gettext("Billing address: "); ?>
                <?php if($billing["company"] != null)
                { ?>
                    <span><?php  echo $billing["company"];?></span>
                <?php  }?>
                <span><?php  echo $billing["firstname"];?></span>
                <span><?php  echo $billing["lastname"];?></span>
                <span><?php  echo $billing["street"];?></span>
                <span><?php echo $billing["postal"];?>
                    <?php  echo $billing["city"];?></span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="small-12 columns">
            <h1><?php echo gettext("Cart items");?></h1>
        </div>
        </div>
        <div class="row">
            <div class="medium-2 small-3 columns"><?php echo  gettext("SKU") ?></div>
            <div class="medium-4 small-7 columns"><?php echo  gettext("Name") ?></div>
            <div class="medium-1 small-2 columns"><?php echo  gettext("Qt") ?></div>
            <div class="medium-1 small-3 columns hide-for-small-only"><?php echo  gettext("Ex vat") ?></div>
            <div class="medium-1 small-3 columns hide-for-small-only"><?php echo  gettext("Vat") ?></div>
            <div class="medium-1 small-2 columns hide-for-small-only"><?php echo  gettext("Tot Ex") ?></div>
            <div class="medium-1 small-2 columns hide-for-small-only"><?php echo  gettext("Tot Inc") ?></div>
        </div>
        <?php
        while($item = mysqli_fetch_assoc($getcart))
        {
            ?>
            <div class="row">
                <div class="medium-2 small-3 columns"><?php echo  $item["artno"] ?></div>
                <div class="medium-4 small-7 columns"><?php echo $item["name"] ?></div>
                <div class="medium-1 small-2 columns"><?php echo $item["quantity"]; echo gettext("pc")?></div>
                <div class="medium-1 small-3 columns hide-for-small-only"><?php echo $item["exvat"] ?></div>
                <div class="medium-1 small-3 columns hide-for-small-only"><?php echo $item["vat"] ?></div>
                <div class="medium-1 small-2 columns hide-for-small-only"><?php echo ($item["exvat"] * $item["quantity"]) ?></div>
                <div class="medium-1 small-2 columns hide-for-small-only"><?php echo ($item["incvat"] * $item["quantity"]) ?></div>
            </div>
            <?
        }?>
    <div class="row">
        <div class="small-6 columns">
            <?php if($order["status"] =="Shipped")
            {
                echo '<form method="post" action="api/'.$order["storeid"].'/credit">';
                echo '<input type="hidden" value="'.$order["invoice"].'" name="id">';
                echo '<button type="submit" name="credit" class="button alert">'.gettext("Credit full order").'</button>';
                echo '</form>';

            }
            else if($order["status"] =="Reserved")
            {
                echo '<form method="post" action="api/'.$order["storeid"].'/activate">';
                echo '<input type="hidden" value="'.$order["reservation"].'" name="id">';
                echo '<button type="submit" name="activate" class="button">'.gettext("Activate full order").'</button>';
                echo '</form>';

            }?>
        </div>
    </div>
    </div>
<script>
    (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
        function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
        e=o.createElement(i);r=o.getElementsByTagName(i)[0];
        e.src='https://www.google-analytics.com/analytics.js';
        r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
    ga('create','UA-XXXXX-X');ga('send','pageview');
</script>

<!-- build:js scripts/vendor.js -->
<!-- bower:js -->
<!-- endbower -->
<!-- endbuild -->
<script   src="https://code.jquery.com/jquery-1.12.4.min.js"   integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="   crossorigin="anonymous"></script>
<!-- build:js scripts/main.js -->
<script src="scripts/main.js"></script>
<!-- endbuild -->
</body>
</html>
